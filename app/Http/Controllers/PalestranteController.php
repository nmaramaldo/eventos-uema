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

    public function indexByEvent(Event $evento)
    {
        $this->authorize('update', $evento); // Usa a permissão do evento

        // Carrega os palestrantes já associados
        $palestrantes = $evento->palestrantes()->orderBy('nome')->paginate();

        // Busca todos os outros palestrantes para um dropdown de "adicionar"
        $palestrantesDisponiveis = Palestrante::orderBy('nome')->paginate();

        return view('palestrantes.index', compact('evento', 'palestrantes', 'palestrantesDisponiveis'));
    }

    public function createByEvent(Event $evento)
    {
        return view('palestrantes.create', compact('evento'));
    }

    public function storeByEvent(StorePalestranteRequest $request, Event $evento)
    {
        $data = $request->input('palestrantes', []);

        if (empty($data)) {
            return redirect()->back()->with('error', 'Adicione pelo menos um palestrante.');
        }

        $palestrantesBefore = $evento->palestrantes()->exists();

        foreach ($data as $palestranteData) {

            // Cria o palestrante
            $palestrante = Palestrante::firstOrCreate(
                ['email' => $palestranteData['email']], // Busca por email único
                [ // Se não existir, cria com estes dados
                    'nome' => $palestranteData['nome'],
                    'biografia' => $palestranteData['biografia'] ?? null
                ]
            );

            // Associa ao evento    
            if (!$evento->palestrantes()->where('palestrante_id', $palestrante->id)->exists()) {
                $evento->palestrantes()->attach($palestrante->id);
            }
        }

        if (!$palestrantesBefore) {
            // primeira vez adicionando palestrantes
            return redirect()
                ->route('eventos.index', $evento)
                ->with('success', 'Palestrante(s) cadastrado(s) com sucesso!');
        } else {
            // já havia palestrantes antes 
            return redirect()->route('eventos.palestrantes.index', $evento);
        }
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
