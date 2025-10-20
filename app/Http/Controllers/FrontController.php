<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function home()
    {
        $destaques = Event::whereIn('status', ['ativo', 'publicado'])
            ->orderBy('data_inicio_evento', 'asc')
            ->take(8)
            ->get();

        $recentes = Event::latest('created_at')->take(12)->get();

        // Mesmas áreas do passo de criação (create-step-1)
        $areas = [
            'Ciências Humanas',
            'Ciências Sociais',
            'Ciências Exatas e da Terra',
            'Engenharias',
            'Ciências da Saúde',
            'Ciências Agrárias',
            'Linguística, Letras e Artes',
            'Outros',
        ];

        // Ícones por área (classes do Bootstrap Icons)
        // OBS: manter APENAS o sufixo 'bi-...' aqui; na view adicionamos a classe 'bi'.
        $areaIcons = [
            'Ciências Humanas'              => 'bi-book',
            'Ciências Sociais'              => 'bi-people',
            'Ciências Exatas e da Terra'    => 'bi-cpu',
            'Engenharias'                   => 'bi-gear-wide-connected',
            'Ciências da Saúde'             => 'bi-heart-pulse',
            'Ciências Agrárias'             => 'bi-flower3',
            'Linguística, Letras e Artes'   => 'bi-palette',
            'Outros'                        => 'bi-grid-3x3-gap',
        ];

        return view('front.home', compact('destaques', 'recentes', 'areas', 'areaIcons'));
    }

    public function eventos(Request $request)
    {
        $q = Event::query()->whereIn('status', ['ativo', 'publicado']);

        $search = trim((string) ($request->query('s') ?? $request->query('q') ?? ''));
        if ($search !== '') {
            $q->where(function ($w) use ($search) {
                $w->where('nome', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%");
            });
        }

        if ($tipo = $request->query('tipo_evento')) {
            $q->where('tipo_evento', $tipo);
        }
        if ($cat = $request->query('tipo_classificacao')) {
            $q->where('tipo_classificacao', $cat);
        }
        if ($area = $request->query('area_tematica')) {
            $q->where('area_tematica', $area);
        }

        $eventos = $q->orderBy('data_inicio_evento', 'asc')
            ->paginate(12)
            ->withQueryString();

        return view('front.eventos-index', compact('eventos'));
    }

    // Rota /eventos -> reaproveita o mesmo método
    public function index(Request $request)
    {
        return $this->eventos($request);
    }

    public function show(Event $evento)
    {
        $evento->load([
            'coordenador',
            'inscricoes',
            'palestrantes',
            'programacao' => fn ($q) => $q->ordenado(),
        ]);

        $relacionados = Event::where('id', '!=', $evento->id)
            ->when($evento->area_tematica, fn ($qq) => $qq->where('area_tematica', $evento->area_tematica))
            ->whereIn('status', ['ativo', 'publicado'])
            ->orderBy('data_inicio_evento', 'asc')
            ->take(6)
            ->get();

        return view('front.event-show', compact('evento', 'relacionados'));
    }
}
