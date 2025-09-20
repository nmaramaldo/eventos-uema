<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $eventos = Event::with(['detalhes', 'coordenador'])
            ->orderBy('data', 'desc')
            ->paginate(10);

        return view('eventos.index', compact('eventos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $coordenadores = User::where('tipo', 'coordenador')->get();
        return view('eventos.create', compact('coordenadores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {

        $evento = Event::create($request->validated());

        return redirect()->route('eventos.index')->with('success', 'Evento criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $evento)
    {
        $evento->load(['detalhes', 'coordenador', 'palestrantes', 'inscricoes']);

        return view('eventos.show', compact('evento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $evento)
    {

        $coordenadores = User::where('tipo', 'coordenador')->get();
        return view('eventos.edit', compact('evento', 'coordenadores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $evento)
    {
        $evento->update($request->validated());

        return redirect()->route('eventos.index')->with('success', 'Evento atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $evento)
    {
        $evento->delete();

        return redirect()->route('eventos.index')->with('success', 'Evento deletado com sucesso!');
    }
}
