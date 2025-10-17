{{-- resources/views/audit-logs/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalhes do Log')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="h3">Detalhes do Log</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">
                ← Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informações Básicas</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Data/Hora:</th>
                            <td>{{ $auditLog->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Ação:</th>
                            <td>
                                <span class="badge bg-{{ $auditLog->action === 'created' ? 'success' : ($auditLog->action === 'updated' ? 'warning' : 'danger') }}">
                                    {{ $auditLog->action }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Model:</th>
                            <td>{{ $auditLog->model_type }} #{{ $auditLog->model_id }}</td>
                        </tr>
                        <tr>
                            <th>Usuário:</th>
                            <td>
                                @if($auditLog->user)
                                    {{ $auditLog->user->name }} (ID: {{ $auditLog->user_id }})
                                @else
                                    <span class="text-muted">Sistema</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>IP:</th>
                            <td>{{ $auditLog->ip_address }}</td>
                        </tr>
                        <tr>
                            <th>User Agent:</th>
                            <td><small class="text-muted">{{ $auditLog->user_agent }}</small></td>
                        </tr>
                        <tr>
                            <th>URL:</th>
                            <td><small class="text-muted">{{ $auditLog->url }}</small></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            @if($auditLog->action === 'updated' && !empty($auditLog->changes))
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Alterações Realizadas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Campo</th>
                                    <th>Valor Antigo</th>
                                    <th>Valor Novo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($auditLog->changes as $field => $change)
                                <tr>
                                    <td><strong>{{ $field }}</strong></td>
                                    <td>
                                        <span class="text-danger">{{ $change['old'] ?? 'null' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-success">{{ $change['new'] ?? 'null' }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-body text-center">
                    <p class="text-muted">Não há alterações detalhadas para esta ação</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Valores Completos --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Valores Antigos (Original)</h5>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded"><code>@json($auditLog->old_values, JSON_PRETTY_PRINT)</code></pre>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Valores Novos (Atual)</h5>
                </div>
                <div class="card-body">
                    <pre class="bg-light p-3 rounded"><code>@json($auditLog->new_values, JSON_PRETTY_PRINT)</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection