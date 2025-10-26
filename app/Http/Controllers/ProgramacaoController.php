<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProgramacaoRequest;
use App\Http\Requests\StoreProgramacaoRequest;
use App\Models\Event;
use App\Models\Programacao;
use App\Models\Local;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProgramacaoController extends Controller
{
    // ---------------------
    // LISTA por EVENTO
    // ---------------------
    public function indexByEvent(Event $evento)
    {
        $this->authorizeManage();

        $evento->load(['detalhes.local']);
        return view('eventos.programacao.index', [
            'evento'  => $evento,
            'itens'   => $evento->detalhes()->orderBy('data_hora_inicio')->get(),
        ]);
    }

    // ---------------------
    // FORM - criar por EVENTO
    // ---------------------
    public function createForEvent(Event $evento)
    {
        $this->authorizeManage();

        return view('eventos.programacao.create', [
            'evento' => $evento,
            'locais' => Local::orderBy('nome')->get(),
        ]);
    }

    // ---------------------
    // STORE - por EVENTO
    // ---------------------
    public function storeForEvent(StoreProgramacaoRequest $r, Event $evento)
    {
        $this->authorizeManage();

        $data = $r->validated();

        foreach ($data['atividades'] as $atividadeData) {
            $atividadeData['evento_id'] = $evento->id;
            $atividadeData['requer_inscricao'] = (bool)($atividadeData['requer_inscricao'] ?? false);

            Programacao::create($atividadeData);
        }

        $isFirstTime = $evento->programacao()->count() === count($data['atividades']);

        if ($isFirstTime && $evento->palestrantes()->count() === 0) {
            return redirect()
                ->route('eventos.palestrantes.create', $evento)
                ->with('success', count($data['atividades']) . ' atividades adicionadas com sucesso! Agora adicione os palestrantes.');
        } else {
            return redirect()->route('eventos.programacao.index', $evento);
        }
    }

    public function editByEvent(Event $evento, Programacao $atividade)
    {
        $this->authorizeManage();

        return view('eventos.programacao.edit', compact('evento', 'atividade'));
    }

    public function updateByEvent(UpdateProgramacaoRequest $request, Event $evento, Programacao $atividade)
    {
        $this->authorizeManage();

        $atividade->update($request->validated());
        return redirect()->route('eventos.programacao.index', $evento)
            ->with('success', 'Atividade atualizada com sucesso!');
    }

    public function destroyByEvent(Event $evento, Programacao $atividade)
    {
        $this->authorizeManage();

        $atividade->delete();

        return redirect()->route('eventos.programacao.index', $evento)
            ->with('success', 'Atividade removida com sucesso!');
    }


    // ---------------------
    private function authorizeManage(): void
    {
        // Usa a gate que você já tem (manage-users)
        if (!Gate::allows('manage-users')) {
            abort(403);
        }
    }
}
