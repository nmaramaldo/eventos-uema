@extends('layouts.app')
@section('title', 'Verificar Certificado')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-header">
            <h2 class="mb-0">Verificar Autenticidade do Certificado</h2>
        </div>
        <div class="card-body">
            @if (isset($certificado) && $certificado)
                @if ($certificado->expires_at && $certificado->expires_at->isPast())
                    <div class="alert alert-danger">
                        Este certificado expirou em {{ $certificado->expires_at->format('d/m/Y') }}.
                    </div>
                @else
                    <div class="alert alert-success">
                        Certificado Válido!
                    </div>
                    <dl class="row">
                        <dt class="col-sm-3">Participante</dt>
                        <dd class="col-sm-9">{{ $certificado->inscricao->user->name }}</dd>

                        <dt class="col-sm-3">Evento</dt>
                        <dd class="col-sm-9">{{ $certificado->inscricao->evento->nome }}</dd>

                        <dt class="col-sm-3">Data de emissão</dt>
                        <dd class="col-sm-9">{{ $certificado->data_emissao->format('d/m/Y') }}</dd>

                        <dt class="col-sm-3">Data de expiração</dt>
                        <dd class="col-sm-9">{{ $certificado->expires_at ? $certificado->expires_at->format('d/m/Y') : 'Não expira' }}</dd>
                    </dl>
                @endif
            @else
                <div class="alert alert-danger">
                    Certificado inválido ou não encontrado.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
