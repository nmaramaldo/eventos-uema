@extends('layouts.app')
@section('title', 'Certificado')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Certificado</h2>
            <div class="d-flex gap-2">
                {{-- Botão de download direto na tela de visualização --}}
                <a href="{{ route('certificados.download', $certificado) }}"
                   class="btn btn-success"
                   target="_blank">
                    Baixar certificado
                </a>
                <a href="{{ route('certificados.index') }}" class="btn btn-outline-secondary">Voltar</a>
            </div>
        </div>

        <div class="card-body">
            @php
                $insc   = $certificado->inscricao ?? null;
                $user   = $insc?->user;
                $evento = $insc?->evento;
                $modelo = $certificado->modelo ?? null;
            @endphp

            <dl class="row">
                <dt class="col-sm-3">Participante</dt>
                <dd class="col-sm-9">{{ $user?->name ?? '—' }} ({{ $user?->email ?? '—' }})</dd>

                <dt class="col-sm-3">Evento</dt>
                <dd class="col-sm-9">{{ $evento?->nome ?? '—' }}</dd>

                <dt class="col-sm-3">Modelo</dt>
                <dd class="col-sm-9">{{ $modelo?->titulo ?? '—' }}</dd>

                <dt class="col-sm-3">Data de emissão</dt>
                <dd class="col-sm-9">
                    {{ $certificado->data_emissao ? \Carbon\Carbon::parse($certificado->data_emissao)->format('d/m/Y') : '—' }}
                </dd>

                <dt class="col-sm-3">URL do certificado</dt>
                <dd class="col-sm-9">
                    @if(!empty($certificado->url_certificado))
                        <a href="{{ $certificado->url_certificado }}" target="_blank">
                            {{ $certificado->url_certificado }}
                        </a>
                    @else
                        —
                    @endif
                </dd>
            </dl>
        </div>
    </div>
</div>
@endsection
