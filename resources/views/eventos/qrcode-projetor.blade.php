@extends('layouts.app')
@section('title', 'Auto Check-in')

@section('content')
<div class="container py-5 text-center">
    <div class="card shadow-lg border-0 mx-auto" style="max-width: 800px;">
        <div class="card-header bg-primary text-white py-4">
            <h1 class="fw-bold mb-0">AUTO CHECK-IN</h1>
            <p class="lead mb-0">{{ $evento->nome }}</p>
        </div>
        
        <div class="card-body py-5">
            <p class="fs-4 mb-4">Aponte a câmera do seu celular para confirmar sua presença:</p>

            <div class="d-inline-block p-4 bg-white border rounded shadow-sm mb-4">
                {{-- Exibe o QR Code gerado --}}
                {!! $qrcode !!}
            </div>

            <div class="alert alert-light border d-inline-block text-start">
                <strong>Instruções:</strong>
                <ol class="mb-0 ps-3">
                    <li>Faça login na plataforma.</li>
                    <li>Abra a câmera e leia o QR Code acima.</li>
                    <li>Aguarde a mensagem de confirmação.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection