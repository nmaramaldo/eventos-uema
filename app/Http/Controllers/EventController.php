<?php

namespace App\Http\Controllers;

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
    // FLUXO EM 3 PASSOS (CREATE) — salvando a cada passo
    // =========================================================

    public function createStep1()
    {
        $this->authorize('create', Event::class);

        // Se já existe rascunho na sessão, traz os dados do banco para repovoar o formulário
        $draftId   = session('event_draft_id');
        $draftData = [];
        if ($draftId && ($draft = Event::find($draftId))) {
            $draftData = $draft->only([
                'nome','descricao','tipo_classificacao','area_tematica',
                'data_inicio_evento','data_fim_evento',
                'data_inicio_inscricao','data_fim_inscricao',
                'tipo_evento','status','vagas'
            ]);
        }

        $eventData = array_merge(session('event_creation_data', []), $draftData);
        return view('eventos.create-step-1', compact('eventData'));
    }

    public function storeStep1(Request $request)
    {
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'nome'                   => 'required|string|max:255',
            'descricao'              => 'required|string',
            'tipo_classificacao'     => 'required|string|max:255',
            'area_tematica'          => 'required|string|max:255',
            'data_inicio_evento'     => 'required|date',
            'data_fim_evento'        => 'required|date|after_or_equal:data_inicio_evento',
            'data_inicio_inscricao'  => 'required|date',
            'data_fim_inscricao'     => 'required|date|after_or_equal:data_inicio_inscricao',
            'tipo_evento'            => 'required|string|max:50',
            'logomarca'              => 'nullable|image|mimes:jpeg,png,gif|max:5120',
            'status'                 => 'required|string|max:50',
            'vagas'                  => 'nullable|integer|min:0',
        ]);

        // Master só pode 'ativo' ou 'encerrado'
        $user = auth()->user();
        $tipo = $user?->tipo_usuario instanceof \BackedEnum ? $user->tipo_usuario->value : (string)($user->tipo_usuario ?? '');
        if ($tipo === 'master') {
            $validated['status'] = in_array($validated['status'], ['ativo','encerrado'], true)
                ? $validated['status']
                : 'ativo';
        }

        // Cria ou atualiza rascunho no banco (progresso fixo)
        $draftId = session('event_draft_id');

        $evento = DB::transaction(function () use ($validated, $request, $draftId) {

            // Se já existe rascunho, atualiza; senão cria
            if ($draftId && ($e = Event::find($draftId))) {
                $e->update($this->extractEventColumns($validated, true));
                $evento = $e;
            } else {
                // status padrão rascunho se não for master/publicado
                $dados = $this->extractEventColumns($validated, true);
                if (!isset($dados['status']) || $dados['status'] === '') {
                    $dados['status'] = 'rascunho';
                }
                $evento = Event::create($dados);
            }

            // Se foi enviada logomarca, salva direto em /banners/{id}
            if ($request->hasFile('logomarca')) {
                $path = $request->file('logomarca')->store('banners/'.$evento->id, 'public');
                $evento->logomarca_path = $path;
                $evento->save();
            }

            return $evento;
        });

        // Guarda o id do rascunho para os próximos passos
        session()->put('event_draft_id', $evento->id);

        // Mantém dados em sessão só para repovoar (sem arquivo)
        $data = array_merge(session('event_creation_data', []), collect($validated)->except('logomarca')->toArray());
        session()->put('event_creation_data', $data);

        return redirect()->route('eventos.create.step2');
    }

    public function createStep2()
    {
        $this->authorize('create', Event::class);

        $draftId = session('event_draft_id');
        if (!$draftId || !Event::find($draftId)) {
            // Se não tem rascunho válido, volta ao passo 1
            return redirect()->route('eventos.create.step1')
                ->with('success', 'Retomamos a criação do evento. Preencha o passo 1.');
        }

        $eventData = session('event_creation_data', []);
        $locais    = Local::orderBy('nome')->get();
        return view('eventos.create-step-2', compact('eventData', 'locais'));
    }

    public function storeStep2(Request $request)
    {
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'atividades'                        => 'nullable|array',
            'atividades.*.titulo'               => 'required_with:atividades|string|max:255',
            'atividades.*.descricao'            => 'nullable|string',
            'atividades.*.modalidade'           => 'nullable|string|max:100',
            'atividades.*.data_hora_inicio'     => 'required_with:atividades|date',
            'atividades.*.data_hora_fim'        => 'required_with:atividades|date|after_or_equal:atividades.*.data_hora_inicio',
            'atividades.*.localidade'           => 'nullable|string|max:255',
            'atividades.*.capacidade'           => 'nullable|integer|min:0',
            'atividades.*.requer_inscricao'     => 'nullable|boolean',
        ]);

        $draftId = session('event_draft_id');
        if (!$draftId || !($evento = Event::find($draftId))) {
            return redirect()->route('eventos.create.step1')->with('success', 'Retomamos a criação do evento.');
        }

        // Atualiza PROGRAMAÇÃO do rascunho (recria pelo método simples)
        DB::transaction(function () use ($evento, $validated) {
            $evento->programacao()->delete();
            foreach ((array)($validated['atividades'] ?? []) as $a) {
                $attrs = $this->buildProgramacaoAttrs($evento->id, $a);
                if (!empty($attrs['titulo'])) {
                    Programacao::create($attrs);
                }
            }
        });

        // mantém dados em sessão
        $data = array_merge(session('event_creation_data', []), $validated);
        session()->put('event_creation_data', $data);

        return redirect()->route('eventos.create.step3');
    }

    public function createStep3()
    {
        $this->authorize('create', Event::class);

        $draftId = session('event_draft_id');
        if (!$draftId || !Event::find($draftId)) {
            return redirect()->route('eventos.create.step1')
                ->with('success', 'Retomamos a criação do evento. Preencha o passo 1.');
        }

        $eventData = session('event_creation_data', []);
        return view('eventos.create-step-3', compact('eventData'));
    }

    public function storeStep3(Request $request)
    {
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'palestrantes'             => 'nullable|array',
            'palestrantes.*.id'        => 'nullable|string',
            'palestrantes.*.nome'      => 'required_with:palestrantes|string|max:255',
            'palestrantes.*.email'     => 'nullable|email|max:255',
            'palestrantes.*.biografia' => 'nullable|string',
        ]);

        $draftId = session('event_draft_id');
        if (!$draftId || !($evento = Event::find($draftId))) {
            return redirect()->route('eventos.create.step1')->with('success', 'Retomamos a criação do evento.');
        }

        // Atualiza palestrantes do rascunho
        DB::transaction(function () use ($evento, $validated) {
            if (!empty($validated['palestrantes']) && is_array($validated['palestrantes'])) {
                $attach = [];
                foreach ($validated['palestrantes'] as $p) {
                    $id    = trim((string)($p['id']    ?? ''));
                    $nome  = trim((string)($p['nome']  ?? ''));
                    $email = trim((string)($p['email'] ?? ''));

                    if ($id !== '') { $attach[] = $id; continue; }

                    if ($email === '') {
                        $email = Str::uuid().'@placeholder.local';
                    }

                    $pal = Palestrante::firstOrCreate(
                        ['email' => $email],
                        ['nome' => $nome ?: $email, 'biografia' => $p['biografia'] ?? null]
                    );

                    $attach[] = $pal->id;
                }

                if (!empty($attach) && method_exists($evento, 'palestrantes')) {
                    $evento->palestrantes()->sync($attach);
                }
            }
        });

        // Finaliza fluxo (limpa sessão de criação)
        session()->forget('event_creation_data');
        session()->forget('event_draft_id');

        return redirect()->route('eventos.index')->with('success', 'Evento criado com sucesso!');
    }

    private function extractEventColumns(array $data, bool $normalizeTipo = false): array
    {
        $out = [
            'nome'                  => $data['nome']                  ?? null,
            'descricao'             => $data['descricao']             ?? null,
            'tipo_classificacao'    => $data['tipo_classificacao']    ?? null,
            'area_tematica'         => $data['area_tematica']         ?? null,
            'data_inicio_evento'    => $data['data_inicio_evento']    ?? null,
            'data_fim_evento'       => $data['data_fim_evento']       ?? null,
            'data_inicio_inscricao' => $data['data_inicio_inscricao'] ?? null,
            'data_fim_inscricao'    => $data['data_fim_inscricao']    ?? null,
            'tipo_evento'           => $data['tipo_evento']           ?? null,
            'status'                => $data['status']                ?? 'rascunho',
            'vagas'                 => $data['vagas']                 ?? null,
        ];

        if ($normalizeTipo && !empty($out['tipo_evento'])) {
            $out['tipo_evento'] = $this->normalizeTipoEvento($out['tipo_evento']);
        }

        return $out;
    }

    private function buildProgramacaoAttrs(string $eventoId, array $a): array
    {
        $t      = 'programacao';
        $titulo = trim((string)($a['titulo'] ?? ''));
        $desc   = $a['descricao']   ?? null;
        $mod    = $a['modalidade']  ?? null;

        $inicioRaw = $a['inicio'] ?? ($a['data_hora_inicio'] ?? null);
        $fimRaw    = $a['fim']    ?? ($a['data_hora_fim']    ?? null);

        $inicio = $inicioRaw ? Carbon::parse($inicioRaw) : null;
        $fim    = $fimRaw    ? Carbon::parse($fimRaw)    : null;

        $attrs = [
            'evento_id'        => $eventoId,
            'titulo'           => $titulo,
            'descricao'        => $desc,
            'modalidade'       => $mod,
            'capacidade'       => $a['capacidade']         ?? null,
            'requer_inscricao' => !empty($a['requer_inscricao']),
        ];

        if (Schema::hasColumn($t, 'data')) {
            $attrs['data']        = $inicio ? $inicio->toDateString() : null;
            if (Schema::hasColumn($t, 'hora_inicio')) $attrs['hora_inicio'] = $inicio ? $inicio->format('H:i:s') : null;
            if (Schema::hasColumn($t, 'hora_fim'))    $attrs['hora_fim']    = $fim    ? $fim->format('H:i:s')    : null;

        } elseif (Schema::hasColumn($t, 'inicio_em')) {
            $attrs['inicio_em']  = $inicio ? $inicio->toDateTimeString() : null;
            if (Schema::hasColumn($t, 'termino_em')) $attrs['termino_em'] = $fim ? $fim->toDateTimeString() : null;

        } elseif (Schema::hasColumn($t, 'data_hora_inicio')) {
            $attrs['data_hora_inicio'] = $inicio ? $inicio->toDateTimeString() : null;
            if (Schema::hasColumn($t, 'data_hora_fim')) $attrs['data_hora_fim'] = $fim ? $fim->toDateTimeString() : null;
        }

        if (Schema::hasColumn($t, 'localidade')) {
            $attrs['localidade'] = $a['localidade'] ?? (function () use ($a) {
                $lid = trim((string)($a['local_id'] ?? ''));
                if ($lid !== '') {
                    return Local::where('id', $lid)->value('nome');
                }
                return null;
            })();
        } elseif (Schema::hasColumn($t, 'local_id')) {
            $lid = trim((string)($a['local_id'] ?? ''));
            $attrs['local_id'] = $lid !== '' ? $lid : null;
        }

        return $attrs;
    }

    // =========================================================
    // CRUD PADRÃO
    // =========================================================

    public function index(Request $request)
    {
        $this->authorize('viewAny', Event::class);

        // Auto-encerramento em massa (ignora cancelados)
        Event::whereNotNull('data_fim_evento')
            ->where('data_fim_evento', '<', now())
            ->whereNotIn('status', ['encerrado','cancelado'])
            ->update(['status' => 'encerrado']);

        $q       = trim((string) $request->query('q', ''));
        $status  = $request->query('status');
        $janela  = $request->query('janela');
        $now     = now();

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

        if ($janela === 'abertas') {
            $query->whereIn('status', ['ativo','publicado'])
                  ->whereNotNull('data_inicio_inscricao')
                  ->whereNotNull('data_fim_inscricao')
                  ->where('data_inicio_inscricao', '<=', $now)
                  ->where('data_fim_inscricao', '>=', $now);
        } elseif ($janela === 'fechadas') {
            $query->where(function ($w) use ($now) {
                $w->whereNull('data_inicio_inscricao')
                  ->orWhereNull('data_fim_inscricao')
                  ->orWhere('data_inicio_inscricao', '>', $now)
                  ->orWhere('data_fim_inscricao', '<', $now);
            });
        }

        $eventos = $query->latest('created_at')->paginate(15)->withQueryString();

        return view('eventos.index', compact('eventos'));
    }

    public function show(Event $evento)
    {
        $this->authorize('view', $evento);

        // Mantém coerência ao exibir
        $evento->ensureStatusUpToDate();

        $evento->load([
            'coordenador',
            'palestrantes',
            'programacao' => fn ($q) => $q->ordenado(),
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

        // Master: força status permitido
        $user = auth()->user();
        $tipo = $user?->tipo_usuario instanceof \BackedEnum ? $user->tipo_usuario->value : (string)($user->tipo_usuario ?? '');
        if ($tipo === 'master') {
            $data['status'] = in_array($data['status'] ?? 'ativo', ['ativo','encerrado'], true)
                ? $data['status']
                : 'ativo';
        }

        DB::transaction(function () use ($evento, $data, $request) {
            $evento->update($data);

            // Recria programação se vier no formulário de edição
            if (is_array($request->input('atividades'))) {
                $evento->programacao()->delete();
                foreach ((array)$request->input('atividades', []) as $a) {
                    $attrs = $this->buildProgramacaoAttrs($evento->id, $a);
                    if (!empty($attrs['titulo'])) {
                        Programacao::create($attrs);
                    }
                }
            }
        });

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
