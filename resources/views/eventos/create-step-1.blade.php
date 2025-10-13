@extends('layouts.app')
@section('title', 'Criar Evento - Passo 1: Informações Gerais')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3>Novo Evento (Passo 1 de 3): Informações Gerais</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('eventos.create.store.step1') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- NOME E DESCRIÇÃO --}}
                        <div class="mb-3">
                            <label for="nome" class="form-label">Título *</label>
                            <input type="text" id="nome" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $eventData['nome'] ?? '') }}" required>
                            @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição *</label>
                            <textarea id="descricao" name="descricao" class="form-control @error('descricao') is-invalid @enderror" rows="5" required>{{ old('descricao', $eventData['descricao'] ?? '') }}</textarea>
                            @error('descricao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        {{-- ✅ CORREÇÃO: CLASSIFICAÇÃO E ÁREA TEMÁTICA COMO SELECT --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipo_classificacao" class="form-label">Classificação *</label>
                                @php
                                    $classificacoes = ['Acadêmico', 'Científico', 'Cultural', 'Esportivo', 'Social', 'Workshop', 'Congresso', 'Seminário'];
                                @endphp
                                <select id="tipo_classificacao" name="tipo_classificacao" class="form-select @error('tipo_classificacao') is-invalid @enderror" required>
                                    <option value="">Selecione...</option>
                                    @foreach ($classificacoes as $classificacao)
                                        <option value="{{ $classificacao }}" @selected(old('tipo_classificacao', $eventData['tipo_classificacao'] ?? '') == $classificacao)>
                                            {{ $classificacao }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_classificacao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="area_tematica" class="form-label">Área Temática *</label>
                                @php
                                    $areas = ['Ciências Humanas', 'Ciências Sociais', 'Ciências Exatas e da Terra', 'Engenharias', 'Ciências da Saúde', 'Ciências Agrárias', 'Linguística, Letras e Artes', 'Outros'];
                                @endphp
                                <select id="area_tematica" name="area_tematica" class="form-select @error('area_tematica') is-invalid @enderror" required>
                                    <option value="">Selecione...</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area }}" @selected(old('area_tematica', $eventData['area_tematica'] ?? '') == $area)>
                                            {{ $area }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('area_tematica')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- DATAS DO EVENTO --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="data_inicio_evento" class="form-label">Início do Evento *</label>
                                <input type="datetime-local" id="data_inicio_evento" name="data_inicio_evento" class="form-control @error('data_inicio_evento') is-invalid @enderror" value="{{ old('data_inicio_evento', isset($eventData['data_inicio_evento']) ? \Carbon\Carbon::parse($eventData['data_inicio_evento'])->format('Y-m-d\TH:i') : '') }}" required>
                                @error('data_inicio_evento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="data_fim_evento" class="form-label">Fim do Evento *</label>
                                <input type="datetime-local" id="data_fim_evento" name="data_fim_evento" class="form-control @error('data_fim_evento') is-invalid @enderror" value="{{ old('data_fim_evento', isset($eventData['data_fim_evento']) ? \Carbon\Carbon::parse($eventData['data_fim_evento'])->format('Y-m-d\TH:i') : '') }}" required>
                                @error('data_fim_evento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        {{-- DATAS DE INSCRIÇÃO --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="data_inicio_inscricao" class="form-label">Início das Inscrições *</label>
                                <input type="datetime-local" id="data_inicio_inscricao" name="data_inicio_inscricao" class="form-control @error('data_inicio_inscricao') is-invalid @enderror" value="{{ old('data_inicio_inscricao', isset($eventData['data_inicio_inscricao']) ? \Carbon\Carbon::parse($eventData['data_inicio_inscricao'])->format('Y-m-d\TH:i') : '') }}" required>
                                @error('data_inicio_inscricao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="data_fim_inscricao" class="form-label">Fim das Inscrições *</label>
                                <input type="datetime-local" id="data_fim_inscricao" name="data_fim_inscricao" class="form-control @error('data_fim_inscricao') is-invalid @enderror" value="{{ old('data_fim_inscricao', isset($eventData['data_fim_inscricao']) ? \Carbon\Carbon::parse($eventData['data_fim_inscricao'])->format('Y-m-d\TH:i') : '') }}" required>
                                @error('data_fim_inscricao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        {{-- TIPO DE EVENTO E LOGOMARCA --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipo_evento" class="form-label">Tipo de Evento *</label>
                                <select id="tipo_evento" name="tipo_evento" class="form-select @error('tipo_evento') is-invalid @enderror" required>
                                    <option value="presencial" @selected(old('tipo_evento', $eventData['tipo_evento'] ?? '') == 'presencial')>Presencial</option>
                                    <option value="online" @selected(old('tipo_evento', $eventData['tipo_evento'] ?? '') == 'online')>Online</option>
                                    <option value="hibrido" @selected(old('tipo_evento', $eventData['tipo_evento'] ?? '') == 'hibrido')>Híbrido</option>
                                </select>
                                @error('tipo_evento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="logomarca" class="form-label">Logomarca (PNG, JPEG - até 5MB)</label>
                                <input type="file" id="logomarca" name="logomarca" class="form-control @error('logomarca') is-invalid @enderror" accept="image/png,image/jpeg">
                                @error('logomarca')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- STATUS E VAGAS --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="rascunho" @selected(old('status', $eventData['status'] ?? 'rascunho') == 'rascunho')>Rascunho</option>
                                    <option value="publicado" @selected(old('status', $eventData['status'] ?? '') == 'publicado')>Publicado</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="vagas" class="form-label">Vagas</label>
                                <input type="number" id="vagas" name="vagas" class="form-control @error('vagas') is-invalid @enderror" value="{{ old('vagas', $eventData['vagas'] ?? '') }}" placeholder="Deixe em branco para ilimitado">
                                @error('vagas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">Próximo: Programação</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection