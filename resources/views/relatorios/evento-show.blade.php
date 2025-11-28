@extends('layouts.app')
@section('title', 'Relatório: ' . $evento->nome)

@section('content')
<div class="container py-5">
    <div class="card shadow-sm">
        {{-- CABEÇALHO DO RELATÓRIO --}}
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">Relatório de Evento</h2>
                    <p class="text-muted mb-0">{{ $evento->nome }}</p>
                </div>
                <div class="d-flex gap-2">
                     <a href="{{ route('relatorios.index') }}" class="btn btn-secondary">Voltar</a>
                     <a href="{{ route('relatorios.evento.pdf', $evento) }}" class="btn btn-primary" target="_blank">
                        Exportar PDF
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            {{-- Detalhes do Evento (Pequeno Resumo) --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <strong>Período:</strong><br>
                    {{ $evento->periodo_evento }}
                </div>
                <div class="col-md-4">
                    <strong>Classificação:</strong><br>
                    {{ $evento->tipo_classificacao ?? 'N/A' }}
                </div>
                <div class="col-md-4">
                    <strong>Inscritos:</strong><br>
                    {{ $participantes->count() }}
                </div>
            </div>
            
            <hr>

            {{-- LISTA DE PARTICIPANTES --}}
            <h4 class="mt-4">Lista de Participantes</h4>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">Nome do Participante</th>
                            <th class="px-3">E-mail</th>
                            <th class="px-3">Data da Inscrição</th>
                            <th class="px-3">Status do Check-in</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($participantes as $inscricao)
                        <tr>
                            <td class="px-3">{{ $inscricao->user->name ?? 'Usuário não encontrado' }}</td>
                            <td class="px-3">{{ $inscricao->user->email ?? 'N/A' }}</td>
                            <td class="px-3">{{ $inscricao->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-3">
                                @if($inscricao->presente)
                                    <span class="badge bg-success">Presente</span>
                                @else
                                    <span class="badge bg-secondary">Ausente</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted p-5">Nenhum participante inscrito neste evento.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection