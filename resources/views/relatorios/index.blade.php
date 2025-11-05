@extends('layouts.app')
@section('title', 'Relatório de Eventos')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Relatório de Eventos</h2>            
        </div>
        
        {{-- ✅ FORMULÁRIO DE FILTROS SIMPLIFICADO --}}
        <div class="card-body border-bottom">
            <h5 class="card-title">Filtros</h5>
            <form method="GET" action="{{ route('relatorios.index') }}">
                <div class="row g-3 align-items-end">
                    {{-- Filtro de Nome --}}
                    <div class="col-md-4">
                        <label for="nome_evento" class="form-label">Nome do Evento</label>
                        <input type="text" id="nome_evento" name="nome_evento" class="form-control" value="{{ $filtros['nome_evento'] ?? '' }}">
                    </div>

                    {{-- Filtro de Período (Início) --}}
                    <div class="col-md-3">
                        <label for="data_inicio" class="form-label">Período (Início a partir de)</label>
                        <input type="date" id="data_inicio" name="data_inicio" class="form-control" value="{{ $filtros['data_inicio'] ?? '' }}">
                    </div>

                    {{-- Filtro de Período (Fim) --}}
                    <div class="col-md-3">
                        <label for="data_fim" class="form-label">Período (Fim até)</label>
                        <input type="date" id="data_fim" name="data_fim" class="form-control" value="{{ $filtros['data_fim'] ?? '' }}">
                    </div>
                    
                    {{-- Botões --}}
                    <div class="col-md-2 d-flex">
                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                        <a href="{{ route('relatorios.index') }}" class="btn btn-light w-100 ms-2">Limpar</a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabela de Resultados (Simplificada) --}}
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-3">Evento</th>
                        <th class="px-3">Período</th>
                        <th class="px-3">Classificação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventos as $evento)
                    <tr>
                        <td class="px-3">
                            <a href="{{ route('relatorios.evento.show', $evento) }}">
                                <strong>{{ $evento->nome }}</strong>
                            </a>
                        </td>
                        <td class="px-3">{{ $evento->periodo_evento }}</td>
                        <td class="px-3">{{ $evento->tipo_classificacao ?? 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted p-5">Nenhum evento encontrado com os filtros aplicados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Paginação --}}
        @if ($eventos->hasPages())
        <div class="card-footer">
            {{ $eventos->links() }}
        </div>
        @endif
    </div>
</div>
@endsection