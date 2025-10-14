@extends('layouts.guest')
@section('title', 'Acesse sua conta')

@section('content')
    <div class="text-center mb-4">
        <img src="{{ asset('new-event/images/uema-logo.png') }}" alt="UEMA" height="48" class="mb-2">
        <h3 class="mb-1">Acesse sua conta</h3>
        <div class="text-muted">Use seu e-mail institucional para acessar</div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

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

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control">
        </div>

        <div class="mb-2">
            <label class="form-label d-flex justify-content-between">
                <span>Senha</span>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="small">Esqueceu a senha?</a>
                @endif
            </label>
            <input type="password" name="password" required class="form-control">
        </div>

        <div class="form-check my-3">
            <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
            <label class="form-check-label" for="remember_me">Lembrar-me</label>
        </div>

        <button class="btn btn-primary w-100">Entrar</button>
    </form>

    @if (Route::has('register'))
        <div class="text-center mt-3">
            <small class="text-muted">Ainda não tem conta?
                <a href="{{ route('register') }}">Crie uma agora</a>
            </small>
        </div>
    @endif
@endsection
