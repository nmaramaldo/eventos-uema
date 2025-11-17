@extends('layouts.app')

@section('title', isset($evento) ? 'Gerar certificado - ' . $evento->nome : 'Gerar certificado')

@section('content')
<div class="container py-5">

    @php
        $eventoAtual = $evento ?? null;
        $eventoParam = $eventoAtual ? ['evento_id' => $eventoAtual->id] : [];
    @endphp

    {{-- Abas: Modelos / Emitir certificados --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link"
               href="{{ route('certificado-modelos.index', $eventoParam) }}">
                Modelos de certificado
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="#">
                Emitir certificados
            </a>
        </li>
    </ul>

    <div class="card shadow-sm">
        <div class="card-header">
            <h2 class="mb-0">
                Gerar certificado
                @isset($eventoAtual)
                    <small class="text-muted">— {{ $eventoAtual->nome }}</small>
                @endisset
            </h2>
            <p class="text-muted mb-0">
                Selecione a inscrição (com check-in realizado) e o modelo de certificado.
            </p>
        </div>

        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if(session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($eventoAtual)
                <div class="alert alert-secondary py-2">
                    <strong>Evento selecionado:</strong> {{ $eventoAtual->nome }}
                </div>
            @endif

            @if($modelos->isEmpty())
                <div class="alert alert-warning">
                    Ainda não há <strong>modelos de certificado</strong> para este contexto.
                    Crie pelo menos um modelo antes de emitir certificados.
                </div>
            @endif

            <form action="{{ route('certificados.store') }}" method="post">
                @csrf

                @if($eventoAtual)
                    {{-- Mantém o contexto do evento, se veio via ?evento_id= --}}
                    <input type="hidden" name="evento_id" value="{{ $eventoAtual->id }}">
                @endif

                {{-- Inscrição (participante + evento) --}}
                <div class="mb-3">
                    <label class="form-label">Inscrição *</label>
                    <select name="inscricao_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        @foreach($inscricoes as $insc)
                            @php
                                $user   = $insc->user ?? $insc->usuario;
                                $eventoLinha = $insc->evento;
                            @endphp
                            <option value="{{ $insc->id }}" @selected(old('inscricao_id')==$insc->id)>
                                [{{ $eventoLinha?->nome ?? 'Sem evento' }}] - {{ $user?->name ?? 'Sem usuário' }} ({{ $user?->email }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-1">
                        Apenas inscrições com <strong>check-in realizado</strong> são exibidas quando o evento foi selecionado.
                    </small>
                </div>

                {{-- Modelo de certificado --}}
                <div class="mb-3">
                    <label class="form-label">Modelo de certificado *</label>
                    <select name="modelo_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        @foreach($modelos as $m)
                            <option value="{{ $m->id }}" @selected(old('modelo_id')==$m->id)>
                                {{ $m->titulo }} — {{ $m->evento?->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Data de emissão --}}
                <div class="mb-3">
                    <label class="form-label">Data de emissão</label>
                    <input
                        type="date"
                        name="data_emissao"
                        class="form-control"
                        value="{{ old('data_emissao', now()->format('Y-m-d')) }}"
                    >
                </div>

                {{-- URL do certificado (opcional) --}}
                <div class="mb-3">
                    <label class="form-label">URL do certificado (opcional)</label>
                    <input
                        type="text"
                        name="url_certificado"
                        class="form-control"
                        value="{{ old('url_certificado') }}"
                        placeholder="Ex: https://meusistema.com/certificados/arquivo.pdf"
                    >
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('certificado-modelos.index', $eventoParam) }}"
                       class="btn btn-outline-secondary">
                        Voltar
                    </a>
                    <button type="submit" class="btn btn-primary" @if($modelos->isEmpty()) disabled @endif>
                        Salvar certificado
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
