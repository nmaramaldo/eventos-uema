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
    public function index()
    {
        $certificados = Certificado::with([
                'inscricao.evento',
                'inscricao.user',
                'modelo',
            ])
            ->orderBy('data_emissao', 'desc')
            ->paginate(15);

        return view('certificados.index', compact('certificados'));
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

        // reaproveitando a mesma view
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

            // modelos publicados desse evento
            $modelos = CertificadoModelo::doEvento($evento->id)
                ->publicados()
                ->with('evento')
                ->orderBy('titulo')
                ->get();
        } else {
            // fallback: tudo
            $inscricoes = Inscricao::with('user', 'evento')
                ->orderBy('created_at', 'desc')
                ->get();

            $modelos = CertificadoModelo::publicados()
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

        // ✅ Refatorado: despacha o job para gerar o PDF em background
        GenerateCertificatePdf::dispatch($certificado);

        return redirect()
            ->route('certificados.index')
            ->with('success', 'Certificado solicitado. O PDF será gerado em breve.');
    }

    public function show(Certificado $certificado)
    {
        $certificado->load('inscricao.user', 'inscricao.evento', 'modelo');

        return view('certificados.show', compact('certificado'));
    }

    public function edit(Certificado $certificado)
    {
        $inscricoes = Inscricao::with('user', 'evento')
            ->orderBy('created_at', 'desc')
            ->get();

        $modelos = CertificadoModelo::with('evento')
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
        if ($modelo->evento_id !== $evento->id) {
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

            // --- Lógica de criação (semelhante à store) ---
            $dadosCertificado = [
                'inscricao_id' => $inscricao->id,
                'modelo_id' => $modelo->id,
                'data_emissao' => now(),
                'hash_verificacao' => (string) Str::uuid(),
                'tipo' => $modelo->slug_tipo,
                'path' => '', // temporário
            ];

            $certificado = Certificado::create($dadosCertificado);

            // ✅ Refatorado: despacha o job para gerar o PDF
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
        // 1) Segurança: se NÃO for admin/master, só pode baixar o próprio certificado
        if (!auth()->user()->can('manage-users')) {
            $userId = auth()->id();
            $inscricao = $certificado->inscricao;

            if (!$inscricao || $inscricao->user_id !== $userId) {
                abort(403, 'Você não tem permissão para baixar este certificado.');
            }
        }

        // 2) Caminho do arquivo no disco "public"
        if (empty($certificado->path)) {
            abort(404, 'O arquivo do certificado ainda não foi gerado. Tente novamente em alguns instantes.');
        }

        if (!Storage::disk('public')->exists($certificado->path)) {
            // Tenta gerar o PDF se ele não existir
            try {
                GenerateCertificatePdf::dispatchSync($certificado);
                // Recarrega o modelo para obter o path atualizado
                $certificado->refresh();
            } catch (\Exception $e) {
                abort(500, 'Não foi possível gerar ou encontrar o arquivo do certificado.');
            }
        }
        
        // 3) Faz o download do arquivo
        $nomeArquivo = 'certificado-' . Str::slug($certificado->inscricao->evento->nome) . '-' . Str::slug($certificado->inscricao->user->name) . '.pdf';
        return Storage::disk('public')->download($certificado->path, $nomeArquivo);
    }
}
