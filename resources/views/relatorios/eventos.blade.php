@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Relatório de Eventos</h2>

        <a href="{{ route('relatorios.eventos.pdf') }}" class="btn btn-primary">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID do Evento</th>
                        <th>Título</th>
                        <th>Data</th>
                        <th>Inscritos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($eventos as $evento)
                        <tr>
                            <td>{{ $evento->id }}</td>                            
                            <td>{{ $evento->nome }}</td>
                            <td>{{ $evento->data_inicio_evento->format('d/m/Y') }}</td>                            
                            <td>{{ $evento->inscricoes_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
