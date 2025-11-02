@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Relatório de Eventos</h2>

        <a href="{{ route('relatorios.eventos.pdf') }}" class="btn btn-primary">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">Filtros</div>
        <div class="card-body">
            <form method="GET" action="{{ route('relatorios.eventos') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="q" class="form-label">Nome do Evento</label>
                        <input type="text" name="q" id="q" class="form-control" value="{{ request('q') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="rascunho" @selected(request('status') == 'rascunho')>Rascunho</option>
                            <option value="publicado" @selected(request('status') == 'publicado')>Publicado</option>
                            <option value="encerrado" @selected(request('status') == 'encerrado')>Encerrado</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="tipo_evento" class="form-label">Tipo de Evento</label>
                        <select name="tipo_evento" id="tipo_evento" class="form-select">
                            <option value="">Todos</option>
                            <option value="presencial" @selected(request('tipo_evento') == 'presencial')>Presencial</option>
                            <option value="online" @selected(request('tipo_evento') == 'online')>Online</option>
                            <option value="hibrido" @selected(request('tipo_evento') == 'hibrido')>Híbrido</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="area_tematica" class="form-label">Área Temática</label>
                        <input type="text" name="area_tematica" id="area_tematica" class="form-control" value="{{ request('area_tematica') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="data_inicio" class="form-label">Data de Início (a partir de)</label>
                        <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ request('data_inicio') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="data_fim" class="form-label">Data de Fim (até)</label>
                        <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ request('data_fim') }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="{{ route('relatorios.eventos') }}" class="btn btn-secondary">Limpar Filtros</a>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Evento</th>
                        <th>Status</th>
                        <th>Período</th>
                        <th>Inscritos</th>
                        <th>Tipo</th>
                        <th>Classificação</th>
                        <th>Área Temática</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($eventos as $evento)
                        <tr>
                            <td>{{ $evento->nome }}</td>
                            <td>{{ ucfirst($evento->status) }}</td>
                            <td>{{ $evento->data_inicio_evento->format('d/m/Y') }} - {{ $evento->data_fim_evento->format('d/m/Y') }}</td>
                            <td>{{ $evento->inscricoes_count }}</td>
                            <td>{{ $evento->tipo_evento }}</td>
                            <td>{{ $evento->tipo_classificacao }}</td>
                            <td>{{ $evento->area_tematica }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Nenhum evento encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
