@extends('layouts.app')
@section('title', 'Editar Atividade - ' . $evento->nome)

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Editar Atividade do Evento: {{ $evento->nome }}</h3>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form
                            action="{{ route('eventos.programacao.update', ['evento' => $evento->id, 'atividade' => $atividade->id]) }}"
                            method="POST" id="mainForm">
                            @csrf
                            @method('PUT')

                            <div class="border p-3 rounded mb-4 bg-light">
                                <h5 class="mb-3">Detalhes da Atividade</h5>
                                <div class="row">
                                    <div class="col-12 mb-2">
                                        <label for="titulo" class="form-label">Título *</label>
                                        <input type="text" id="titulo" name="titulo" class="form-control"
                                            value="{{ old('titulo', $atividade->titulo) }}" required>
                                    </div>

                                    <div class="col-12 mb-2">
                                        <label for="descricao" class="form-label">Descrição (opcional)</label>
                                        <textarea id="descricao" name="descricao" class="form-control" rows="2">{{ old('descricao', $atividade->descricao) }}</textarea>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <label for="modalidade" class="form-label">Modalidade *</label>
                                        <select id="modalidade" name="modalidade" class="form-control" required>
                                            @php
                                                $modalidades = [
                                                    'Palestra',
                                                    'Minicurso',
                                                    'Mesa-redonda',
                                                    'Workshop',
                                                    'Conferência',
                                                    'Oficina',
                                                    'Outro',
                                                ];
                                            @endphp
                                            @foreach ($modalidades as $m)
                                                <option value="{{ $m }}"
                                                    {{ old('modalidade', $atividade->modalidade) == $m ? 'selected' : '' }}>
                                                    {{ $m }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <label for="data_hora_inicio" class="form-label">Início *</label>
                                        <input type="datetime-local" id="data_hora_inicio" name="data_hora_inicio"
                                            class="form-control"
                                            value="{{ old('data_hora_inicio', \Carbon\Carbon::parse($atividade->data_hora_inicio)->format('Y-m-d\TH:i')) }}"
                                            required>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <label for="data_hora_fim" class="form-label">Fim *</label>
                                        <input type="datetime-local" id="data_hora_fim" name="data_hora_fim"
                                            class="form-control"
                                            value="{{ old('data_hora_fim', \Carbon\Carbon::parse($atividade->data_hora_fim)->format('Y-m-d\TH:i')) }}"
                                            required>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <label for="localidade" class="form-label">Local *</label>
                                        <input type="text" id="localidade" name="localidade" class="form-control"
                                            value="{{ old('localidade', $atividade->local?->nome ?? $atividade->localidade) }}"
                                            required>
                                    </div>

                                    <div class="col-md-3 mb-2">
                                        <label for="capacidade" class="form-label">Vagas (opcional)</label>
                                        <input type="number" id="capacidade" name="capacidade" class="form-control"
                                            min="0" value="{{ old('capacidade', $atividade->capacidade) }}">
                                    </div>

                                    <div class="col-md-3 d-flex align-items-center mt-3">
                                        <div class="form-check">
                                            <input type="checkbox" id="requer_inscricao" name="requer_inscricao"
                                                class="form-check-input"
                                                {{ old('requer_inscricao', $atividade->requer_inscricao) ? 'checked' : '' }}>
                                            <label for="requer_inscricao" class="form-check-label">Requer Inscrição?</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('eventos.programacao.index', $evento) }}"
                                    class="btn btn-outline-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-success" id="submitBtn">Salvar Alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
