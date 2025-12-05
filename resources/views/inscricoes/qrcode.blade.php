@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-2 text-center">QR Code para Check-in</h1>
            
            <div class="mt-4 text-center">
                <p class="text-gray-600">Apresente este QR Code na entrada do evento para validar sua presença.</p>
            </div>

            <div class="my-6 flex justify-center">
                {{-- Sintaxe para o pacote simplesoftwareio/simple-qrcode --}}
                {!! QrCode::size(300)->generate($inscricao->id) !!}
            </div>

            <div class="text-center text-sm text-gray-700">
                <p class="font-semibold">Evento:</p>
                <p>{{ $inscricao->evento->nome }}</p>
                <p class="mt-2 font-semibold">Participante:</p>
                <p>{{ $inscricao->user->name }}</p>
                <p class="mt-2 font-semibold">ID da Inscrição:</p>
                <p class="font-mono">{{ $inscricao->id }}</p>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-center">
            <a href="{{ route('meus-eventos.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                &larr; Voltar para Meus Eventos
            </a>
        </div>
    </div>
</div>
@endsection
