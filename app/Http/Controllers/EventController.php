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
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-users');

        $q      = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        $query = Event::query();

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('nome', 'like', "%{$q}%")
                    ->orWhere('descricao', 'like', "%{$q}%");
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $eventos = $query->latest('created_at')->paginate(15)->withQueryString();

        return view('eventos.index', compact('eventos'));
    }

    public function create()
    {
        Gate::authorize('manage-users');

        $coordenadores = User::orderBy('name')->get();
        // mantém o formulário novo
        return view('eventos.create', compact('coordenadores'));
    }

    public function store(StoreEventRequest $request)
    {
        Gate::authorize('manage-users');

        $data = $request->validated();

        // Upload de capa (campo "capa" -> salva URL pública em logomarca_url)
        if ($request->hasFile('capa')) {
            $path = $request->file('capa')->store('capas', 'public');
            $data['logomarca_url'] = Storage::url($path);
        }

        if (!empty($data['tipo_evento'])) {
            $data['tipo_evento'] = $this->normalizeTipoEvento($data['tipo_evento']);
        }

        $data['status'] = $data['status'] ?? 'rascunho';

        if (empty($data['coordenador_id']) && auth()->check()) {
            $data['coordenador_id'] = auth()->id();
        }

        $evento = DB::transaction(function () use ($data, $request) {
            $evento = Event::create($data);
            $this->persistProgramacaoDoRequest($request, $evento);
            return $evento;
        });

        return redirect()
            ->route('eventos.programacao.create', $evento);
    }

    public function show(Event $evento)
    {
        Gate::authorize('manage-users');

        $evento->load([
            'coordenador',
            'inscricoes',
            'palestrantes',
            'detalhes' => fn($q) => $q->ordenado(),
        ]);

        $relacionados = Event::where('id', '!=', $evento->id)
            ->when($evento->area_tematica, fn($q) => $q->where('area_tematica', $evento->area_tematica))
            ->whereIn('status', ['ativo', 'publicado'])
            ->orderBy('data_inicio_evento', 'asc')
            ->take(6)
            ->get();

        return view('front.event-show', compact('evento', 'relacionados'));
    }

    public function edit(Event $evento)
    {
        Gate::authorize('manage-users');

        $coordenadores = User::orderBy('name')->get();
        // mantém o formulário novo também na edição
        return view('eventos.edit', compact('evento', 'coordenadores'));
    }

    public function update(UpdateEventRequest $request, Event $evento)
    {
        Gate::authorize('manage-users');

        $data = $request->validated();

        if ($request->hasFile('capa')) {
            $path = $request->file('capa')->store('capas', 'public');
            $data['logomarca_url'] = Storage::url($path);
        }

        if (array_key_exists('tipo_evento', $data) && !empty($data['tipo_evento'])) {
            $data['tipo_evento'] = $this->normalizeTipoEvento($data['tipo_evento']);
        }

        if (!array_key_exists('status', $data) || $data['status'] === null || $data['status'] === '') {
            $data['status'] = $evento->status ?? 'rascunho';
        }

        if (empty($data['coordenador_id']) && auth()->check()) {
            $data['coordenador_id'] = auth()->id();
        }

        DB::transaction(function () use ($evento, $data, $request) {
            $evento->update($data);
            $this->persistProgramacaoDoRequest($request, $evento);
        });

        return redirect()
            ->route('eventos.show', $evento)
            ->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy(Event $evento)
    {
        Gate::authorize('manage-users');

        $evento->delete();

        return redirect()
            ->route('eventos.index')
            ->with('success', 'Evento removido.');
    }

    /* =======================================================================
     * Helpers
     * ======================================================================= */

    /**
     * Persiste locais, palestrantes e atividades (programação) vindos do request.
     * Este método é tolerante ao schema: só envia colunas que realmente existem.
     */
    protected function persistProgramacaoDoRequest(Request $request, Event $evento): void
    {
        $localIdMap   = [];
        $localNameMap = [];

        /* -------- LOCAIS -------- */
        foreach ((array) $request->input('locais', []) as $idx => $l) {
            $nome = trim($l['nome'] ?? '');
            if ($nome === '') {
                continue;
            }

            $attrs = ['nome' => $nome];
            if (Schema::hasColumn('locais', 'evento_id')) {
                $attrs['evento_id'] = $evento->id;
            }

            $local = Local::firstOrCreate($attrs, $attrs);

            $key = "row{$idx}";
            $localIdMap[$key]   = $local->id;
            $localNameMap[$key] = $local->nome;
        }

        /* -------- PALESTRANTES -------- */
        $palestrantesIds = [];
        foreach ((array) $request->input('palestrantes', []) as $p) {
            $nome = trim($p['nome'] ?? '');
            if ($nome === '') {
                continue;
            }

            $email = trim((string) ($p['email'] ?? ''));

            $pal = null;
            if ($email !== '') {
                $pal = Palestrante::firstOrCreate(
                    ['email' => $email],
                    [
                        'nome'     => $nome,
                        'cargo'    => $p['cargo']    ?? null,
                        'mini_bio' => $p['mini_bio'] ?? null,
                        'foto_url' => $p['foto_url'] ?? null,
                    ]
                );
            }
            if (!$pal) {
                $pal = Palestrante::firstOrCreate(
                    ['nome' => $nome],
                    [
                        'email'    => $email ?: null,
                        'cargo'    => $p['cargo']    ?? null,
                        'mini_bio' => $p['mini_bio'] ?? null,
                        'foto_url' => $p['foto_url'] ?? null,
                    ]
                );
            }

            $palestrantesIds[] = $pal->id;
        }
        if (!empty($palestrantesIds) && method_exists($evento, 'palestrantes')) {
            $evento->palestrantes()->syncWithoutDetaching($palestrantesIds);
        }

        /* -------- ATIVIDADES (Programacao) -------- */
        $colsDetalhe = Schema::getColumnListing('eventos_detalhes');

        foreach ((array) $request->input('atividades', []) as $a) {
            $titulo = trim($a['titulo'] ?? '');
            if ($titulo === '') {
                continue;
            }

            $inicio = $a['inicio'] ?? null;
            $fim    = $a['fim']    ?? null;

            $attrs = [
                'evento_id'   => $evento->id,
                'descricao'   => $a['descricao'] ?? $titulo,                                  // tabela usa 'descricao'
                'modalidade'  => $a['tipo'] ?? ($a['modalidade'] ?? null),                    // tabela usa 'modalidade'
                'data'        => $inicio ? Carbon::parse($inicio)->toDateString() : null,
                'hora_inicio' => $inicio ? Carbon::parse($inicio)->format('H:i:s') : null,
                'hora_fim'    => $fim    ? Carbon::parse($fim)->format('H:i:s')    : null,
            ];

            // Local: se existir 'local_id' usa id; senão, se existir 'localidade', usa nome
            $localKey = $a['local_key'] ?? null;
            if ($localKey) {
                if (in_array('local_id', $colsDetalhe)) {
                    $attrs['local_id'] = $localIdMap[$localKey] ?? null;
                } elseif (in_array('localidade', $colsDetalhe)) {
                    $attrs['localidade'] = $localNameMap[$localKey] ?? null;
                }
            }

            if (in_array('capacidade', $colsDetalhe)) {
                $attrs['capacidade'] = $a['capacidade'] ?? null;
            }

            if (in_array('requer_inscricao', $colsDetalhe)) {
                $attrs['requer_inscricao'] = (bool)($a['requer_inscricao'] ?? false);
            }

            Programacao::create($attrs);
        }
    }

    protected function normalizeTipoEvento(string $tipo): string
    {
        $tipo  = strtolower(trim($tipo));
        $valid = ['presencial', 'online', 'hibrido', 'videoconf'];
        return in_array($tipo, $valid, true) ? $tipo : 'presencial';
    }
}
