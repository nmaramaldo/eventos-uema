<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\Local;
use App\Models\Palestrante;
use App\Models\Programacao;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    // =======================================================================
    // ✅ MÉTODOS DO NOVO FLUXO DE CRIAÇÃO (3 PASSOS)
    // =======================================================================

    /**
     * PASSO 1: Mostra o formulário de Informações Gerais e Inscrições.
     */
    

// ... (seus outros métodos como index, edit, etc., continuam iguais)

// ✅ =======================================================================
// MÉTODOS DO FLUXO DE CRIAÇÃO ATUALIZADOS
// =======================================================================

    public function createStep1()
    {
        $this->authorize('create', \App\Models\Event::class);
        $eventData = session('event_creation_data', []);
        return view('eventos.create-step-1', compact('eventData'));
    }

    public function storeStep1(Request $request)
    {
        $this->authorize('create', \App\Models\Event::class);
        
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'tipo_classificacao' => 'required|string',
            'area_tematica' => 'required|string',
            'data_inicio_evento' => 'required|date',
            'data_fim_evento' => 'required|date|after_or_equal:data_inicio_evento',
            'data_inicio_inscricao' => 'required|date',
            'data_fim_inscricao' => 'required|date|after_or_equal:data_inicio_inscricao',
            'tipo_evento' => 'required|string',
            'logomarca' => 'nullable|image|mimes:jpeg,png|max:5120', // Validação de imagem
            'status' => 'required|string',
            'vagas' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('logomarca')) {
            $path = $request->file('logomarca')->store('temp_banners', 'public');
            $validatedData['logomarca_path'] = $path;
            unset($validatedData['logomarca']); 
        }

        $data = array_merge(session('event_creation_data', []), $validatedData);
        session()->put('event_creation_data', $data);

        return redirect()->route('eventos.create.step2');
    }

    public function createStep2()
    {
        $this->authorize('create', \App\Models\Event::class);
        $eventData = session('event_creation_data', []);
        $locais = \App\Models\Local::orderBy('nome')->get();
        return view('eventos.create-step-2', compact('eventData', 'locais'));
    }

    public function storeStep2(Request $request)
    {
        $this->authorize('create', \App\Models\Event::class);

        $validatedData = $request->validate([
            'atividades' => 'nullable|array',
            'atividades.*.titulo' => 'required_with:atividades|string',
            'atividades.*.descricao' => 'nullable|string',
            'atividades.*.modalidade' => 'required_with:atividades|string',
            'atividades.*.data_hora_inicio' => 'required_with:atividades|date',
            'atividades.*.data_hora_fim' => 'required_with:atividades|date|after_or_equal:atividades.*.data_hora_inicio',
            'atividades.*.localidade' => 'required_with:atividades|string',
            'atividades.*.capacidade' => 'nullable|integer|min:0',
            'atividades.*.requer_inscricao' => 'required_with:atividades|boolean',
        ]);

        $data = array_merge(session('event_creation_data', []), $validatedData);
        session()->put('event_creation_data', $data);

        return redirect()->route('eventos.create.step3');
    }

    public function createStep3()
    {
        $this->authorize('create', \App\Models\Event::class);
        $eventData = session('event_creation_data', []);
        return view('eventos.create-step-3', compact('eventData'));
    }

    public function storeStep3(Request $request)
    {
        $this->authorize('create', \App\Models\Event::class);

        // Validação de múltiplos arquivos é complexa em Blade. 
        // Por simplicidade, validamos os campos de texto aqui.
        $validatedData = $request->validate([
            'palestrantes' => 'nullable|array',
            'palestrantes.*.nome' => 'required_with:palestrantes|string',
            'palestrantes.*.email' => 'nullable|email',
            'palestrantes.*.biografia' => 'nullable|string',
        ]);
        
        // Une todos os dados da sessão
        $data = array_merge(session('event_creation_data', []), $validatedData);

        $evento = \Illuminate\Support\Facades\DB::transaction(function () use ($data, $request) {
            $evento = \App\Models\Event::create($data);

            if (isset($data['logomarca_path'])) {
                $newPath = str_replace('temp_banners', 'banners/' . $evento->id, $data['logomarca_path']);
                \Illuminate\Support\Facades\Storage::disk('public')->move($data['logomarca_path'], $newPath);
                $evento->logomarca_path = $newPath;
                $evento->save();
            }

            if (!empty($data['atividades'])) {
                $evento->programacao()->createMany($data['atividades']);
            }
            
            if (!empty($data['palestrantes'])) {
                $palestranteIds = [];
                foreach ($data['palestrantes'] as $palestranteData) {
                    $palestrante = \App\Models\Palestrante::updateOrCreate(
                        ['nome' => $palestranteData['nome']],
                        $palestranteData
                    );
                    $palestranteIds[] = $palestrante->id;
                }
                $evento->palestrantes()->sync($palestranteIds);
            }
            return $evento;
        });

        session()->forget('event_creation_data');

        return redirect()->route('eventos.index')->with('success', 'Evento criado com sucesso!');
    }


    // =======================================================================
    // MÉTODOS ANTIGOS QUE FORAM MANTIDOS (NÃO MEXER)
    // =======================================================================
    
    public function index(Request $request)
    {
        $this->authorize('viewAny', Event::class);
        $query = Event::query();
        // ... sua lógica de busca e paginação ...
        $eventos = $query->latest()->paginate(15);
        return view('eventos.index', compact('eventos'));
    }

    public function show(Event $evento)
    {
        $this->authorize('view', $evento);
        // ... sua lógica para carregar relacionamentos ...
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
        // ... sua lógica de atualização, que ainda pode usar o persistProgramacaoDoRequest ...
        DB::transaction(function () use ($evento, $data, $request) {
            $evento->update($data);
            $this->persistProgramacaoDoRequest($request, $evento); // O update continua funcionando como antes
        });
        return redirect()->route('eventos.show', $evento)->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy(Event $evento)
    {
        $this->authorize('delete', $evento);
        $evento->delete();
        return redirect()->route('eventos.index')->with('success', 'Evento removido.');
    }

    /**
     * Helper antigo, mantido para o método update() continuar funcionando.
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
        $colsDetalhe = Schema::getColumnListing('programacao');

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
