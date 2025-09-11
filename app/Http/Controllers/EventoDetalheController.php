<?php

namespace App\Http\Controllers;

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
    public function store(Request $request)
    {
        $data = $request->validate([
            'evento_id' => 'required|exists:eventos,id',
            'descricao' => 'required|string',
            'data' => 'required|date',
            'hora_inicio' => 'required',
            'hora_fim' => 'required',
            'modalidade' => 'required|string|max:100',
            'capacidade' => 'nullable|integer',
        ]);

        EventoDetalhe::create($data);

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
    public function update(Request $request, $id)
    {
        $detalhe = EventoDetalhe::findOrFail($id);

        $data = $request->validate([
            'descricao' => 'sometimes|string',
            'data' => 'sometimes|date',
            'hora_inicio' => 'sometimes',
            'hora_fim' => 'sometimes',
            'modalidade' => 'sometimes|string|max:100',
            'capacidade' => 'nullable|integer',

        ]);

        $detalhe->update($data);

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
