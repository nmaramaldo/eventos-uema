<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema; // <-- importa

class EventController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Event::class);

        $q      = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        $query = Event::query();

        if ($q !== '') {
            $query->where(fn($w) => $w
                ->where('nome', 'like', "%{$q}%")
                ->orWhere('descricao', 'like', "%{$q}%"));
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $eventos = $query->latest('created_at')->paginate(15)->withQueryString();

        return view('eventos.index', compact('eventos'));
    }

    public function create()
    {
        $this->authorize('create', Event::class);
        $coordenadores = User::orderBy('name')->get();
        return view('eventos.create', compact('coordenadores'));
    }

    public function store(StoreEventRequest $request)
    {
        $this->authorize('create', Event::class);

        $data = $request->validated();
        $data['tipo_evento'] = $this->normalizeTipoEvento($data['tipo_evento']);

        // ✅ só grava owner_id se a coluna existir
        if (Schema::hasColumn('eventos', 'owner_id')) {
            $data['owner_id'] = $data['owner_id'] ?? auth()->id();
        }

        $event = Event::create($data);

        if ($request->hasFile('logomarca')) {
            $path = $request->file('logomarca')->store('banners/' . $event->id, 'public');
            $event->update(['logomarca_path' => $path]);
        }

        return redirect()
            ->route('eventos.programacao.create', $event)
            ->with('success', 'Evento criado com sucesso! Agora adicione a programação.');
    }

    public function show(Event $evento)
    {
        $this->authorize('view', $evento);

        $evento->load([
            'coordenador',
            'palestrantes',
            'programacao' => fn($q) => $q->ordenado(),
        ]);

        return view('front.event-show', compact('evento'));
    }

    public function edit(Event $evento)
    {
        $this->authorize('update', $evento);
        $coordenadores = User::orderBy('name')->get();
        return view('eventos.edit', compact('evento', 'coordenadores'));
    }

    public function update(UpdateEventRequest $request, Event $evento)
    {
        $this->authorize('update', $evento);

        $data = $request->validated();

        if ($evento->programacao()->count() === 0) {
            $data['status'] = 'rascunho';
        }

        $evento->update($data);

        return redirect()->route('eventos.show', $evento)->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy(Event $evento)
    {
        $this->authorize('delete', $evento);
        $evento->delete();
        return redirect()->route('eventos.index')->with('success', 'Evento removido.');
    }

    protected function normalizeTipoEvento(string $tipo): string
    {
        $tipo  = strtolower(trim($tipo));
        $valid = ['presencial', 'online', 'hibrido', 'videoconf'];
        return in_array($tipo, $valid, true) ? $tipo : 'presencial';
    }
}
