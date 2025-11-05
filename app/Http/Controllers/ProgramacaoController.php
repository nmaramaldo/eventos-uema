<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgramacaoRequest;
use App\Http\Requests\UpdateProgramacaoRequest;
use App\Models\Event;
use App\Models\Local;
use App\Models\Palestrante;
use App\Models\Programacao;
use Illuminate\Support\Facades\Gate;

class ProgramacaoController extends Controller
{
    public function indexByEvent(Event $evento)
    {
        $this->authorizeManage();

        // carrega relações necessárias
        $evento->load(['detalhes.local']);

        return view('eventos.programacao.index', [
            'evento' => $evento,
            'itens'  => $evento->detalhes()->orderBy('data_hora_inicio')->get(),
        ]);
    }

    public function createForEvent(Event $evento)
    {
        $this->authorizeManage();

        return view('eventos.programacao.create', [
            'evento' => $evento,
            'locais' => Local::orderBy('nome')->get(),
        ]);
    }

    public function storeAjaxForEvent(StoreProgramacaoRequest $request, Event $evento)

    {
        $this->authorizeManage();

        $atividadeData = [
            'titulo' => $request->input('titulo'),
            'descricao' => $request->input('descricao'),
            'data_hora_inicio' => $request->input('inicio'),
            'data_hora_fim' => $request->input('fim'),
            'local_id' => $request->input('local'),
            'vagas' => $request->input('vagas'),
            'requer_inscricao' => $request->boolean('requer_inscricao'),
            'evento_id' => $evento->id,
        ];

        $atividade = Programacao::create($atividadeData);

        return response()->json(['success' => true, 'atividade' => $atividade]);    
    }


    public function storeForEvent(StoreProgramacaoRequest $request, Event $evento)
    {
        $this->authorizeManage();

        $data = $request->validated();

        // cria múltiplas atividades de uma vez
        foreach ($data['atividades'] as $atividadeData) {
            $atividadeData['evento_id'] = $evento->id;
            $atividadeData['requer_inscricao'] = !empty($atividadeData['requer_inscricao']);

            Programacao::create($atividadeData);
        }

        // checa se foi a primeira vez que o evento recebeu atividades
        $isFirstTime = $evento->programacao()->count() === count($data['atividades']);

        if ($isFirstTime && $evento->palestrantes()->count() === 0) {
            return redirect()
                ->route('eventos.palestrantes.create', $evento)
                ->with('success', count($data['atividades']) . ' atividades adicionadas com sucesso! Agora adicione os palestrantes.');
        }

        return redirect()
            ->route('eventos.programacao.index', $evento)
            ->with('success', 'Atividades adicionadas com sucesso!');
    }

    public function editByEvent(Event $evento, Programacao $atividade)
    {
        $this->authorizeManage();

        $palestrantes = $evento->palestrantes()->orderBy('nome')->get();
        $selecionados = $atividade->palestrantes()->pluck('palestrantes.id')->toArray();

        return view('eventos.programacao.edit', compact('evento', 'atividade', 'palestrantes', 'selecionados'));
    }

    public function updateByEvent(UpdateProgramacaoRequest $request, Event $evento, Programacao $atividade)
    {
        $this->authorizeManage();

        $validated = $request->validated();
        $atividade->update($validated);

        // sincroniza os palestrantes vinculados
        $ids = collect($request->input('palestrantes', []))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $atividade->palestrantes()->sync($ids);

        return redirect()
            ->route('eventos.programacao.index', $evento)
            ->with('success', 'Atividade atualizada com sucesso!');
    }

    public function destroyByEvent(Event $evento, Programacao $atividade)
    {
        $this->authorizeManage();

        $atividade->delete();

        return redirect()
            ->route('eventos.programacao.index', $evento)
            ->with('success', 'Atividade removida com sucesso!');
    }

    private function authorizeManage(): void
    {
        abort_unless(Gate::allows('manage-users'), 403);
    }
}
