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

        $areas = [
            'Educação',
            'Medicina',
            'Saúde e bem-estar',
            'Direito',
            'Agricultura, pesca e veterinária',
            'Artes e humanidades',
        ];

        return view('front.home', compact('destaques', 'recentes', 'areas'));
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

    // Rota /eventos -> reaproveita o mesmo método dos filtros
    public function index(Request $request)
    {
        return $this->eventos($request);
    }

    public function show(Event $evento)
    {
        // ❗ Relação correta é 'programacao'
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
