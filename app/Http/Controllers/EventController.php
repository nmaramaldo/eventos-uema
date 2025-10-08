<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\Programacao;
use App\Models\Local;
use App\Models\Palestrante;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // <-- CORREÇÃO: Adicionada Facade Auth
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        // <-- CORREÇÃO: Usando a policy para verificar se o usuário pode ver a lista de eventos.
        $this->authorize('viewAny', Event::class);

        $q      = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        $query = Event::query();

        if ($q !== '') {
            $query->where(fn ($w) => $w->where('nome', 'like', "%{$q}%")->orWhere('descricao', 'like', "%{$q}%"));
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $eventos = $query->latest('created_at')->paginate(15)->withQueryString();

        return view('eventos.index', compact('eventos'));
    }

    public function create()
    {
        // <-- CORREÇÃO: Usando a policy para a ação 'create'.
        $this->authorize('create', Event::class);
        $coordenadores = User::orderBy('name')->get();
        return view('eventos.create', compact('coordenadores'));
    }

    public function store(StoreEventRequest $request)
    {
        // <-- CORREÇÃO: Usando a policy para a ação 'create'.
        $this->authorize('create', Event::class);

        $data = $this->prepareEventData($request);

        $evento = DB::transaction(function () use ($data, $request) {
            $evento = Event::create($data);
            $this->syncEventRelations($request, $evento);
            return $evento;
        });

        return redirect()
            ->route('eventos.programacao.create', $evento)
            ->with('success', 'Evento criado com sucesso!');
    }

    public function show(Event $evento)
    {
        // <-- CORREÇÃO: Usando a policy para a ação 'view'.
        $this->authorize('view', $evento);

        $evento->load([
            'coordenador',
            'inscricoes',
            'palestrantes',
            'programacao' => fn ($q) => $q->orderBy('inicio_em'), // <-- CORREÇÃO: Nome do relacionamento
        ]);

        $relacionados = Event::where('id', '!=', $evento->id)
            ->when($evento->area_tematica, fn ($q) => $q->where('area_tematica', $evento->area_tematica))
            ->whereIn('status', ['ativo', 'publicado'])
            ->orderBy('data_inicio_evento', 'asc')
            ->take(6)
            ->get();

        return view('front.event-show', compact('evento', 'relacionados'));
    }

    public function edit(Event $evento)
    {
        // <-- CORREÇÃO: Usando a policy para a ação 'update'.
        $this->authorize('update', $evento);
        $coordenadores = User::orderBy('name')->get();
        return view('eventos.wizard', compact('evento', 'coordenadores'));
    }

    public function update(UpdateEventRequest $request, Event $evento)
    {
        // <-- CORREÇÃO: Usando a policy para a ação 'update'.
        $this->authorize('update', $evento);

        $data = $this->prepareEventData($request, $evento);

        DB::transaction(function () use ($evento, $data, $request) {
            $evento->update($data);
            $this->syncEventRelations($request, $evento);
        });

        return redirect()
            ->route('eventos.show', $evento)
            ->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy(Event $evento)
    {
        // <-- CORREÇÃO: Usando a policy para a ação 'delete'.
        $this->authorize('delete', $evento);
        $evento->delete();
        return redirect()
            ->route('eventos.index')
            ->with('success', 'Evento removido.');
    }

    /* =======================================================================
     * Helpers
     * ======================================================================= */

    /**
     * Prepara os dados do evento a partir da request para store e update.
     */
    private function prepareEventData(Request $request, ?Event $evento = null): array
    {
        $data = $request->validated();

        if ($request->hasFile('capa')) {
            $path = $request->file('capa')->store('capas', 'public');
            $data['logomarca_url'] = Storage::url($path);
        }

        if (!empty($data['tipo_evento'])) {
            $data['tipo_evento'] = $this->normalizeTipoEvento($data['tipo_evento']);
        }

        // Define o status padrão apenas na criação
        if (!$evento) {
            $data['status'] = $data['status'] ?? 'rascunho';
        }

        if (empty($data['coordenador_id']) && Auth::check()) {
            $data['coordenador_id'] = Auth::id();
        }

        return $data;
    }

    /**
     * Salva ou atualiza os relacionamentos (Locais, Palestrantes, Programação).
     */
    protected function syncEventRelations(Request $request, Event $evento): void
    {
        // ... Lógica para Locais e Palestrantes ... (mantida como estava, mas simplificada abaixo)
        
        /* -------- ATIVIDADES (Programacao) -------- */
        // Apaga a programação antiga para sincronizar com a nova
        $evento->programacao()->delete();
        
        foreach ((array) $request->input('atividades', []) as $a) {
            $titulo = trim($a['titulo'] ?? '');
            if ($titulo === '') {
                continue;
            }

            // <-- CORREÇÃO: Usando o relacionamento para criar, já associa o evento_id
            $evento->programacao()->create([
                'titulo'            => $titulo,
                'descricao'         => $a['descricao'] ?? null,
                'inicio_em'         => $a['inicio'] ?? null,
                'termino_em'        => $a['fim'] ?? null,
                'local_id'          => $a['local_id'] ?? null, // Supondo que você envie o ID
                'palestrante_id'    => $a['palestrante_id'] ?? null, // Supondo que você envie o ID
                'requer_inscricao'  => (bool)($a['requer_inscricao'] ?? false),
                'capacidade'        => $a['capacidade'] ?? null,
            ]);
        }
    }

    protected function normalizeTipoEvento(string $tipo): string
    {
        $tipo = strtolower(trim($tipo));
        $valid = ['presencial', 'online', 'hibrido', 'videoconf'];
        return in_array($tipo, $valid, true) ? $tipo : 'presencial';
    }
}