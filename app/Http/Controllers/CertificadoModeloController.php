<?php

namespace App\Http\Controllers;

use App\Models\CertificadoModelo;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificadoModeloController extends Controller
{
    public function index()
    {
        $modelos = CertificadoModelo::with('evento')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('certificados.modelos-index', compact('modelos'));
    }

    public function create()
    {
        $modelo  = new CertificadoModelo();
        $eventos = Event::orderBy('nome')->get();

        return view('certificados.modelo', compact('modelo', 'eventos'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        // upload da imagem de fundo, se houver
        if ($request->hasFile('background')) {
            $data['background_path'] = $request->file('background')
                ->store('certificados/backgrounds', 'public');
        }

        $data['publicado'] = $request->boolean('publicado');

        CertificadoModelo::create($data);

        return redirect()
            ->route('certificado-modelos.index')
            ->with('success', 'Modelo de certificado criado com sucesso!');
    }

    public function edit(CertificadoModelo $certificado_modelo)
    {
        $modelo  = $certificado_modelo;
        $eventos = Event::orderBy('nome')->get();

        return view('certificados.modelo', compact('modelo', 'eventos'));
    }

    public function update(Request $request, CertificadoModelo $certificado_modelo)
    {
        $data = $this->validateData($request);

        // se enviou nova imagem, troca
        if ($request->hasFile('background')) {
            if ($certificado_modelo->background_path) {
                Storage::disk('public')->delete($certificado_modelo->background_path);
            }

            $data['background_path'] = $request->file('background')
                ->store('certificados/backgrounds', 'public');
        }

        $data['publicado'] = $request->boolean('publicado');

        $certificado_modelo->update($data);

        return redirect()
            ->route('certificado-modelos.index')
            ->with('success', 'Modelo de certificado atualizado com sucesso!');
    }

    public function destroy(CertificadoModelo $certificado_modelo)
    {
        if ($certificado_modelo->background_path) {
            Storage::disk('public')->delete($certificado_modelo->background_path);
        }

        $certificado_modelo->delete();

        return redirect()
            ->route('certificado-modelos.index')
            ->with('success', 'Modelo de certificado removido com sucesso!');
    }

    /**
     * Validação compartilhada entre store e update.
     */
    protected function validateData(Request $request): array
    {
        return $request->validate([
            'evento_id' => ['required', 'uuid', 'exists:eventos,id'],
            'titulo'    => ['required', 'string', 'max:255'],

            // participante / palestrante / organizador
            'slug_tipo' => ['required', 'in:participante,palestrante,organizador'],

            // ex: "Todos os inscritos", "Somente presentes", etc.
            'atribuicao' => ['nullable', 'string', 'max:255'],

            'corpo_html' => ['required', 'string'],
            'publicado'  => ['nullable', 'boolean'],

            // imagem de fundo
            'background' => ['nullable', 'image', 'max:2048'],
        ]);
    }
}
