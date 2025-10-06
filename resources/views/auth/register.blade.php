{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.auth')
@section('title', 'Criar conta')

@section('content')
  {{-- Apenas o CARD. O hero da esquerda já vem do layouts/auth --}}
  <div class="w-full max-w-md bg-white rounded-2xl shadow px-8 py-8">
    <div class="flex flex-col items-center mb-6">
      <img src="{{ asset('new-event/images/uema-logo.png') }}" class="h-12 mb-3" alt="UEMA">
      <h2 class="text-2xl font-semibold">Criar conta</h2>
    </div>

    {{-- Erros de validação --}}
    @if ($errors->any())
      <div class="mb-4 rounded-md bg-red-50 text-red-700 px-4 py-3 text-sm">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
      @csrf

      <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
        <input id="name" name="name" type="text" required autocomplete="name"
               value="{{ old('name') }}"
               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">
      </div>

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
        <input id="email" name="email" type="email" required autocomplete="username"
               value="{{ old('email') }}"
               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
        <input id="password" name="password" type="password" required autocomplete="new-password"
               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">
      </div>

      <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar senha</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">
      </div>

      <button type="submit"
              class="w-full rounded-xl bg-blue-600 px-4 py-3 text-white font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600">
        Registrar
      </button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
      Já tenho conta.
      <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-700">Entrar</a>
    </p>
  </div>
@endsection
