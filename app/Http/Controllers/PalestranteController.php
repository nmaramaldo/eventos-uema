<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePalestranteRequest;
use App\Http\Requests\UpdatePalestranteRequest;
use App\Models\Event;
use App\Models\Palestrante;

class PalestranteController extends Controller
{
    public function indexByEvent(Event $evento)
    {
        $this->authorize('update', $evento);

        $palestrantes = $evento->palestrantes()->orderBy('nome')->paginate();
        $palestrantesDisponiveis = Palestrante::orderBy('nome')->paginate();

        return view('palestrantes.index', compact('evento', 'palestrantes', 'palestrantesDisponiveis'));
    }

    public function createByEvent(Event $evento)
    {
        // para montar o select de atividades
        $evento->load(['programacao' => fn($q) => $q->ordenado()]);
        return view('palestrantes.create', compact('evento'));
    }

    public function storeByEvent(StorePalestranteRequest $request, Event $evento)
    {
        $data = $request->input('palestrantes', []);
        if (empty($data)) {
            return back()->with('error', 'Adicione pelo menos um palestrante.');
        }

        $palestrantesBefore = $evento->palestrantes()->exists();

        foreach ($data as $i => $palestranteData) {
            if (empty($palestranteData['nome'])) {
                continue; // Pula se o nome estiver vazio
            }

            if (!empty($palestranteData['email'])) {
                $palestrante = Palestrante::updateOrCreate(
                    ['email' => $palestranteData['email']],
                    [
                        'nome'      => $palestranteData['nome'],
                        'biografia' => $palestranteData['biografia'] ?? null,
                    ]
                );
            } else {
                $palestrante = Palestrante::create([
                    'nome'      => $palestranteData['nome'],
                    'email'     => null,
                    'biografia' => $palestranteData['biografia'] ?? null,
                ]);
            }

            // upload da foto deste índice
            if ($request->hasFile("palestrantes.$i.foto")) {
                $path = $request->file("palestrantes.$i.foto")
                    ->store("speakers/{$palestrante->id}", 'public');
                $palestrante->update(['foto' => $path]);
            }

            // vincula ao evento
            $evento->palestrantes()->syncWithoutDetaching([$palestrante->id]);

            // vincula às atividades (se vieram)
            $atividades = $palestranteData['atividades'] ?? [];
            if (!empty($atividades)) {
                $validos = $evento->programacao()->whereIn('id', $atividades)->pluck('id')->all();
                if (!empty($validos)) {
                    $palestrante->atividades()->syncWithoutDetaching($validos);
                }
            }
        }

        return $palestrantesBefore
            ? redirect()->route('eventos.palestrantes.index', $evento)
            : redirect()->route('eventos.index')->with('success', 'Palestrante(s) cadastrado(s) com sucesso!');
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
