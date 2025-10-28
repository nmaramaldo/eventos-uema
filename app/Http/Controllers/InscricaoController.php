<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInscricaoRequest;
use App\Http\Requests\UpdateInscricaoRequest;
use App\Models\Event;
use App\Models\Inscricao;
use Illuminate\Database\QueryException;

class InscricaoController extends Controller
{
    /**
     * Minhas inscrições (usuário logado).
     */
    public function index()
    {
        $inscricoes = Inscricao::with('evento')
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('inscricoes.index', compact('inscricoes'));
    }

    /**
     * Inscrever o usuário logado em um evento.
     */
    public function store(StoreInscricaoRequest $request)
    {
        $evento = Event::findOrFail($request->input('evento_id'));

        // ✅ AUTORIZAÇÃO: Chama o método 'create' da InscricaoPolicy.
        $this->authorize('create', Inscricao::class);

        // Validações de negócio que você já tinha (estão ótimas!)
        if (!$evento->inscricoesAbertas()) {
            return back()->with('error', 'As inscrições para este evento não estão abertas.');
        }
        if ($evento->vagasDisponiveis() !== null && $evento->vagasDisponiveis() <= 0) {
            return back()->with('error', 'Não há vagas disponíveis para este evento.');
        }

        // Tenta criar a inscrição (ou a recupera se já existir)
        $inscricao = Inscricao::firstOrCreate(
            ['user_id' => auth()->id(), 'evento_id' => $evento->id],
            ['status' => 'ativa'] // Será ignorado se a inscrição já existir
        );

        // Verifica se a inscrição foi criada nesta requisição
        if ($inscricao->wasRecentlyCreated) {
            return redirect()->route('inscricoes.index')->with('success', 'Inscrição realizada com sucesso!');
        }

        return redirect()->route('inscricoes.index')->with('info', 'Você já estava inscrito neste evento.');
    }

    /**
     * Ver detalhes de uma inscrição específica.
     */
    public function show(Inscricao $inscricao)
    {
        // ✅ AUTORIZAÇÃO: Chama o método 'view' da InscricaoPolicy.
        $this->authorize('view', $inscricao);
        
        $inscricao->load(['evento']);
        return view('inscricoes.show', compact('inscricao'));
    }

    /**
     * Mostra o formulário para editar uma inscrição.
     */
    public function edit(Inscricao $inscricao)
    {
        // ✅ AUTORIZAÇÃO: Chama o método 'update' da InscricaoPolicy.
        $this->authorize('update', $inscricao);
        
        return view('inscricoes.edit', compact('inscricao'));
    }

    /**
     * Atualiza uma inscrição.
     */
    public function update(UpdateInscricaoRequest $request, Inscricao $inscricao)
    {
        // ✅ AUTORIZAÇÃO: Chama o método 'update' da InscricaoPolicy.
        $this->authorize('update', $inscricao);
        
        $inscricao->update($request->validated());
        return redirect()->route('inscricoes.index')->with('success', 'Inscrição atualizada com sucesso!');
    }

    /**
     * Cancelar (deletar) uma inscrição.
     */
    public function destroy(Inscricao $inscricao)
    {
        // ✅ AUTORIZAÇÃO: Chama o método 'delete' da InscricaoPolicy.
        $this->authorize('delete', $inscricao);
        
        $inscricao->delete();
        
        // ✅ MENSAGEM: Melhorada para maior clareza.
        return redirect()->route('inscricoes.index')->with('success', 'Inscrição cancelada com sucesso!');
    }
}