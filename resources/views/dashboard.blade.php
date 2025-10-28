@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container" style="padding:60px 0">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-sm-12">
            <h2 class="page-header">Dashboard</h2>
            <p>Você está logado(a), {{ auth()->user()->name }}!</p>
        </div>
    </div>

    {{-- Atalhos do usuário (sempre visíveis) --}}
    <div class="row" style="margin-top:20px">
        <div class="col-sm-4">
            <a href="{{ route('inscricoes.index') }}" class="panel panel-default" style="display:block;text-decoration:none;">
                <div class="panel-heading"><strong>Minhas inscrições</strong></div>
                <div class="panel-body text-muted">Gerencie suas inscrições em eventos</div>
            </a>
        </div>

        <div class="col-sm-4">
            <a href="{{ route('certificados.index') }}" class="panel panel-default" style="display:block;text-decoration:none;">
                <div class="panel-heading"><strong>Meus certificados</strong></div>
                <div class="panel-body text-muted">Acesse e baixe seus certificados</div>
            </a>
        </div>
    </div>

    {{-- Bloco de Administração (visível para ADMIN e MASTER) --}}
    @can('viewAny', App\Models\Event::class)
    <div class="row" style="margin-top:35px">
        <div class="col-sm-12"><h3 class="page-header" style="margin-top:0">Administração</h3></div>

        {{-- Card "Usuários" - Visível apenas para MASTER --}}
        @can('viewAny', App\Models\User::class)
            <div class="col-sm-4">
                <a href="{{ route('admin.usuarios.index') }}" class="panel panel-default" style="display:block;text-decoration:none;">
                    <div class="panel-heading"><strong>Usuários</strong></div>
                    <div class="panel-body text-muted">Criar, editar, ativar/bloquear</div>
                </a>
            </div>
        @endcan

        {{-- Card "Eventos" - Visível para ADMIN e MASTER --}}
        <div class="col-sm-4">
            <a href="{{ route('eventos.index') }}" class="panel panel-default" style="display:block;text-decoration:none;">
                <div class="panel-heading"><strong>Eventos</strong></div>
                <div class="panel-body text-muted">Cadastro e programação</div>
            </a>
        </div>

        {{-- Card "Relatórios" - Visível apenas para MASTER --}}
       @can('viewAny', App\Models\User::class)           
            <div class="col-sm-4">               
                <a href="{{ route('relatorios.index') }}" class="panel panel-default" style="display:block;text-decoration:none;">
                    <div class="panel-heading"><strong>Relatórios</strong></div>
                    <div class="panel-body text-muted">Gerar relatórios de eventos e participantes.</div>
                </a>
            </div>
        @endcan
</div>

@endcan 

@endsection
