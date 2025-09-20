<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventoDetalheRequest;
use App\Http\Requests\UpdateEventoDetalheRequest;
use App\Models\Event;
use App\Models\EventoDetalhe;
use Illuminate\Http\Request;


class EventoDetalheController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detalhes = EventoDetalhe::with('evento')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('eventos_detalhes.index', compact('detalhes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $eventos = Event::all();
        return view('eventos_detalhes.create', compact('eventos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventoDetalheRequest $request)
    {

        EventoDetalhe::create($request->validated());

        return redirect()->route('eventos_detalhes.index')->with('success', 'Detalhe criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(EventoDetalhe $detalhe)
    {
        $detalhe->load('evento');
        return view('eventos_detalhes.show', compact('detalhe'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventoDetalhe $detalhe)
    {
        $eventos = Event::all();
        return view('eventos_detalhes.edit', compact('detalhe', 'eventos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventoDetalheRequest $request, EventoDetalhe $detalhe)
    {
        $detalhe->update($request->validated());

        return redirect()->route('eventos_detalhes.index')->with('success', 'Detalhe atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventoDetalhe $detalhe)
    {
        $detalhe->delete();

        return redirect()->route('eventos_detalhes.index')->with('success', 'Detalhe deletado com sucesso!');
    }
}
