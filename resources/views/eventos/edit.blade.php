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
                            <label for="nome" class="form-label">Título *</label>
                            <input type="text" id="nome" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome', $evento->nome) }}" required>
                            @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição *</label>
                            <textarea id="descricao" name="descricao" class="form-control @error('descricao') is-invalid @enderror" rows="5" required>{{ old('descricao', $evento->descricao) }}</textarea>
                            @error('descricao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- CLASSIFICAÇÃO E ÁREA TEMÁTICA --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipo_classificacao" class="form-label">Classificação *</label>
                                @php
                                    $classificacoes = config('eventos.classificacoes', []);
                                @endphp
                                <select id="tipo_classificacao" name="tipo_classificacao" class="form-select @error('tipo_classificacao') is-invalid @enderror" required>
                                    <option value="">Selecione...</option>
                                    @foreach ($classificacoes as $classificacao)
                                        <option value="{{ $classificacao }}" @selected(old('tipo_classificacao', $evento->tipo_classificacao) == $classificacao)>
                                            {{ $classificacao }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_classificacao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="area_tematica" class="form-label">Área Temática *</label>
                                @php
                                    $areas = config('eventos.areas_tematica', []);
                                @endphp
                                <select id="area_tematica" name="area_tematica" class="form-select @error('area_tematica') is-invalid @enderror" required>
                                    <option value="">Selecione...</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area }}" @selected(old('area_tematica', $evento->area_tematica) == $area)>
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
                                <input type="datetime-local" id="data_inicio_evento" name="data_inicio_evento" class="form-control @error('data_inicio_evento') is-invalid @enderror" value="{{ old('data_inicio_evento', $evento->data_inicio_evento->format('Y-m-d\TH:i')) }}" required>
                                @error('data_inicio_evento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="data_fim_evento" class="form-label">Fim do Evento *</label>
                                <input type="datetime-local" id="data_fim_evento" name="data_fim_evento" class="form-control @error('data_fim_evento') is-invalid @enderror" value="{{ old('data_fim_evento', $evento->data_fim_evento->format('Y-m-d\TH:i')) }}" required>
                                @error('data_fim_evento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- DATAS DE INSCRIÇÃO --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="data_inicio_inscricao" class="form-label">Início das Inscrições *</label>
                                <input type="datetime-local" id="data_inicio_inscricao" name="data_inicio_inscricao" class="form-control @error('data_inicio_inscricao') is-invalid @enderror" value="{{ old('data_inicio_inscricao', $evento->data_inicio_inscricao->format('Y-m-d\TH:i')) }}" required>
                                @error('data_inicio_inscricao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="data_fim_inscricao" class="form-label">Fim das Inscrições *</label>
                                <input type="datetime-local" id="data_fim_inscricao" name="data_fim_inscricao" class="form-control @error('data_fim_inscricao') is-invalid @enderror" value="{{ old('data_fim_inscricao', $evento->data_fim_inscricao->format('Y-m-d\TH:i')) }}" required>
                                @error('data_fim_inscricao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- TIPO DE EVENTO E LOGOMARCA --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipo_evento" class="form-label">Tipo de Evento *</label>
                                <select id="tipo_evento" name="tipo_evento" class="form-select @error('tipo_evento') is-invalid @enderror" required>
                                    <option value="presencial" @selected(old('tipo_evento', $evento->tipo_evento) == 'presencial')>Presencial</option>
                                    <option value="online" @selected(old('tipo_evento', $evento->tipo_evento) == 'online')>Online</option>
                                    <option value="hibrido" @selected(old('tipo_evento', $evento->tipo_evento) == 'hibrido')>Híbrido</option>
                                </select>
                                @error('tipo_evento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="logomarca" class="form-label">Logomarca (PNG, JPEG - até 5MB)</label>
                                <input type="file" id="logomarca" name="logomarca" class="form-control @error('logomarca') is-invalid @enderror" accept="image/png,image/jpeg">
                                @error('logomarca')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- ONLINE FIELDS --}}
                        <div id="online-fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="link_reuniao" class="form-label">Link da Reunião</label>
                                    <input type="url" id="link_reuniao" name="link_reuniao" class="form-control @error('link_reuniao') is-invalid @enderror" value="{{ old('link_reuniao', $evento->link_reuniao) }}" placeholder="https://">
                                    @error('link_reuniao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="link_app" class="form-label">Link do App (Meet, Teams, etc)</label>
                                    <input type="url" id="link_app" name="link_app" class="form-control @error('link_app') is-invalid @enderror" value="{{ old('link_app', $evento->link_app) }}" placeholder="https://">
                                    @error('link_app')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                        {{-- VAGAS --}}
                        <div class="col-md-6 mb-3">
                            <label for="vagas" class="form-label">Vagas</label>
                            <input type="number" id="vagas" name="vagas" 
                                class="form-control @error('vagas') is-invalid @enderror" 
                                value="{{ old('vagas', $evento->vagas) }}" placeholder="Deixe em branco para ilimitado">
                            @error('vagas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- TIPO DE PAGAMENTO --}}
                        <div class="col-md-6 mb-3">
                            <label for="tipo_pagamento" class="form-label">Tipo de Pagamento *</label>
                            @php
                                $pagamentoSelecionado = old('tipo_pagamento', $evento->tipo_pagamento ?? 'gratis');
                            @endphp

                            <select id="tipo_pagamento" name="tipo_pagamento" 
                                    class="form-select @error('tipo_pagamento') is-invalid @enderror" required>
                                <option value="gratis" @selected($pagamentoSelecionado == 'gratis')>Grátis</option>
                                <option value="pix" @selected($pagamentoSelecionado == 'pix')>Pix</option>
                                <option value="outros" @selected($pagamentoSelecionado == 'outros')>Outros</option>
                            </select>
                            @error('tipo_pagamento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- DETALHES DO PAGAMENTO (aparece se escolher "outros") --}}
                    <div class="row" id="detalhes-pagamento-box" style="display: none;">
                        <div class="col-md-12 mb-3">
                            <label for="detalhes_pagamento" class="form-label">Detalhes do Pagamento *</label>
                            <textarea id="detalhes_pagamento" name="detalhes_pagamento" 
                                    class="form-control @error('detalhes_pagamento') is-invalid @enderror" 
                                    rows="3" placeholder="Ex: Doação de alimentos, dados bancários, etc.">{{ old('detalhes_pagamento', $evento->detalhes_pagamento ?? '') }}</textarea>
                            @error('detalhes_pagamento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectTipoEvento = document.getElementById('tipo_evento');
    const onlineFields = document.getElementById('online-fields');

    function toggleOnlineFields() {
        const selected = selectTipoEvento.value;
        if (selected === 'online' || selected === 'hibrido') {
            onlineFields.style.display = 'block';
        } else {
            onlineFields.style.display = 'none';
        }
    }

    selectTipoEvento.addEventListener('change', toggleOnlineFields);
    toggleOnlineFields();

    const selectPagamento = document.getElementById('tipo_pagamento');
    const boxDetalhes = document.getElementById('detalhes-pagamento-box');

    function toggleDetalhesBox() {
        if (selectPagamento.value === 'outros') {
            boxDetalhes.style.display = 'block';
        } else {
            boxDetalhes.style.display = 'none';
            document.getElementById('detalhes_pagamento').value = '';
        }
    }

    selectPagamento.addEventListener('change', toggleDetalhesBox);
    toggleDetalhesBox();
});
</script>
@endpush

@endsection
