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
    // FLUXO EM 3 PASSOS (CREATE)
    // =========================================================

    public function createStep1()
    {
        $this->authorize('create', Event::class);
        $eventId = session('event_creation_id');
        $eventData = [];
        if ($eventId) {
            $event = Event::find($eventId);
            if ($event) {
                $eventData = $event->toArray();
            } else {
                session()->forget('event_creation_id');
            }
        }
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

        $eventData = $this->extractEventColumns($validated, true);

        $eventId = session('event_creation_id');
        if ($eventId) {
            $event = Event::find($eventId);
            $event->update($eventData);
        } else {
            $event = Event::create($eventData);
            session()->put('event_creation_id', $event->id);
        }

        if ($request->hasFile('logomarca')) {
            $path = $request->file('logomarca')->store('temp_banners', 'public');
            session()->put('event_creation_logomarca_tmp', $path);
        }

        return redirect()->route('eventos.create.step2');
    }

    public function createStep2()
    {
        $this->authorize('create', Event::class);
        $eventId = session('event_creation_id');
        if (!$eventId) {
            return redirect()->route('eventos.create.step1')->with('error', 'Sessão expirada, por favor comece novamente.');
        }
        $event = Event::with('programacao')->find($eventId);
        if (!$event) {
            session()->forget('event_creation_id');
            return redirect()->route('eventos.create.step1')->with('error', 'Evento não encontrado, por favor comece novamente.');
        }

        $eventData = $event->toArray();
        $eventData['atividades'] = $event->programacao->toArray();

        $locais    = Local::orderBy('nome')->get();
        return view('eventos.create-step-2', compact('eventData', 'locais'));
    }

    public function storeStep2(Request $request)
    {
        $this->authorize('create', Event::class);
        $eventId = session('event_creation_id');
        if (!$eventId) {
            return redirect()->route('eventos.create.step1')->with('error', 'Evento não encontrado. Por favor, inicie novamente.');
        }

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

        DB::transaction(function () use ($eventId, $validated) {
            Programacao::where('evento_id', $eventId)->delete();
            if (!empty($validated['atividades']) && is_array($validated['atividades'])) {
                foreach ($validated['atividades'] as $a) {
                    $attrs = $this->buildProgramacaoAttrs($eventId, $a);
                    if (!empty($attrs['titulo'])) {
                        Programacao::create($attrs);
                    }
                }
            }
        });

        return redirect()->route('eventos.create.step3');
    }

    public function createStep3()
    {
        $this->authorize('create', Event::class);
        $eventId = session('event_creation_id');
        if (!$eventId) {
            return redirect()->route('eventos.create.step1')->with('error', 'Sessão expirada, por favor comece novamente.');
        }
        $event = Event::with('palestrantes')->find($eventId);
        if (!$event) {
            session()->forget('event_creation_id');
            return redirect()->route('eventos.create.step1')->with('error', 'Evento não encontrado, por favor comece novamente.');
        }

        $eventData = $event->toArray();
        $eventData['palestrantes'] = $event->palestrantes->toArray();

        return view('eventos.create-step-3', compact('eventData'));
    }

    public function storeStep3(Request $request)
    {
        $this->authorize('create', Event::class);
        $eventId = session('event_creation_id');
        $evento = Event::find($eventId);

        if (!$evento) {
            return redirect()->route('eventos.create.step1')->with('error', 'Evento não encontrado. Por favor, inicie novamente.');
        }

        $validated = $request->validate([
            'palestrantes'             => 'nullable|array',
            'palestrantes.*.id'        => 'nullable|string',
            'palestrantes.*.nome'      => 'required_with:palestrantes|string|max:255',
            'palestrantes.*.email'     => 'nullable|email|max:255',
            'palestrantes.*.biografia' => 'nullable|string',
        ]);

        DB::transaction(function () use ($evento, $validated) {
            // Palestrantes
            $attach = [];
            if (!empty($validated['palestrantes']) && is_array($validated['palestrantes'])) {
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
            }

            if (method_exists($evento, 'palestrantes')) {
                $evento->palestrantes()->sync($attach);
            }

            // Mover a logomarca que estava temporária
            $logomarcaTmpPath = session('event_creation_logomarca_tmp');
            if ($logomarcaTmpPath && Storage::disk('public')->exists($logomarcaTmpPath)) {
                $newPath = 'banners/'.$evento->id.'/'.basename($logomarcaTmpPath);
                Storage::disk('public')->makeDirectory('banners/'.$evento->id);
                Storage::disk('public')->move($logomarcaTmpPath, $newPath);
                $evento->logomarca_path = $newPath;
                $evento->save();
            }
        });

        session()->forget(['event_creation_id', 'event_creation_logomarca_tmp']);

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

    /**
     * Constrói o array de atributos para Programacao
     * de acordo com as colunas REALMENTE existentes.
     */
    private function buildProgramacaoAttrs(string $eventoId, array $a): array
    {
        $t      = 'programacao';
        $titulo = trim((string)($a['titulo'] ?? ''));
        $desc   = $a['descricao']   ?? null;
        $mod    = $a['modalidade']  ?? null;

        // origem do horário (create-step usa data_hora_inicio/fim)
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

        // Mapeia colunas de data/hora conforme o schema
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

        // Local: usamos 'localidade' (texto) se existir; senão tenta local_id
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

        $q      = trim((string) $request->query('q', ''));
        $status = $request->query('status');

        $query = Event::query();

        if ($q !== '') {
            $query->where(fn ($w) => $w
                ->where('nome', 'like', "%{$q}%")
                ->orWhere('descricao', 'like', "%{$q}%"));
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $eventos = $query->latest('created_at')->paginate(15)->withQueryString();

        return view('eventos.index', compact('eventos'));
    }

    public function show(Event $evento)
    {
        $this->authorize('view', $evento);

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

        DB::transaction(function () use ($evento, $data, $request) {
            $evento->update($data);

            // Recria programação usando o mesmo helper dinâmico
            $evento->programacao()->delete();
            foreach ((array)$request->input('atividades', []) as $a) {
                $attrs = $this->buildProgramacaoAttrs($evento->id, $a);
                if (!empty($attrs['titulo'])) {
                    Programacao::create($attrs);
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