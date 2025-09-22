<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePalestranteRequest;
use App\Http\Requests\UpdatePalestranteRequest;
use App\Models\Event;
use App\Models\Palestrante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PalestranteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $palestrantes = Palestrante::with(['eventos'])
        ->orderBy('nome')
        ->paginate(10);

        return view('palestrantes.index', compact('palestrantes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $eventos = Event::orderBy('nome')->get();

        return view('palestrantes.create', compact('eventos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePalestranteRequest $request)
    {
        $palestrante = Palestrante::create($request->validated());

        if ($request->has('eventos')) {
            $palestrante->eventos()->sync($request->eventos);
        }

        return redirect()->route('palestrantes.index')->with('success', 'Palestrante cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Palestrante $palestrante)
    {
        $palestrante->load(['eventos' => function ($query) {
            $query->with('coordenador')->orderBy('data', 'desc');
        }]);

        return view('palestrantes.show', compact('palestrante'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Palestrante $palestrante)
    {
        $eventos = Event::orderBy('nome')->get();
        $palestrante->load('eventos');

        return view('palestrantes.edit', compact('palestrante', 'eventos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePalestranteRequest $request, Palestrante $palestrante)
    {
        $palestrante->update($request->validated());

        $palestrante->eventos()->sync($request->eventos ?? []);

        return redirect()->route('palestrantes.index')->with('success', 'Palestrante atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Palestrante $palestrante)
    {

        if ($palestrante->eventos()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível excluir o palestrante pois existem eventos associados.');
        }

        $palestrante->delete();

        return redirect()->route('palestrantes.index')->with('success', 'Palestrante excluído com sucesso!');
    }
}
