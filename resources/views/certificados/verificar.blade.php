@extends('layouts.app')

@section('title', 'Validação de Certificado')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">✅ Certificado Válido</h4>
                </div>

                <div class="card-body">
                    <p><strong>Participante:</strong> {{ $certificado->inscricao->user->name }}</p>
                    <p><strong>Evento:</strong> {{ $certificado->inscricao->evento->nome }}</p>
                    <p><strong>Data de Emissão:</strong>
                        {{ $certificado->data_emissao?->format('d/m/Y') }}
                    </p>
                    <p><strong>Código de Verificação:</strong></p>
                    <code>{{ $certificado->hash_verificacao }}</code>
                </div>

                <div class="card-footer text-center">
                    <span class="badge bg-success">Documento Autêntico</span>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
