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
     * Lista "Minhas inscrições".
     */
    public function index()
    {
        $inscricoes = Inscricao::with(['evento'])
            ->where('user_id', auth()->guard('web')->id())
            ->orderByDesc('created_at')
            ->paginate(20); // para usar {{ $inscricoes->links() }}

        return view('inscricoes.index', compact('inscricoes'));
    }

    /**
     * Form opcional de criação (se for permitir escolher evento por aqui).
     */
    public function create()
    {
        $eventos = Event::where('data_inicio_evento', '>=', now())
            ->orderBy('data_inicio_evento', 'asc')
            ->get();

        return view('inscricoes.create', compact('eventos'));
    }

    /**
     * Realiza a inscrição do usuário logado.
     */
    public function store(StoreInscricaoRequest $request)
    {
        // Compatibilidade: aceita 'evento_id' (atual) ou 'event_id' (legado)
        $validated = $request->validated();
        $userId    = auth()->guard('web')->id();
        $eventoId  = $validated['evento_id'] ?? $request->input('evento_id') ?? $request->input('event_id');

        if (!$eventoId) {
            return back()->withErrors(['error' => 'Evento não informado.']);
        }

        $evento = Event::find($eventoId);
        if (!$evento) {
            return back()->withErrors(['error' => 'Evento inválido.']);
        }

        // Janela de inscrição precisa estar aberta
        if (!$evento->inscricoesAbertas()) {
            return back()->withErrors(['error' => 'Inscrições indisponíveis para este evento.']);
        }

        // Evita duplicidade
        $jaInscrito = Inscricao::where('user_id', $userId)
            ->where('evento_id', $evento->id)
            ->exists();
        if ($jaInscrito) {
            return back()->withErrors(['error' => 'Você já está inscrito neste evento.']);
        }

        // (Opcional) Checa vagas se sua tabela do evento tiver 'vagas' (o helper do Model lida quando não existe)
        $vagas = $evento->vagasDisponiveis();
        if ($vagas !== null && $vagas <= 0) {
            return back()->withErrors(['error' => 'Não há vagas disponíveis.']);
        }

        try {
            Inscricao::create([
                'user_id'   => $userId,
                'evento_id' => $evento->id,
            ]);

            return redirect()
                ->route('inscricoes.index')
                ->with('success', 'Inscrição realizada com sucesso.');
        } catch (QueryException $e) {
            // 23000 (SQLite/MySQL) — violação de constraint (ex.: índice único)
            if ($e->getCode() === '23000') {
                return back()->withErrors(['error' => 'Você já está inscrito neste evento.']);
            }
            return back()->withErrors(['error' => 'Erro ao processar a inscrição.']);
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Erro ao processar a inscrição.']);
        }
    }

    /**
     * Exibe a inscrição (do próprio usuário).
     */
    public function show(Inscricao $inscricao)
    {
        if ($inscricao->user_id !== auth()->guard('web')->id()) {
            abort(403, 'Acesso não autorizado');
        }

        $inscricao->load(['usuario', 'evento']); // assuming Inscricao has usuario() and evento()
        return view('inscricoes.show', compact('inscricao'));
    }

    /**
     * Form de edição (opcional).
     */
    public function edit(Inscricao $inscricao)
    {
        if ($inscricao->user_id !== auth()->guard('web')->id()) {
            abort(403, 'Acesso não autorizado');
        }

        $eventos = Event::where('data_inicio_evento', '>=', now())
            ->orderBy('data_inicio_evento', 'asc')
            ->get();

        return view('inscricoes.edit', compact('inscricao', 'eventos'));
    }

    /**
     * Atualiza a inscrição (opcional).
     */
    public function update(UpdateInscricaoRequest $request, Inscricao $inscricao)
    {
        if ($inscricao->user_id !== auth()->guard('web')->id()) {
            abort(403, 'Acesso não autorizado');
        }

        // Se sua UpdateInscricaoRequest permitir mudar o evento, considere checar duplicidade/janela aqui também.
        $inscricao->update($request->validated());

        return redirect()
            ->route('inscricoes.index')
            ->with('success', 'Inscrição atualizada com sucesso!');
    }

    /**
     * Cancela a inscrição (do próprio usuário).
     */
    public function destroy(Inscricao $inscricao)
    {
        if ($inscricao->user_id !== auth()->guard('web')->id()) {
            abort(403, 'Acesso não autorizado');
        }

        $inscricao->delete();

        return redirect()
            ->route('inscricoes.index')
            ->with('success', 'Inscrição deletada com sucesso!');
    }
}
