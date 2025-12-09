<?php

namespace App\Http\Controllers;

use App\Models\CertificadoModelo;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificadoModeloController extends Controller
{
    /**
     * Lista de modelos de certificado.
     * Se vier ?evento_id=XXX, filtra por esse evento.
     */
    public function index(Request $request)
    {
        $eventoId = $request->query('evento_id');
        $evento = null;

        // Se veio evento_id, filtra por evento
        if ($eventoId) {
            $evento = Event::find($eventoId);
            $modelos = CertificadoModelo::where(function ($query) use ($eventoId) {
                $query->where('evento_id', $eventoId)
                    ->orWhereNull('evento_id');
            })
                ->with('evento')
                ->orderBy('created_at', 'desc')
                ->paginate(15)
                ->appends(['evento_id' => $eventoId]);
        } else {
            // Mostra todos os modelos
            $modelos = CertificadoModelo::with('evento')
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('certificados.modelos-index', compact('modelos', 'evento'));
    }

    /**
     * Tela de criação de modelo.
     * Se vier ?evento_id=XXX, pre-seleciona esse evento no formulário.
     */
    public function create(Request $request)
    {
        $modelo = new CertificadoModelo();
        $eventos = Event::orderBy('nome')->get();

        // Passar evento_id se vier na requisição
        $eventoId = $request->query('evento_id');
        $eventoSelecionado = null;

        if ($eventoId) {
            $eventoSelecionado = Event::find($eventoId);
            // Pre-selecionar o evento no formulário
            $modelo->evento_id = $eventoId;
        }

        return view('certificados.modelo', compact('modelo', 'eventos', 'eventoSelecionado'));
    }

    /**
     * Salva um novo modelo de certificado.
     * Redireciona mantendo o contexto do evento se houver.
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);

        // Guardar o evento_id para redirecionamento
        $eventoId = $data['evento_id'] ?? null;
        $redirectWithEvent = $request->has('redirect_with_event') || $request->has('source_evento_id');

        // Upload da imagem de fundo, se houver
        if ($request->hasFile('background')) {
            $data['background_path'] = $request->file('background')
                ->store('certificados/backgrounds', 'public');
        }

        $data['publicado'] = $request->boolean('publicado');

        CertificadoModelo::create($data);

        // Redirecionar mantendo contexto se solicitado
        if ($redirectWithEvent || $eventoId) {
            $targetEventoId = $eventoId ?? $request->input('source_evento_id');

            if ($targetEventoId) {
                return redirect()
                    ->route('certificados.create', ['evento_id' => $targetEventoId])
                    ->with('success', 'Modelo de certificado criado com sucesso!');
            }
        }

        return redirect()
            ->route('certificado-modelos.index')
            ->with('success', 'Modelo de certificado criado com sucesso!');
    }

    /**
     * Tela de edição de modelo.
     * Mantém o contexto do evento associado ao modelo.
     */
    public function edit(CertificadoModelo $certificado_modelo)
    {
        $modelo = $certificado_modelo;
        $eventos = Event::orderBy('nome')->get();

        // Passar evento do modelo como selecionado
        $eventoSelecionado = $modelo->evento;

        return view('certificados.modelo', compact('modelo', 'eventos', 'eventoSelecionado'));
    }

    /**
     * Atualiza um modelo de certificado.
     * Redireciona mantendo o contexto do evento se houver.
     */
    public function update(Request $request, CertificadoModelo $certificado_modelo)
    {
        $data = $this->validateData($request);

        // Guardar o evento_id para redirecionamento
        $eventoId = $data['evento_id'] ?? $certificado_modelo->evento_id;
        $redirectWithEvent = $request->has('redirect_with_event');

        // Se enviou nova imagem, substitui a antiga
        if ($request->hasFile('background')) {
            if ($certificado_modelo->background_path) {
                Storage::disk('public')->delete($certificado_modelo->background_path);
            }

            $data['background_path'] = $request->file('background')
                ->store('certificados/backgrounds', 'public');
        }

        $data['publicado'] = $request->boolean('publicado');

        $certificado_modelo->update($data);

        // Redirecionar mantendo contexto se solicitado
        if ($redirectWithEvent && $eventoId) {
            return redirect()
                ->route('certificados.create', ['evento_id' => $eventoId])
                ->with('success', 'Modelo de certificado atualizado com sucesso!');
        }

        return redirect()
            ->route('certificado-modelos.index')
            ->with('success', 'Modelo de certificado atualizado com sucesso!');
    }

    /**
     * Remove um modelo de certificado.
     * Redireciona mantendo o contexto do evento se houver.
     */
    public function destroy(CertificadoModelo $certificado_modelo)
    {
        $eventoId = $certificado_modelo->evento_id;

        // Remove a imagem de fundo se existir
        if ($certificado_modelo->background_path) {
            Storage::disk('public')->delete($certificado_modelo->background_path);
        }

        $certificado_modelo->delete();

        // Redirecionar mantendo contexto se o modelo estava associado a um evento
        if ($eventoId) {
            return redirect()
                ->route('certificados.create', ['evento_id' => $eventoId])
                ->with('success', 'Modelo de certificado removido com sucesso!');
        }

        return redirect()
            ->route('certificado-modelos.index')
            ->with('success', 'Modelo de certificado removido com sucesso!');
    }

    /**
     * Validação compartilhada entre store e update.
     * Permite evento nulo para modelos globais.
     */
    protected function validateData(Request $request): array
    {
        return $request->validate([
            // Permite nulo para modelos globais (não associados a evento específico)
            'evento_id' => ['nullable', 'uuid', 'exists:eventos,id'],
            'titulo'    => ['required', 'string', 'max:255'],

            // Tipo: participante / palestrante / organizador
            'slug_tipo' => ['required', 'in:participante,palestrante,organizador'],

            // Descrição de atribuição: "Todos os inscritos", "Somente presentes", etc.
            'atribuicao' => ['nullable', 'string', 'max:255'],

            // Conteúdo HTML do modelo
            'corpo_html' => ['required', 'string'],
            'publicado'  => ['nullable', 'boolean'],

            // Imagem de fundo
            'background' => ['nullable', 'image', 'max:2048'],
        ]);
    }

    /**
     * Método auxiliar: Alterna o status de publicação de um modelo.
     */
    public function togglePublicado(CertificadoModelo $certificado_modelo)
    {
        $certificado_modelo->update([
            'publicado' => !$certificado_modelo->publicado
        ]);

        $status = $certificado_modelo->publicado ? 'publicado' : 'ocultado';

        return redirect()
            ->back()
            ->with('success', "Modelo {$status} com sucesso!");
    }

    /**
     * Duplica um modelo de certificado.
     * Mantém o contexto do evento original.
     */
    public function duplicar(CertificadoModelo $certificado_modelo)
    {
        $novoModelo = $certificado_modelo->replicate();
        $novoModelo->titulo = $certificado_modelo->titulo . ' (Cópia)';
        $novoModelo->save();

        return redirect()
            ->route('certificado-modelos.edit', $novoModelo)
            ->with('success', 'Modelo duplicado com sucesso!');
    }
}
