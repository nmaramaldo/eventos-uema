{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.guest')
@section('title', 'Criar conta')

@section('content')
    <div class="text-center mb-4">
        <img src="{{ asset('new-event/images/uema-logo.png') }}" alt="UEMA" height="48" class="mb-2">
        <h3 class="mb-1">Criar conta</h3>
        <div class="text-muted">Preencha os dados abaixo para se registrar</div>
    </div>

    {{-- Mensagens de erro --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Ops! Verifique os campos abaixo:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus class="form-control">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="form-control">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input id="password" name="password" type="password" required class="form-control">
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirmar senha</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100">Registrar</button>
    </form>

    <div class="text-center mt-3">
        <small class="text-muted">
            Já tem conta?
            <a href="{{ route('login') }}">Entre aqui</a>
        </small>
    </div>
@endsection
