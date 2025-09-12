<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventoDetalheRequest;
use App\Http\Requests\UpdateEventoDetalheRequest;
use App\Models\Event;
use App\Models\EventoDetalhe;
use Illuminate\Http\Request;
use Symfony\Component\VarDumper\Caster\RedisCaster;

class EventoDetalheController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detalhes = EventoDetalhe::with('evento')->get();
        return view('eventos-detalhes.index', compact('detalhes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $eventos = Event::all();
        return view('eventos-detalhes.create', compact('eventos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventoDetalheRequest $request)
    {
        

        EventoDetalhe::create($request->validated());

        return redirect()->route('eventos-detalhes.index')->with('success', 'Detalhe criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $detalhe = EventoDetalhe::with('evento')->findOrFail($id);
        return view('eventos-detalhes.show', compact('detalhe'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $detalhe = EventoDetalhe::findOrFail($id);
        $eventos = Event::all(); // Lista de eventos para selecionar
        return view('eventos-detalhes.edit', compact('detalhe', 'eventos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventoDetalheRequest $request, $id)
    {
        $detalhe = EventoDetalhe::findOrFail($id);
        $detalhe->update($request->validated());

        return redirect()->route('eventos-detalhes.index')->with('success', 'Detalhe atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $detalhe = EventoDetalhe::findOrFail($id);
        $detalhe->delete();

        return redirect()->route('eventos-detalhes.index')->with('success', 'Detalhe deletado com sucesso!');
    }
}
