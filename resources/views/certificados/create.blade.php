@extends('layouts.app')

@section('title', isset($evento) ? 'Gerar certificado - ' . $evento->nome : 'Gerar certificado')

@section('content')
<div class="container py-5">

    @php
        $eventoAtual = $evento ?? null;
        $eventoParam = $eventoAtual ? ['evento_id' => $eventoAtual->id] : [];
    @endphp

    {{-- Abas --}}
    <ul class="nav nav-tabs mb-4">
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

        <li class="nav-item">
            <a class="nav-link"
               href="{{ route('certificados.index', $eventoParam) }}">
                Certificados gerados
                @isset($eventoAtual)
                    <small class="text-muted ms-1">({{ $eventoAtual->nome }})</small>
                @endisset
            </a>
        </li>
    </ul>


    {{-- CARD 1 — EMISSÃO INDIVIDUAL --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h2 class="mb-0">
                Gerar certificado
                @isset($eventoAtual)
                    <small class="text-muted">— {{ $eventoAtual->nome }}</small>
                @endisset
            </h2>
            <p class="text-muted mb-0">
                Preencha os dados abaixo para gerar um certificado individual.
            </p>
        </div>

        <div class="card-body">

            {{-- Alerts --}}
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

            @isset($eventoAtual)
                <div class="alert alert-secondary py-2">
                    <strong>Evento selecionado:</strong> {{ $eventoAtual->nome }}
                </div>
            @endisset

            @if($modelos->isEmpty())
                <div class="alert alert-warning">
                    Ainda não há modelos de certificado cadastrados.
                </div>
            @endif

            {{-- FORM INDIVIDUAL --}}
            <form action="{{ route('certificados.store') }}" method="post">
                @csrf

                @isset($eventoAtual)
                    <input type="hidden" name="evento_id" value="{{ $eventoAtual->id }}">
                @endisset


                {{-- Inscrição --}}
                <div class="mb-3">
                    <label class="form-label">Inscrição *</label>
                    <select name="inscricao_id" class="form-select" required>
                        <option value="">Selecione...</option>

                        @foreach($inscricoes as $insc)
                            @php
                                $user = $insc->user ?? $insc->usuario;
                                $eventoLinha = $insc->evento;
                            @endphp

                            <option value="{{ $insc->id }}" @selected(old('inscricao_id')==$insc->id)>
                                [{{ $eventoLinha?->nome ?? 'Sem evento' }}] - {{ $user?->name ?? 'Sem usuário' }}
                                ({{ $user?->email }})
                            </option>
                        @endforeach
                    </select>

                    <small class="text-muted">Apenas inscrições com check-in realizado aparecem aqui.</small>
                </div>


                {{-- Modelo --}}
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


                {{-- Data --}}
                <div class="mb-3">
                    <label class="form-label">Data de emissão</label>
                    <input type="date" name="data_emissao" class="form-control"
                           value="{{ old('data_emissao', now()->format('Y-m-d')) }}">
                </div>

                {{-- URL --}}
                <div class="mb-3">
                    <label class="form-label">URL do certificado (opcional)</label>
                    <input type="text"
                           name="url_certificado"
                           class="form-control"
                           placeholder="https://meusistema.com/certificados/arquivo.pdf"
                           value="{{ old('url_certificado') }}">
                </div>


                <div class="d-flex justify-content-between">
                    <a href="{{ route('certificado-modelos.index', $eventoParam) }}"
                       class="btn btn-outline-secondary">
                        Voltar
                    </a>

                    <button type="submit"
                            class="btn btn-primary"
                            @if($modelos->isEmpty()) disabled @endif>
                        Salvar certificado
                    </button>
                </div>

            </form>
        </div>
    </div>



    {{-- CARD 2 — GERAÇÃO EM MASSA --}}
    @isset($eventoAtual)
    <div class="card shadow-sm">
        <div class="card-header">
            <h2 class="mb-0">
                Gerar em Massa
                <small class="text-muted">— {{ $eventoAtual->nome }}</small>
            </h2>
            <p class="text-muted mb-0">
                Gere certificados automaticamente para todos os participantes com check-in confirmado.
            </p>
        </div>

        <div class="card-body">

            <form action="{{ route('certificados.gerar_todos', $eventoAtual->id) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-bold">Modelo para Todos *</label>
                    <select name="modelo_id" class="form-select" required>
                        <option value="">Selecione o modelo...</option>
                        @foreach($modelos as $modelo)
                            <option value="{{ $modelo->id }}">{{ $modelo->titulo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit"
                            class="btn btn-primary"
                            @if($modelos->isEmpty()) disabled @endif>
                        Salvar todos
                    </button>
                </div>

            </form>

        </div>
    </div>
    @endisset


</div>
@endsection