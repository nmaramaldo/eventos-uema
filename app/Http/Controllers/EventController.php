<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Event::class);

        $q      = trim((string) $request->query('q'));
        
        $query = Event::query();

        if ($q !== '') {
            $query->where(fn($w) => $w
                ->where('nome', 'like', "%{$q}%")
                ->orWhere('descricao', 'like', "%{$q}%"));
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

        // cria sempre como rascunho
        $data['status'] = 'rascunho';

        if (Schema::hasColumn('eventos', 'owner_id')) {
            $data['owner_id'] = $data['owner_id'] ?? auth()->id();
        }

        $data['tipo_pagamento'] = $request->input('tipo_pagamento', 'gratis');
        $data['detalhes_pagamento'] = $request->input('detalhes_pagamento', null);

        $event = Event::create($data);

        if ($request->hasFile('logomarca')) {
            $path = $request->file('logomarca')->store('banners/' . $event->id, 'public');
            $event->update(['logomarca_path' => $path]);
        }

        return redirect()
            ->route('eventos.programacao.create', $event)
            ->with('success', 'Evento criado como rascunho. Agora adicione a programação.');
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

        $evento->load('programacao'); // Load the programacao relationship

        $data = $request->validated();

        // Atualiza todos os campos EXCETO status primeiro
        $evento->fill(collect($data)->except(['status'])->all());
        $evento->save();

        // --- NORMALIZA o status pedido (aceita várias formas) ---
        $statusRaw = $data['status'] ?? null;

        if ($statusRaw === null) {
            // aceita checkboxes/alternativos do form
            if ($request->boolean('publicar') || $request->boolean('is_publicado') || $request->boolean('ativo')) {
                $statusRaw = 'publicado';
            } elseif ($request->boolean('rascunho')) {
                $statusRaw = 'rascunho';
            }
        }

        if (is_string($statusRaw)) {
            $statusRaw = strtolower(trim($statusRaw));
            // mapear valores comuns
            if (in_array($statusRaw, ['on', 'true', '1'], true)) {
                $statusRaw = 'publicado';
            }
            if (in_array($statusRaw, ['off', 'false', '0'], true)) {
                $statusRaw = 'rascunho';
            }
        }

        
        if (in_array($statusRaw, ['publicado', 'rascunho', 'ativo'], true)) {
            // Se o evento não tiver programação, ele não pode ser publicado.
            // Força o status para 'rascunho' se tentar publicar sem programação.
            if ($statusRaw === 'publicado' && $evento->programacao->isEmpty()) {
                $statusRaw = 'rascunho';
                
            }

            
            DB::table('eventos')
                ->where('id', $evento->id)
                ->update(['status' => 'rascunho']);

            $evento->refresh();
        }

        return redirect()->route('eventos.show', $evento)->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy(Event $evento)
    {
        $this->authorize('delete', $evento);

        if ($evento->inscricoes()->exists()) {
            return redirect()->route('eventos.index')->with('error', 'Não é possível remover um evento que já possui participantes inscritos.');
        }

        $evento->delete();
        return redirect()->route('eventos.index')->with('success', 'Evento removido.');
    }

    protected function normalizeTipoEvento(string $tipo): string
    {
        $tipo  = strtolower(trim($tipo));
        $valid = ['presencial', 'online', 'hibrido', 'videoconf'];
        return in_array($tipo, $valid, true) ? $tipo : 'presencial';
    }

    public function publish(Event $evento)
    {
        // 1. Autoriza a ação (reutiliza a permissão de 'update' da EventPolicy)
        $this->authorize('update', $evento);

        // 2. Altera o status do evento para 'publicado'
        $evento->status = 'publicado';
        $evento->save();

        // 3. Redireciona de volta para a lista com uma mensagem de sucesso
        return redirect()->route('eventos.index')
                         ->with('success', 'Evento "' . $evento->nome . '" foi publicado com sucesso!');
    }
    
}
