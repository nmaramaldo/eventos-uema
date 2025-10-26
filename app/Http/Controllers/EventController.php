<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\Local;
use App\Models\Palestrante;
use App\Models\Programacao;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{

    // =========================================================
    // CRUD PADRÃO
    // =========================================================

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
        return view('eventos.create');
    }

    public function store(StoreEventRequest $request)
    {
        $this->authorize('create', Event::class);
        $validated = $request->validated();

        $validated['tipo_evento'] = $this->normalizeTipoEvento($validated['tipo_evento']);

        $event = Event::create($validated);

        if ($request->hasFile('logomarca')) {
            $path = $request->file('logomarca')->store('banners/' . $event->id, 'public');
            $event->update(['logomarca_path' => $path]);
        }

        return redirect()->route('eventos.programacao.create', $event)->with('success', 'Evento criado com sucesso!');
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
