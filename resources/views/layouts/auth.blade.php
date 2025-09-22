<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'Login – UEMA')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50">
  <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
    {{-- LADO ESQUERDO (gradiente + texto) --}}
    <aside class="hidden lg:flex flex-col justify-center px-12 bg-gradient-to-br from-blue-700 to-blue-900 text-white">
      <div class="flex items-center gap-3 mb-10">
        <img src="{{ asset('new-event/images/uema-logo.png') }}" class="h-9" alt="UEMA">
        <div class="font-semibold">Sistema de Eventos</div>
      </div>
      <h1 class="text-4xl font-extrabold leading-tight">Gerencie eventos, inscrições e certificados</h1>
      <p class="mt-4 text-white/90">Feito para a comunidade acadêmica da UEMA</p>
    </aside>

    {{-- LADO DIREITO (formulário) --}}
    <main class="flex items-center justify-center p-6">
      @yield('content')
    </main>
  </div>
</body>
</html>
