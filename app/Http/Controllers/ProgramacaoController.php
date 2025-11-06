<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgramacaoRequest;
use App\Http\Requests\UpdateProgramacaoRequest;
use App\Http\Requests\StoreAjaxProgramacaoRequest;
use App\Models\Event;
use App\Models\Local;
use App\Models\Programacao;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProgramacaoController extends Controller
{
    /** Permite somente Admin e Master */
    private function authorizeAdminOrMaster(): void
    {
        $u = auth()->user();
        $allow = $u && in_array(strtolower((string)($u->tipo ?? '')), ['admin','master'], true);

        abort_unless($allow, 403);
    }

    public function indexByEvent(Event $evento)
    {
        $this->authorizeAdminOrMaster();

        $evento->load(['detalhes.local']);

        return view('eventos.programacao.index', [
            'evento' => $evento,
            'itens'  => $evento->detalhes()->orderBy('data_hora_inicio')->get(),
        ]);
    }

    public function createForEvent(Event $evento)
    {
        $this->authorizeAdminOrMaster();

        return view('eventos.programacao.create', [
            'evento' => $evento,
            'locais' => Local::orderBy('nome')->get(),
        ]);
    }

    public function storeAjaxForEvent(StoreAjaxProgramacaoRequest $request, Event $evento)
    {
        $this->authorizeAdminOrMaster();

        try {
            $data = $request->validated();

            // Local: aceita ID ou nome (cria se vier apenas o nome)
            $localId = $data['local_id'] ?? null;
            if (!$localId && !empty($data['localidade'])) {
                $local = Local::firstOrCreate(['nome' => trim($data['localidade'])]);
                $localId = $local->id;
            }

            DB::beginTransaction();

            // Se o ID for fornecido, atualiza. Senão, cria.
            $a = Programacao::findOrNew($data['id'] ?? null);

            if (Schema::hasColumn('programacao', 'evento_id'))        $a->evento_id        = $evento->id;
            if (Schema::hasColumn('programacao', 'titulo'))           $a->titulo           = $data['titulo'];
            if (Schema::hasColumn('programacao', 'descricao'))        $a->descricao        = $data['descricao'] ?? null;
            if (Schema::hasColumn('programacao', 'data_hora_inicio')) $a->data_hora_inicio = $data['data_hora_inicio'];
            if (Schema::hasColumn('programacao', 'data_hora_fim'))    $a->data_hora_fim    = $data['data_hora_fim'];
            if (Schema::hasColumn('programacao', 'modalidade'))       $a->modalidade       = $data['modalidade'] ?? null;
            if (Schema::hasColumn('programacao', 'localidade'))       $a->localidade       = $data['localidade'] ?? null;
            if (Schema::hasColumn('programacao', 'local_id'))         $a->local_id         = $localId;

            // capacidade OU vagas (salva no que existir)
            if (isset($data['capacidade']) && $data['capacidade'] !== '') {
                if (Schema::hasColumn('programacao', 'capacidade')) {
                    $a->capacidade = (int) $data['capacidade'];
                } elseif (Schema::hasColumn('programacao', 'vagas')) {
                    $a->vagas = (int) $data['capacidade'];
                }
            }

            if (Schema::hasColumn('programacao', 'requer_inscricao')) {
                $a->requer_inscricao = (bool)($data['requer_inscricao'] ?? false);
            }

            $a->save();
            DB::commit();

            $a->load('local');

            return response()->json(['success' => true, 'atividade' => $a]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('storeAjaxForEvent', ['m'=>$e->getMessage(),'f'=>$e->getFile(),'l'=>$e->getLine()]);
            return response()->json([
                'success'=>false,
                'message'=>'Erro ao salvar a atividade.',
                'error'=>$e->getMessage(),
                'at'=>basename($e->getFile()).':'.$e->getLine(),
            ], 500);
        }
    }

    public function storeForEvent(StoreProgramacaoRequest $request, Event $evento)
    {
        $this->authorizeAdminOrMaster();

        $data = $request->validated();

        foreach ($data['atividades'] as $atividadeData) {
            $atividadeData['evento_id'] = $evento->id;
            $atividadeData['requer_inscricao'] = !empty($atividadeData['requer_inscricao']);
            Programacao::create($atividadeData);
        }

        $isFirstTime = $evento->programacao()->count() === count($data['atividades']);

        if ($isFirstTime && $evento->palestrantes()->count() === 0) {
            return redirect()
                ->route('eventos.palestrantes.create', $evento)
                ->with('success', count($data['atividades']).' atividades adicionadas! Agora adicione os palestrantes.');
        }

        return redirect()
            ->route('eventos.programacao.index', $evento)
            ->with('success', 'Atividades adicionadas com sucesso!');
    }

    public function editByEvent(Event $evento, Programacao $atividade)
    {
        $this->authorizeAdminOrMaster();

        $palestrantes = $evento->palestrantes()->orderBy('nome')->get();
        $selecionados = $atividade->palestrantes()->pluck('palestrantes.id')->toArray();

        return view('eventos.programacao.edit', compact('evento','atividade','palestrantes','selecionados'));
    }

    public function updateByEvent(UpdateProgramacaoRequest $request, Event $evento, Programacao $atividade)
    {
        $this->authorizeAdminOrMaster();

        $atividade->update($request->validated());

        $ids = collect($request->input('palestrantes', []))->filter()->unique()->values()->all();
        $atividade->palestrantes()->sync($ids);

        return redirect()->route('eventos.programacao.index', $evento)
            ->with('success','Atividade atualizada com sucesso!');
    }

    public function destroyByEvent(Event $evento, Programacao $atividade)
    {
        $this->authorizeAdminOrMaster();

        $atividade->delete();

        return redirect()->route('eventos.programacao.index', $evento)
            ->with('success','Atividade removida com sucesso!');
    }
}
