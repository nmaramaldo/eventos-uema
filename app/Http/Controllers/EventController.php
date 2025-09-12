<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $eventos = Event::with(['detalhes', 'coordenador'])->get();

        return view('eventos.index', compact('eventos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $coordenadores = \App\Models\User::all();
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
    public function show($id)
    {
        $evento = Event::with(['detalhes', 'coordenador'])->findOrFail($id);
        return view('eventos.show', compact('evento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $evento = Event::findOrFail($id);
        $coordenadores = \App\Models\User::all();
        return view('eventos.edit', compact('evento', 'coordenadores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, $id)
    {
        $evento = Event::findOrFail($id);
        $evento->update($request->validated());

        return redirect()->route('eventos.index')->with('success', 'Evento atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $evento = Event::findOrFail($id);
        $evento->delete();

        return redirect()->route('eventos.index')->with('success', 'Evento deletado com sucesso!');
    }
}
