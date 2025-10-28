@extends('layouts.app')
@section('title', 'Editar Evento')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            {{-- CABEÇALHO E NAVEGAÇÃO --}}
            <h2 class="mb-2">Editar Evento</h2>
            <h3 class="text-muted fw-light">{{ $evento->nome }}</h3>
            <hr>
            <div class="d-flex gap-2 mb-4">
                <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-primary">
                    Informações Gerais
                </a>
                <a href="{{ route('eventos.programacao.index', $evento) }}" class="btn btn-outline-primary">
                    Editar Programação
                </a>
                <a href="{{ route('eventos.palestrantes.index', $evento) }}" class="btn btn-outline-primary">
                    Editar Palestrantes
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- FORMULÁRIO PARA EDITAR INFORMAÇÕES GERAIS --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4>Informações Gerais e Inscrições</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('eventos.update', $evento) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- NOME E DESCRIÇÃO --}}
                        <div class="mb-3">
                            <label for="nome" class="form-label">Título do Evento *</label>
                            <input type="text" id="nome" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $evento->nome) }}" required>
                            @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        {{-- Adicione aqui os outros campos de Informações Gerais que podem ser editados --}}
                        {{-- Exemplo para o campo de vagas: --}}
                        <div class="mb-3">
                            <label for="vagas" class="form-label">Número de Vagas</label>
                            <input type="number" id="vagas" name="vagas" class="form-control @error('vagas') is-invalid @enderror" value="{{ old('vagas', $evento->vagas) }}" placeholder="Deixe em branco para ilimitado">
                            @error('vagas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('eventos.index') }}" class="btn btn-secondary">Voltar à Lista</a>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection