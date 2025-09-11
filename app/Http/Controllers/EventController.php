<?php

namespace App\Http\Controllers;

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
    public function store(Request $request)
    {
        $data = $request->validate([
            'coordenador_id' => 'required|exists:users,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'data_inicio_evento' => 'required|date',
            'data_fim_evento' => 'required|date',
            'data_inicio_inscricao' => 'required|date',
            'data_fim_inscricao' => 'required|date',
            'tipo_evento' => 'required|string|max:100',
            'logomarca_url' => 'nullable|string',
            'status' => 'required|string|max:50',
        ]);

        $evento = Event::create($data);

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
    public function update(Request $request, $id)
    {
        $evento = Event::findOrFail($id);

        $data = $request->validate([
            'nome' => 'sometimes|string|max:255',
            'descricao' => 'sometimes|string',
            'data_inicio_evento' => 'sometimes|date',
            'data_fim_evento' => 'sometimes|date',
            'data_inicio_inscricao' => 'sometimes|date',
            'data_fim_inscricao' => 'sometimes|date',
            'tipo_evento' => 'sometimes|string|max:100',
            'logomarca_url' => 'nullable|string',
            'status' => 'sometimes|string|max:50',
        ]);

        $evento->update($data);

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
