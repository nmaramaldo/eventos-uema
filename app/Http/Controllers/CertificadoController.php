<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCertificadoRequest;
use App\Http\Requests\UpdateCertificadoRequest;
use App\Models\Certificado;
use App\Models\CertificadoModelo;
use App\Models\Inscricao;
use App\Models\Event;
use App\Jobs\GenerateCertificatePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificadoController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificado::with([
            'inscricao.evento',
            'inscricao.user',
            'modelo',
        ]);

        $evento = null;
        
        if ($request->has('evento_id')) {
            $eventoId = $request->get('evento_id');
            $evento = Event::find($eventoId);

            if ($evento) {
                $query->whereHas('inscricao', function ($q) use ($eventoId) {
                    $q->where('evento_id', $eventoId);
                });
            }
        }

        $certificados = $query->orderBy('data_emissao', 'desc')->paginate(15);

        // Passamos a variável $evento para a view saber que estamos num contexto específico
        return view('certificados.index', compact('certificados', 'evento'));
    }

    /**
     * Lista de certificados do usuário logado (participante).
     */
    public function meus()
    {
        $userId = auth()->id();

        $certificados = Certificado::with(['inscricao.evento', 'modelo'])
            ->whereHas('inscricao', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->orderBy('data_emissao', 'desc')
            ->paginate(15);

        return view('certificados.index', compact('certificados'));
    }

    /**
     * Tela de GERAR certificado (admin/master).
     * Se vier ?evento_id=XXX, filtra por esse evento e por inscrições com check-in.
     */
    public function create(Request $request)
    {
        $eventoId = $request->query('evento_id');
        $evento   = null;

        if ($eventoId) {
            $evento = Event::findOrFail($eventoId);

            // somente inscrições daquele evento COM check-in (presente = true)
            $inscricoes = Inscricao::with('user', 'evento')
                ->where('evento_id', $evento->id)
                ->where('presente', true)
                ->orderBy('created_at', 'desc')
                ->get();

            // modelos deste evento OU globais (sem evento_id)
            $modelos = CertificadoModelo::where(function ($query) use ($evento) {
                $query->where('evento_id', $evento->id)
                    ->orWhereNull('evento_id');
            })
                ->where('publicado', true)
                ->with('evento')
                ->orderBy('titulo')
                ->get();
        } else {
            // sem evento específico: tudo
            $inscricoes = Inscricao::with('user', 'evento')
                ->where('presente', true)
                ->orderBy('created_at', 'desc')
                ->get();

            $modelos = CertificadoModelo::where('publicado', true)
                ->with('evento')
                ->orderBy('titulo')
                ->get();
        }

        return view('certificados.create', compact('inscricoes', 'modelos', 'evento'));
    }

    public function store(StoreCertificadoRequest $request)
    {
        $data = $request->validated();

        // inscrição obrigatória
        $inscricao = Inscricao::with('evento')->findOrFail($data['inscricao_id']);

        // regra: só emite certificado se houve check-in no evento
        if (!$inscricao->presente) {
            return back()
                ->with('error', 'Não é possível emitir certificado: o participante ainda não foi credenciado (sem check-in no evento).')
                ->withInput();
        }

        // guarda evento_id para possível redirecionamento
        $eventoId = $inscricao->evento_id ?? null;

        // garante data de emissão
        if (empty($data['data_emissao'])) {
            $data['data_emissao'] = now();
        }

        // se tiver hash_verificacao na tabela, gera um
        if (!isset($data['hash_verificacao']) && \Schema::hasColumn('certificados', 'hash_verificacao')) {
            $data['hash_verificacao'] = (string) Str::uuid();
        }

        // tenta pegar o tipo a partir do modelo (participante / organizador / palestrante)
        if (!isset($data['tipo']) && !empty($data['modelo_id'])) {
            $modelo = CertificadoModelo::find($data['modelo_id']);
            if ($modelo && \Schema::hasColumn('certificados', 'tipo')) {
                $data['tipo'] = $modelo->slug_tipo;
            }
        }

        // ✅ URL OPCIONAL, MAS EVITANDO NULL NA COLUNA NOT NULL
        if (!array_key_exists('path', $data) || $data['path'] === null) {
            $data['path'] = '';
        }

        // evitar duplicado para mesma inscrição + modelo
        $duplicado = Certificado::where('inscricao_id', $inscricao->id)
            ->where('modelo_id', $data['modelo_id'] ?? null)
            ->first();

        if ($duplicado) {
            return redirect()
                ->route('certificados.index')
                ->with('info', 'Já existe um certificado emitido para esta inscrição com esse modelo.');
        }

        $certificado = Certificado::create($data);

        // ✅ Despacha o job para gerar o PDF em background
        GenerateCertificatePdf::dispatch($certificado);

        // Redireciona mantendo contexto se veio de um evento específico
        if ($eventoId && $request->has('evento_id')) {
            return redirect()
                ->route('certificados.create', ['evento_id' => $eventoId])
                ->with('success', 'Certificado solicitado.');
        }

        return redirect()
            ->route('certificados.index')
            ->with('success', 'Certificado solicitado.');
    }

    public function show(Certificado $certificado)
    {
        $certificado->load('inscricao.user', 'inscricao.evento', 'modelo');

        return view('certificados.show', compact('certificado'));
    }

    public function edit(Certificado $certificado)
    {
        $inscricoes = Inscricao::with('user', 'evento')
            ->where('presente', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $modelos = CertificadoModelo::with('evento')
            ->where('publicado', true)
            ->orderBy('titulo')
            ->get();

        return view('certificados.edit', compact('certificado', 'inscricoes', 'modelos'));
    }

    public function update(UpdateCertificadoRequest $request, Certificado $certificado)
    {
        $data = $request->validated();

        if (empty($data['data_emissao'])) {
            $data['data_emissao'] = now();
        }

        // mesma proteção da store
        if (!array_key_exists('path', $data) || $data['path'] === null) {
            $data['path'] = '';
        }

        $certificado->update($data);

        return redirect()
            ->route('certificados.index')
            ->with('success', 'Certificado atualizado com sucesso!');
    }

    public function destroy(Certificado $certificado)
    {
        // Opcional: deletar o arquivo PDF do storage
        if ($certificado->path && Storage::disk('public')->exists($certificado->path)) {
            Storage::disk('public')->delete($certificado->path);
        }

        $certificado->delete();

        return redirect()
            ->route('certificados.index')
            ->with('success', 'Certificado deletado com sucesso!');
    }

    /**
     * Gera certificados para todos os participantes presentes em um evento.
     */
    public function gerarTodosParaPresentes(Request $request, Event $evento)
    {
        $this->authorize('update', $evento);

        $data = $request->validate([
            'modelo_id' => 'required|exists:certificado_modelos,id',
        ]);

        $modelo = CertificadoModelo::findOrFail($data['modelo_id']);

        // Apenas modelos do evento
        if ($modelo->evento_id !== $evento->id && !is_null($modelo->evento_id)) {
            return back()->with('error', 'O modelo de certificado selecionado não pertence a este evento.');
        }

        $inscricoesPresentes = Inscricao::where('evento_id', $evento->id)
            ->where('presente', true)
            ->get();

        $criados = 0;
        $ignorados = 0;

        foreach ($inscricoesPresentes as $inscricao) {
            // Evitar duplicado para mesma inscrição + modelo
            $duplicado = Certificado::where('inscricao_id', $inscricao->id)
                ->where('modelo_id', $modelo->id)
                ->first();

            if ($duplicado) {
                $ignorados++;
                continue;
            }

            $dadosCertificado = [
                'inscricao_id' => $inscricao->id,
                'modelo_id' => $modelo->id,
                'data_emissao' => now(),
                'hash_verificacao' => (string) Str::uuid(),
                'tipo' => $modelo->slug_tipo,
                'path' => '',
            ];

            $certificado = Certificado::create($dadosCertificado);

            GenerateCertificatePdf::dispatch($certificado);
            $criados++;
        }

        $message = "Emissão em massa solicitada: {$criados} certificados estão sendo gerados em fila.";
        if ($ignorados > 0) {
            $message .= " {$ignorados} foram ignorados por já existirem.";
        }

        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * ✅ Download do PDF do certificado
     */
    public function download(Certificado $certificado)
    {
        // 1) Verificação de segurança básica
        if (!auth()->user()->can('manage-users')) {
            $userId = auth()->id();
            if (!$certificado->inscricao || $certificado->inscricao->user_id !== $userId) {
                abort(403, 'Você não tem permissão para baixar este certificado.');
            }
        }

        // 2) Carrega os dados necessários
        $certificado->load([
            'inscricao.user',
            'inscricao.evento',
            'modelo'
        ]);

        // 3) Prepara os dados para a view
        $inscricao = $certificado->inscricao;
        $user = $inscricao?->user ?? $inscricao?->usuario;
        $evento = $inscricao?->evento;
        $modelo = $certificado->modelo;

        // 4) Verifica dados mínimos
        if (!$inscricao || !$user || !$modelo) {
            // Retorna uma página de erro simples
            return response("<h2>Erro: Certificado incompleto</h2>
                       <p>Faltam dados para gerar o certificado.</p>
                       <a href='" . route('certificados.index') . "'>Voltar</a>");
        }

        // 5) Prepara o texto renderizado (se não existir)
        if (empty($certificado->texto_renderizado) && $modelo->corpo_html) {
            // Substitui variáveis no modelo
            $texto = str_replace(
                ['{participante}', '{evento}', '{data}', '{carga_horaria}'],
                [
                    $user->name,
                    $evento->nome ?? 'Evento',
                    $certificado->data_emissao ? $certificado->data_emissao->format('d/m/Y') : now()->format('d/m/Y'),
                    $evento->carga_horaria ?? '--'
                ],
                $modelo->corpo_html
            );
            $certificado->texto_renderizado = $texto;
        }

        // 6) Gera o PDF
        $pdf = Pdf::loadView('certificados.pdf', [
            'certificado' => $certificado,
            'inscricao' => $inscricao,
            'user' => $user,
            'evento' => $evento,
            'modelo' => $modelo,
        ])->setPaper('a4', 'landscape');

        // 7) Nome do arquivo
        $nomeArquivo = 'certificado-' .
            ($user->name ? Str::slug($user->name) : 'user') . '-' .
            ($evento ? Str::slug($evento->nome) : 'evento') . '.pdf';

        // 8) MOSTRA no navegador (sempre funciona)
        return $pdf->stream($nomeArquivo);

}

    /**
     * Verifica um certificado pelo hash.
     */
    public function verificar($hash)
    {
        $certificado = Certificado::where('hash_verificacao', $hash)
            ->with(['inscricao.user', 'inscricao.evento', 'modelo'])
            ->firstOrFail();

        return view('certificados.verificar', compact('certificado'));
    }
}
