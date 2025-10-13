@extends('layouts.app')
@section('title', 'Gerenciar Palestrantes')

@section('content')
<div class="container py-5">
    {{-- Navegação (similar à que fizemos no edit.blade.php) --}}
    <h2 class="mb-2">Gerenciar Evento</h2>
    <h3 class="text-muted fw-light">{{ $evento->nome }}</h3>
    <hr>
    <div class="d-flex gap-2 mb-4">
        <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-outline-primary">Informações Gerais</a>
        <a href="{{ route('eventos.programacao.index', $evento) }}" class="btn btn-outline-primary">Gerenciar Programação</a>
        <a href="{{ route('eventos.palestrantes.index', $evento) }}" class="btn btn-primary">Gerenciar Palestrantes</a>
    </div>

    {{-- Lógica para gerenciar palestrantes aqui... --}}
    <div class="card shadow-sm">
        <div class="card-header"><h4>Palestrantes do Evento</h4></div>
        <div class="card-body">
            {{-- Formulário para adicionar um palestrante existente ao evento --}}
            {{-- Lista dos palestrantes já associados com botão para remover --}}
            <p>Em construção...</p>
        </div>
    </div>
</div>
@endsection