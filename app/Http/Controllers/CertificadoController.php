<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCertificadoRequest;
use App\Http\Requests\UpdateCertificadoRequest;
use App\Models\Certificado;
use App\Models\Inscricao;
use Illuminate\Http\Request;

class CertificadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $certificados = Certificado::with(['inscricao.evento', 'inscricao.user'])
            ->orderBy('data_emissao', 'desc')
            ->paginate(15);

        return view('certificados.index', compact('certificados'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $inscricoes = Inscricao::whereHas('evento', function ($query) {
            $query->where('status', 'finalizado');
        })
            ->with('user', 'evento')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('certificados.create', compact('inscricoes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCertificadoRequest $request)
    {
        $data = $request->validated();
        if (empty($data['data_emissao'])) {
            $data['data_emissao'] = now();
        }

        $certificado = Certificado::create($request->validated());

        return redirect()->route('certificados.index')->with('success', 'Certificado criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Certificado $certificado)
    {
        $certificado->load('inscricao.user', 'inscricao.evento');

        return view('certificados.show', compact('certificado'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Certificado $certificado)
    {
        $inscricoes = Inscricao::with('user', 'evento')
        ->orderBy('created_at', 'desc')
        ->get();

        return view('certificados.edit', compact('certificado', 'inscricoes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCertificadoRequest $request, Certificado $certificado)
    {
        $certificado->update($request->validated());

        return redirect()->route('certificados.index')->with('success', 'Certificado atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certificado $certificado)
    {
        $certificado->delete();

        return redirect()->route('certificados.index')->with('success', 'Certificado deletado com sucesso!');
    }
}
