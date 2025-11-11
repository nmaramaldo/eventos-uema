<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class RelatorioController extends Controller
{
    
    /**
     * Exibe a lista principal de relatórios.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Event::class); // Protege a rota

        // ✅ FILTROS SIMPLIFICADOS
        $filtros = $request->only([
            'nome_evento', 'data_inicio', 'data_fim'
        ]);

        // Inicia a consulta ao banco
        $query = Event::query();

        // 1. Filtro por Nome do Evento
        if ($request->filled('nome_evento')) {
            $query->where('nome', 'like', '%' . $request->input('nome_evento') . '%');
        }

        // 2. Filtro por Data de Início (a partir de)
        if ($request->filled('data_inicio')) {
            $query->where('data_inicio_evento', '>=', $request->input('data_inicio'));
        }
        
        // 3. Filtro por Data de Fim (até)
        if ($request->filled('data_fim')) {
            $query->where('data_fim_evento', '<=', $request->input('data_fim'));
        }

        // Executa a consulta, ordena e pagina
        $eventos = $query->orderBy('data_inicio_evento', 'desc')
                         ->paginate(20)
                         ->withQueryString(); // Mantém os filtros na paginação

        // Não precisamos mais das 'opcoes' de dropdown
        return view('relatorios.index', compact('eventos', 'filtros'));
    }

    /**
     * ✅ NOVO MÉTODO
     * Exibe o relatório detalhado de um evento específico.
     */
    public function showEvento(Event $evento)
    {
        // Autoriza o usuário (ex: apenas admins podem ver relatórios)
        $this->authorize('viewAny', Event::class); 

        // Carrega o evento com suas inscrições E os dados dos usuários inscritos
        $evento->load('inscricoes.user');

        // Pega a coleção de inscrições (que agora contêm os dados do usuário)
        $participantes = $evento->inscricoes;

        return view('relatorios.evento-show', compact('evento', 'participantes'));
    }

    public function exportarPDF(Event $evento)
    {
        $this->authorize('viewAny', Event::class); 

        // Carrega os mesmos dados da página de detalhes
        $evento->load('inscricoes.user');
        $participantes = $evento->inscricoes;

        // ✅ CAPTURA OS NOVOS DADOS
        $usuarioExportador = Auth::user(); // Pega o usuário logado
        $dataExportacao = now(); // Pega a data e hora atuais

        // Prepara os dados para a view
        $data = [
            'evento' => $evento,
            'participantes' => $participantes,
            'usuarioExportador' => $usuarioExportador, //  Passa o usuário para a view
            'dataExportacao'    => $dataExportacao     //  Passa a data para a view
        ];

        // Carrega a view do PDF
        $pdf = Pdf::loadView('relatorios.evento-pdf', $data);
        
        $fileName = 'relatorio_' . \Illuminate\Support\Str::slug($evento->nome) . '.pdf';
        return $pdf->download($fileName);
    }
}