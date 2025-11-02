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
    public function listaEventos(Request $request)
    {
        $query = Event::withCount('inscricoes')->orderBy('data_inicio_evento', 'desc');

        // Filtros
        if ($request->filled('q')) {
            $query->where('nome', 'like', '%' . $request->input('q') . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('tipo_evento')) {
            $query->where('tipo_evento', $request->input('tipo_evento'));
        }

        if ($request->filled('area_tematica')) {
            $query->where('area_tematica', $request->input('area_tematica'));
        }

        if ($request->filled('data_inicio')) {
            $query->where('data_inicio_evento', '>=', $request->input('data_inicio'));
        }

        if ($request->filled('data_fim')) {
            $query->where('data_fim_evento', '<=', $request->input('data_fim'));
        }

        $eventos = $query->get();

        return view('relatorios.eventos', compact('eventos'));
    }

    /**
     * Gera um PDF com a lista de todos os eventos.
     * Carrega a view: relatorios/pdf.blade.php
     */
    public function gerarPdfEventos(Request $request)
    {
        $query = Event::withCount('inscricoes')->orderBy('data_inicio_evento', 'desc');

        // Filtros
        if ($request->filled('q')) {
            $query->where('nome', 'like', '%' . $request->input('q') . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('tipo_evento')) {
            $query->where('tipo_evento', $request->input('tipo_evento'));
        }

        if ($request->filled('area_tematica')) {
            $query->where('area_tematica', $request->input('area_tematica'));
        }

        if ($request->filled('data_inicio')) {
            $query->where('data_inicio_evento', '>=', $request->input('data_inicio'));
        }

        if ($request->filled('data_fim')) {
            $query->where('data_fim_evento', '<=', $request->input('data_fim'));
        }

        $eventos = $query->get();

        $pdf = PDF::loadView('relatorios.pdf', compact('eventos'));
        
        return $pdf->stream('relatorio-geral-eventos-uema.pdf');
    }
}