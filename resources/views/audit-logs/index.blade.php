{{-- resources/views/audit-logs/index.blade.php --}}
@extends('layouts.new-event')

@section('title', 'Logs de Auditoria')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3">Logs de Auditoria</h1>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="action" class="form-label">Ação</label>
                    <select name="action" id="action" class="form-select">
                        <option value="">Todas</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="model_type" class="form-label">Model</label>
                    <select name="model_type" id="model_type" class="form-select">
                        <option value="">Todos</option>
                        @foreach($modelTypes as $type)
                            <option value="{{ $type }}" {{ request('model_type') == $type ? 'selected' : '' }}>
                                {{ class_basename($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="user_id" class="form-label">Usuário ID</label>
                    <input type="number" name="user_id" id="user_id" class="form-control" 
                           value="{{ request('user_id') }}" placeholder="ID do usuário">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                    <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabela de Logs --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Usuário</th>
                            <th>Ação</th>
                            <th>Model</th>
                            <th>Descrição</th>
                            <th>IP</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($log->user)
                                    {{ $log->user->name }}
                                @else
                                    <span class="text-muted">Sistema</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $log->action === 'created' ? 'success' : ($log->action === 'updated' ? 'warning' : 'danger') }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>
                                {{ class_basename($log->model_type) }} 
                                @if($log->model_id)
                                    #{{ $log->model_id }}
                                @endif
                            </td>
                            <td>{{ $log->description }}</td>
                            <td>
                                <small class="text-muted">{{ $log->ip_address }}</small>
                            </td>
                            <td>
                                <a href="{{ route('audit-logs.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                    Detalhes
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Nenhum log encontrado</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginação --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection