@extends('layouts.auth')
@section('title', 'Acesse sua conta')

@section('content')
  <div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow p-8">
      <div class="flex justify-center mb-6">
        <img src="{{ asset('new-event/images/uema-logo.png') }}" class="h-12" alt="UEMA">
      </div>
      <h2 class="text-center text-2xl font-semibold">Acesse sua conta</h2>

      @if (session('status'))
        <div class="mt-4 rounded-lg bg-green-50 text-green-700 px-3 py-2 text-sm">
          {{ session('status') }}
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf
        <div>
          <label class="block text-sm font-medium">E-mail</label>
          <input name="email" type="email" value="{{ old('email') }}" required autofocus
                 class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-600">
          @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label class="block text-sm font-medium">Senha</label>
            @if (Route::has('password.request'))
              <a class="text-sm text-blue-600 hover:underline" href="{{ route('password.request') }}">
                Esqueceu a senha?
              </a>
            @endif
          </div>
          <input name="password" type="password" required
                 class="mt-1 block w-full rounded-lg border-gray-300 focus:ring-2 focus:ring-blue-600">
          @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <label class="inline-flex items-center">
          <input type="checkbox" name="remember" class="rounded border-gray-300">
          <span class="ml-2 text-sm">Lembrar-me</span>
        </label>

        <button type="submit"
                class="w-full inline-flex justify-center rounded-lg bg-blue-600 text-white py-2.5 font-medium hover:bg-blue-700">
          Entrar
        </button>
      </form>

      @if (Route::has('register'))
        <p class="text-center text-sm mt-6">
          Ainda não tem conta?
          <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Crie uma agora</a>
        </p>
      @endif
    </div>
  </div>
@endsection
