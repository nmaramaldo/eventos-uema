<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    /**
     * Listagem de eventos.
     */
    public function index()
    {
        $eventos = Event::with(['detalhes', 'coordenador'])
            ->orderByDesc('data_inicio_evento')
            ->paginate(10);

        return view('eventos.index', compact('eventos'));
    }

    /**
     * Form de criação (wizard).
     */
    public function create()
    {
        abort_unless(Gate::allows('manage-users'), 403);

        $coordenadores = User::whereIn('tipo_usuario', ['admin', 'master'])
            ->orderBy('name')
            ->get();

        if (auth()->check() && $coordenadores->doesntContain('id', auth()->id())) {
            $coordenadores->push(auth()->user());
        }

        return view('eventos.wizard', compact('coordenadores'));
    }

    /**
     * Salvar novo evento.
     */
    public function store(StoreEventRequest $request)
    {
        abort_unless(Gate::allows('manage-users'), 403);

        $data = $request->validated();

        // Normaliza tipo_evento se vier com rótulos legados.
        if (!empty($data['tipo_evento'])) {
            $data['tipo_evento'] = $this->normalizeTipoEvento($data['tipo_evento']);
        }

        // Status padrão se não vier do form.
        $data['status'] = $data['status'] ?? 'rascunho';

        // Coordenador padrão: usuário atual.
        if (empty($data['coordenador_id']) && auth()->check()) {
            $data['coordenador_id'] = auth()->id();
        }

        $evento = Event::create($data);

        return redirect()
            ->route('eventos.show', $evento)
            ->with('success', 'Evento criado com sucesso!');
    }

    /**
     * Detalhe do evento.
     */
    public function show(Event $evento)
    {
        $evento->load(['detalhes', 'coordenador', 'palestrantes', 'inscricoes']);

        return view('eventos.show', compact('evento'));
    }

    /**
     * Form de edição (wizard).
     */
    public function edit(Event $evento)
    {
        abort_unless(Gate::allows('manage-users'), 403);

        $coordenadores = User::whereIn('tipo_usuario', ['admin', 'master'])
            ->orderBy('name')
            ->get();

        if (auth()->check() && $coordenadores->doesntContain('id', auth()->id())) {
            $coordenadores->push(auth()->user());
        }

        return view('eventos.wizard', compact('evento', 'coordenadores'));
    }

    /**
     * Atualizar evento.
     */
    public function update(UpdateEventRequest $request, Event $evento)
    {
        abort_unless(Gate::allows('manage-users'), 403);

        $data = $request->validated();

        // Normaliza tipo_evento, se vier.
        if (array_key_exists('tipo_evento', $data) && !empty($data['tipo_evento'])) {
            $data['tipo_evento'] = $this->normalizeTipoEvento($data['tipo_evento']);
        }

        // Status: se não vier, preserva o atual (evita erro de "required").
        if (!array_key_exists('status', $data) || $data['status'] === null || $data['status'] === '') {
            $data['status'] = $evento->status ?? 'rascunho';
        }

        // Coordenador padrão.
        if (empty($data['coordenador_id']) && auth()->check()) {
            $data['coordenador_id'] = auth()->id();
        }

        $evento->update($data);

        return redirect()
            ->route('eventos.show', $evento)
            ->with('success', 'Evento atualizado com sucesso!');
    }

    /**
     * Remover evento.
     */
    public function destroy(Event $evento)
    {
        abort_unless(Gate::allows('manage-users'), 403);

        $evento->delete();

        return redirect()
            ->route('eventos.index')
            ->with('success', 'Evento deletado com sucesso!');
    }

    /**
     * Normaliza valores de tipo_evento para os slugs aceitos pela validação.
     */
    private function normalizeTipoEvento(string $valor): string
    {
        $map = [
            'presencial'            => 'presencial',
            'online'                => 'online',
            'hibrido'               => 'hibrido',
            'híbrido'               => 'hibrido',
            'videoconf'             => 'videoconf',
            'videoconf.'            => 'videoconf',
            'videoconferência'      => 'videoconf',
            'videoconferencia'      => 'videoconf',
            'presencial '           => 'presencial',
        ];

        $v = trim(mb_strtolower($valor, 'UTF-8'));
        return $map[$v] ?? $v;
    }
}
