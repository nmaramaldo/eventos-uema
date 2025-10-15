@extends('layouts.app')
@section('title', 'Editar Palestrante - ' . $evento->nome)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3>Editar Palestrante: {{ $palestrante->nome }} - Evento: {{ $evento->nome }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('eventos.palestrantes.update', ['evento' => $evento->id, 'palestrante' => $palestrante->id]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Nome --}}
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome *</label>
                            <input type="text" name="nome" id="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $palestrante->nome) }}" required>
                            @error('nome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- E-mail --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $palestrante->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Biografia --}}
                        <div class="mb-3">
                            <label for="biografia" class="form-label">Biografia</label>
                            <textarea name="biografia" id="biografia" class="form-control @error('biografia') is-invalid @enderror" rows="3">{{ old('biografia', $palestrante->biografia) }}</textarea>
                            @error('biografia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('eventos.palestrantes.index', $evento) }}" class="btn btn-outline-secondary">Voltar</a>
                            <button type="submit" class="btn btn-success">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
