<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInscricaoRequest;
use App\Http\Requests\UpdateInscricaoRequest;
use App\Models\Event;
use App\Models\Inscricao;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class InscricaoController extends Controller
{
    /**
     * Minhas inscrições (usuário logado).
     */
    public function index()
    {
        $userId = auth()->id();

        $inscricoes = Inscricao::with('evento')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('inscricoes.index', compact('inscricoes'));
    }

    /**
     * (Opcional) Form de criação.
     */
    public function create()
    {
        $eventos = Event::where('data_inicio_evento', '>=', now())
            ->orderBy('data_inicio_evento')
            ->get();

        return view('inscricoes.create', compact('eventos'));
    }

    /**
     * Inscrever (idempotente).
     */
    public function store(StoreInscricaoRequest $request)
    {
        $userId = auth()->id();
        if (!$userId) {
            return back()->withErrors(['error' => 'Você precisa estar logado para se inscrever.']);
        }

        $eventoId = $request->input('evento_id') ?? $request->input('event_id');
        if (!$eventoId) {
            return back()->withErrors(['error' => 'Evento não informado.']);
        }

        $evento = Event::find($eventoId);
        if (!$evento) {
            return back()->withErrors(['error' => 'Evento inválido.']);
        }

        // Janela de inscrições deve estar aberta
        if (!$evento->inscricoesAbertas()) {
            return back()->withErrors(['error' => 'Inscrições indisponíveis para este evento.']);
        }

        // Vagas (se houver limite)
        $vagas = $evento->vagasDisponiveis();
        if ($vagas !== null && $vagas <= 0) {
            return back()->withErrors(['error' => 'Não há vagas disponíveis.']);
        }

        try {
            $inscricao = Inscricao::firstOrCreate(
                ['user_id' => $userId, 'evento_id' => $evento->id],
                ['status'  => 'ativa'] // remova se sua coluna não existir
            );

            if ($inscricao->wasRecentlyCreated) {
                return redirect()
                    ->route('inscricoes.index')
                    ->with('success', 'Inscrição realizada com sucesso.');
            }

            return back()->withErrors(['error' => 'Você já está inscrito neste evento.']);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return back()->withErrors(['error' => 'Você já está inscrito neste evento.']);
            }
            return back()->withErrors(['error' => 'Erro ao processar a inscrição.']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Erro ao processar a inscrição.']);
        }
    }

    /**
     * Ver inscrição.
     */
    public function show(Inscricao $inscricao)
    {
        if ($inscricao->user_id !== auth()->id()) {
            abort(403);
        }

        $inscricao->load(['usuario', 'evento']);
        return view('inscricoes.show', compact('inscricao'));
    }

    /**
     * (Opcional) Editar.
     */
    public function edit(Inscricao $inscricao)
    {
        if ($inscricao->user_id !== auth()->id()) {
            abort(403);
        }

        $eventos = Event::where('data_inicio_evento', '>=', now())
            ->orderBy('data_inicio_evento')
            ->get();

        return view('inscricoes.edit', compact('inscricao', 'eventos'));
    }

    /**
     * (Opcional) Atualizar.
     */
    public function update(UpdateInscricaoRequest $request, Inscricao $inscricao)
    {
        if ($inscricao->user_id !== auth()->id()) {
            abort(403);
        }

        $inscricao->update($request->validated());

        return redirect()
            ->route('inscricoes.index')
            ->with('success', 'Inscrição atualizada com sucesso!');
    }

    /**
     * Cancelar inscrição.
     */
    public function destroy(Inscricao $inscricao)
    {
        if ($inscricao->user_id !== auth()->id()) {
            abort(403);
        }

        $inscricao->delete();

        return redirect()
            ->route('inscricoes.index')
            ->with('success', 'Inscrição deletada com sucesso!');
    }
}
