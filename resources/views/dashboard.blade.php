@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container py-5">

    <h1 class="mb-4">DASHBOARD</h1>

    <p class="text-muted">
        Você está logado(a), {{ auth()->user()->name }}!
    </p>

    {{-- Área do PARTICIPANTE: só aparece para quem NÃO é admin/master --}}
    @cannot('manage-users')
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-light fw-semibold">
                        Minhas inscrições
                    </div>
                    <div class="card-body">
                        <p class="card-text text-muted mb-3">
                            Gerencie suas inscrições em eventos
                        </p>
                        <a href="{{ route('inscricoes.index') }}" class="btn btn-primary">
                            Acessar minhas inscrições
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-light fw-semibold">
                        Meus certificados
                    </div>
                    <div class="card-body">
                        <p class="card-text text-muted mb-3">
                            Acesse e baixe seus certificados
                        </p>
                        {{-- rota nova, só do participante --}}
                        <a href="{{ route('certificados.meus') }}" class="btn btn-primary">
                            Ver meus certificados
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endcannot

    {{-- Área de ADMINISTRAÇÃO: só para quem pode manage-users (master/admin) --}}
    @can('manage-users')
        <h2 class="h4 mt-4 mb-3">Administração</h2>

        <div class="row g-3">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold">Usuários</div>
                    <div class="card-body">
                        <p class="card-text text-muted mb-3">
                            Criar, editar, ativar/desativar usuários
                        </p>
                        <a href="{{ route('admin.usuarios.index') }}" class="btn btn-sm btn-primary">
                            Gerenciar usuários
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold">Eventos</div>
                    <div class="card-body">
                        <p class="card-text text-muted mb-3">
                            Cadastro e programação de eventos
                        </p>
                        <a href="{{ route('eventos.index') }}" class="btn btn-sm btn-primary">
                            Gerenciar eventos
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold">Relatórios</div>
                    <div class="card-body">
                        <p class="card-text text-muted mb-3">
                            Gerar relatórios de eventos e participantes
                        </p>
                        <a href="{{ route('relatorios.index') }}" class="btn btn-sm btn-primary">
                            Ver relatórios
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold">Logs de Auditoria</div>
                    <div class="card-body">
                        <p class="card-text text-muted mb-3">
                            Acompanhar alterações realizadas no sistema
                        </p>
                        <a href="{{ route('audit-logs.index') }}" class="btn btn-sm btn-primary">
                            Ver logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endcan
</div>
@endsection
