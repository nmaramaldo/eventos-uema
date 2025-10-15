<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePalestranteRequest;
use App\Http\Requests\UpdatePalestranteRequest;
use App\Models\Event;
use App\Models\Palestrante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;

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
    public function create(Event $evento)
    {
        return view('palestrantes.create', compact('evento'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePalestranteRequest $request, Event $evento)
    {
        $palestrante = Palestrante::create($request->validated());

        if ($request->has('eventos')) {
            $palestrante->eventos()->sync($request->eventos);
        }

        return redirect()->route('palestrantes.index', ['evento' => $evento->id])->with('success', 'Palestrante cadastrado com sucesso!');
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

    public function indexByEvent(Event $evento)
    {
        $this->authorize('update', $evento); // Usa a permissão do evento

        // Carrega os palestrantes já associados
        $palestrantes = $evento->palestrantes()->orderBy('nome')->paginate();

        // Busca todos os outros palestrantes para um dropdown de "adicionar"
        $palestrantesDisponiveis = Palestrante::orderBy('nome')->paginate();

        return view('palestrantes.index-by-event', compact('evento', 'palestrantes', 'palestrantesDisponiveis'));
    }

    public function createByEvent(Event $evento)
    {
        return view('palestrantes.create-by-event', compact('evento'));
    }

    public function storeByEvent(StorePalestranteRequest $request, Event $evento)
    {
        $data = $request->input('palestrantes', []);

        if (empty($data)) {
            return redirect()->back()->with('error', 'Adicione pelo menos um palestrante.');
        }

        foreach ($data as $palestranteData) {
            // Cria o palestrante
            $palestrante = Palestrante::create($palestranteData);

            // Associa ao evento
            $palestrante->eventos()->attach($evento->id);
        }

        return redirect()
            ->route('eventos.palestrantes.index', $evento)
            ->with('success', 'Palestrante(s) cadastrado(s) com sucesso!');
    }


    public function editByEvent(Event $evento, Palestrante $palestrante)
    {
        return view('palestrantes.edit', compact('evento', 'palestrante'));
    }

    public function updateByEvent(UpdatePalestranteRequest $request, Event $evento, Palestrante $palestrante)
    {
        $palestrante->update($request->validated());
        return redirect()->route('eventos.palestrantes.index', $evento)
            ->with('success', 'Palestrante atualizado com sucesso!');
    }

    public function destroyByEvent(Event $evento, Palestrante $palestrante)
    {
        $palestrante->delete();
        return redirect()->route('eventos.palestrantes.index', $evento)
            ->with('success', 'Palestrante removido com sucesso!');
    }
}
