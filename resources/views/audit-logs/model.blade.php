@extends('layouts.new-event')

@section('title', 'Logs de Auditoria - ' . class_basename($modelType))

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="h3">
                    <i class="fas fa-history me-2"></i>
                    Logs de Auditoria
                    @if ($modelId)
                        - {{ class_basename($modelType) }} #{{ $modelId }}
                    @else
                        - {{ class_basename($modelType) }}
                    @endif
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('audit-logs.index') }}">Todos os Logs</a></li>
                        <li class="breadcrumb-item active">{{ class_basename($modelType) }}</li>
                        @if ($modelId)
                            <li class="breadcrumb-item active">#{{ $modelId }}</li>
                        @endif
                    </ol>
                </nav>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Voltar para Todos os Logs
                </a>
            </div>
        </div>

        {{-- Estatísticas --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $logs->total() }}</h4>
                                <small>Total de Logs</small>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-list fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $logs->where('action', 'created')->count() }}</h4>
                                <small>Criações</small>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-plus fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $logs->where('action', 'updated')->count() }}</h4>
                                <small>Atualizações</small>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-edit fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $logs->where('action', 'deleted')->count() }}</h4>
                                <small>Exclusões</small>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-trash fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabela de Logs --}}
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        Histórico de Alterações
                        @if ($modelId)
                            - ID: {{ $modelId }}
                        @endif
                    </h5>
                    <span class="badge bg-primary">
                        Model: {{ class_basename($modelType) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                @if ($logs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="150">Data/Hora</th>
                                    <th width="120">Ação</th>
                                    <th>Usuário</th>
                                    <th>Descrição</th>
                                    <th>Alterações</th>
                                    <th width="100">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logs as $log)
                                    <tr>
                                        <td>
                                            <small>
                                                <strong>{{ $log->created_at->format('d/m/Y') }}</strong><br>
                                                {{ $log->created_at->format('H:i:s') }}
                                            </small>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $log->action === 'created' ? 'success' : ($log->action === 'updated' ? 'warning' : 'danger') }}">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($log->user)
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <i class="fas fa-user-circle me-2 text-muted"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-2">
                                                        <strong>{{ $log->user->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">ID: {{ $log->user_id }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">
                                                    <i class="fas fa-robot me-1"></i> Sistema
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $log->description }}
                                            @if ($log->model_id)
                                                <br>
                                                <small class="text-muted">ID: {{ $log->model_id }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($log->action === 'updated' && !empty($log->changes))
                                                <span class="badge bg-info">
                                                    {{ count($log->changes) }} campo(s) alterado(s)
                                                </span>
                                            @elseif($log->action === 'created')
                                                <span class="badge bg-success">Novo registro</span>
                                            @elseif($log->action === 'deleted')
                                                <span class="badge bg-danger">Registro excluído</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('audit-logs.show', $log) }}"
                                                class="btn btn-sm btn-outline-primary" title="Ver detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginação --}}
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Mostrando {{ $logs->firstItem() }} a {{ $logs->lastItem() }} de {{ $logs->total() }}
                            registros
                        </div>
                        <div>
                            {{ $logs->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Nenhum log encontrado</h4>
                        <p class="text-muted">
                            Não há registros de auditoria para
                            @if ($modelId)
                                {{ class_basename($modelType) }} #{{ $modelId }}
                            @else
                                {{ class_basename($modelType) }}
                            @endif
                        </p>
                        <a href="{{ route('audit-logs.index') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-arrow-left me-1"></i> Ver Todos os Logs
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Informações do Model --}}
        @if ($modelId)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações do Model
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Model Type:</th>
                                    <td><code>{{ $modelType }}</code></td>
                                </tr>
                                <tr>
                                    <th>Model ID:</th>
                                    <td><strong>#{{ $modelId }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Nome do Model:</th>
                                    <td>{{ class_basename($modelType) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="d-grid gap-2">
                                <a href="{{ route('audit-logs.index', ['model_type' => $modelType]) }}"
                                    class="btn btn-outline-primary">
                                    <i class="fas fa-list me-1"></i> Ver Todos os Logs deste Model
                                </a>
                                <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-database me-1"></i> Ver Todos os Logs do Sistema
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>
        .table th {
            border-top: none;
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            color: #6c757d;
        }

        .badge {
            font-size: 0.75rem;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }
    </style>
@endsection
