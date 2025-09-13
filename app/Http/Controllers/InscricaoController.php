<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInscricaoRequest;
use App\Http\Requests\UpdateInscricaoRequest;
use App\Models\Event;
use App\Models\Inscricao;
use App\Models\User;
use Illuminate\Http\Request;

class InscricaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $inscricoes = Inscricao::with(['user', 'evento'])->get();

        return view('inscricoes.index', compact('inscricoes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        $eventos = Event::all();
        return view('inscricoes.create', compact('users', 'eventos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInscricaoRequest $request)
    {
        $inscricao = Inscricao::create($request->validated());

        return redirect()->route('inscricoes.index')->with('success', 'Inscriçao criada com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inscricao $inscricao)
    {
        $inscricao->load(['user', 'evento']);
        return view('inscricoes.show', compact('inscricao'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inscricao $inscricao)
    {
        $users = User::all();
        $eventos = Event::all();
        return view('inscricoes.edit', compact('inscricao', 'users', 'eventos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInscricaoRequest $request, Inscricao $inscricao)
    {
        $inscricao->update($request->validated());

        return redirect()->route('inscricoes.index')->with('success', 'Inscrição atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inscricao $inscricao)
    {
        $inscricao->delete();

        return redirect()->route('inscricoes.index')->with('success', 'Inscrição deletada com sucesso!');
    }
}
