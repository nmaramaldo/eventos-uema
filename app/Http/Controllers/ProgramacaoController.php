<?php

namespace App\Http\Controllers;

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
    public function storeForEvent(Request $r, Event $evento)
    {
        $this->authorizeManage();

        $data = $r->validate([
            'titulo'            => ['required', 'string', 'max:255'],
            'descricao'         => ['nullable', 'string'],
            'data_hora_inicio'  => ['required', 'date'],
            'data_hora_fim'     => ['required', 'date', 'after_or_equal:data_hora_inicio'],
            'modalidade' => ['required', 'string', 'max:255'],
            'capacidade'        => ['nullable', 'integer', 'min:1'],
            'localidade'        => ['required', 'string', 'max:255'],
            'requer_inscricao'  => ['nullable', 'boolean'],
        ]);

        $data['evento_id'] = $evento->id;
        $data['requer_inscricao'] = (bool)($data['requer_inscricao'] ?? false);

        Programacao::create($data);

        return redirect()
            ->route('eventos.programacao.index', $evento)
            ->with('success', 'Atividade adicionada com sucesso.');
    }

    // ---------------------
    // CRUD genérico (se quiser usar)
    // ---------------------

    public function index()
    {
        $this->authorizeManage();

        $itens = Programacao::with(['evento', 'local'])
            ->orderByDesc('data_hora_inicio')
            ->paginate(20);

        return view('eventos_detalhes.index', compact('itens'));
    }

    public function create()
    {
        $this->authorizeManage();

        return view('eventos_detalhes.create', [
            'eventos' => Event::orderBy('nome')->get(),
            'locais'  => Local::orderBy('nome')->get(),
        ]);
    }

    public function store(Request $r)
    {
        $this->authorizeManage();

        $data = $r->validate([
            'evento_id'         => ['required', 'uuid', 'exists:eventos,id'],
            'titulo'            => ['required', 'string', 'max:255'],
            'descricao'         => ['nullable', 'string'],
            'data_hora_inicio'  => ['required', 'date'],
            'data_hora_fim'     => ['required', 'date', 'after_or_equal:data_hora_inicio'],
            'local_id'          => ['nullable', 'uuid', 'exists:locais,id'],
            'requer_inscricao'  => ['nullable', 'boolean'],
            'vagas'             => ['nullable', 'integer', 'min:1'],
        ]);

        $data['requer_inscricao'] = (bool)($data['requer_inscricao'] ?? false);

        Programacao::create($data);

        return redirect()->route('eventos_detalhes.index')->with('success', 'Atividade criada.');
    }

    public function edit(Programacao $eventos_detalhe)
    {
        $this->authorizeManage();

        return view('eventos.programacao.edit', [
            'item'    => $eventos_detalhe,
            'eventos' => Event::orderBy('nome')->get(),
            'locais'  => Local::orderBy('nome')->get(),
        ]);
    }

    public function update(Request $r, Programacao $eventos_detalhe)
    {
        $this->authorizeManage();

        $data = $r->validate([
            'evento_id'         => ['required', 'uuid', 'exists:eventos,id'],
            'titulo'            => ['required', 'string', 'max:255'],
            'descricao'         => ['nullable', 'string'],
            'data_hora_inicio'  => ['required', 'date'],
            'data_hora_fim'     => ['required', 'date', 'after_or_equal:data_hora_inicio'],
            'local_id'          => ['nullable', 'uuid', 'exists:locais,id'],
            'requer_inscricao'  => ['nullable', 'boolean'],
            'vagas'             => ['nullable', 'integer', 'min:1'],
        ]);

        $data['requer_inscricao'] = (bool)($data['requer_inscricao'] ?? false);

        $eventos_detalhe->update($data);

        return back()->with('success', 'Atividade atualizada.');
    }

    public function destroy(Programacao $eventos_detalhe)
    {
        $this->authorizeManage();

        $eventos_detalhe->delete();

        return back()->with('success', 'Atividade removida.');
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
