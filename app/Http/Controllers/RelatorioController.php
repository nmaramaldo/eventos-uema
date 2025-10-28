<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    /**
     * Mostra o menu principal de relatórios.
     * Carrega a view: relatorios/index.blade.php
     */
    public function index()
    {
        return view('relatorios.index');
    }

    /**
     * Mostra a lista de todos os eventos em uma tabela.
     * Carrega a view: relatorios/eventos.blade.php
     */
    public function listaEventos()
    {
        // Usar withCount é mais eficiente que carregar todas as inscrições só para contar
        $eventos = Event::withCount('inscricoes')
                        ->orderBy('data_inicio_evento', 'desc')
                        ->get();

        return view('relatorios.eventos', compact('eventos'));
    }

    /**
     * Gera um PDF com a lista de todos os eventos.
     * Carrega a view: relatorios/pdf.blade.php
     */
    public function gerarPdfEventos()
    {
        $eventos = Event::withCount('inscricoes')
                        ->orderBy('data_inicio_evento', 'desc')
                        ->get();

        $pdf = PDF::loadView('relatorios.pdf', compact('eventos'));
        
        return $pdf->stream('relatorio-geral-eventos-uema.pdf');
    }
}