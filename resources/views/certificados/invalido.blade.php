@extends('layouts.app')

@section('title', 'Certificado inválido')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow border-danger">
                <div class="card-header bg-danger text-white text-center">
                    <h4 class="mb-0">❌ Certificado inválido</h4>
                </div>

                <div class="card-body text-center">
                    <p>O código informado <strong>não corresponde</strong> a nenhum certificado emitido.</p>
                    <p>Verifique se o link ou QR Code está correto.</p>
                </div>

                <div class="card-footer text-center">
                    <a href="{{ route('front.home') }}" class="btn btn-outline-primary">
                        Voltar ao início
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
