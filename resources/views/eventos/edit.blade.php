@extends('layouts.app')
@section('title', 'Gerenciar Evento')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <h2 class="mb-2">Gerenciar Evento</h2>
            <h3 class="text-muted fw-light">{{ $evento->nome }}</h3>
            <hr>

            {{-- BOTÕES DE NAVEGAÇÃO PARA AS SEÇÕES --}}
            <div class="d-flex gap-2 mb-4">
                <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-primary">
                    <i class="fas fa-info-circle me-1"></i> Informações Gerais
                </a>
                <a href="{{ route('eventos.programacao.index', $evento) }}" class="btn btn-outline-primary">
                    <i class="fas fa-calendar-alt me-1"></i> Gerenciar Programação
                </a>
                <a href="{{ route('eventos.palestrantes.index', $evento) }}" class="btn btn-outline-primary">
                    <i class="fas fa-users me-1"></i> Gerenciar Palestrantes
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h4>Informações Gerais e Inscrições</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('eventos.update', $evento) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- NOME --}}
                        <div class="mb-3">
                            <label for="nome" class="form-label">Título do Evento *</label>
                            <input type="text" id="nome" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $evento->nome) }}" required>
                            @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- STATUS --}}
                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="rascunho"  @selected(old('status',$evento->status)==='rascunho')>Rascunho</option>
                                <option value="publicado" @selected(old('status',$evento->status)==='publicado')>Publicado</option>
                            </select>
                            <small class="text-muted d-block mt-1">
                                <strong>Observação:</strong> o status <em>Encerrado</em> é exibido automaticamente quando a data de término do evento já passou.
                            </small>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- VAGAS --}}
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
