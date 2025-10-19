<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'UEMA Eventos')</title>

  @vite([
    'resources/css/app.scss',
    'resources/js/app.js',
    'resources/template/new-event/css/new-event.css',
    'resources/template/new-event/js/new-event.js',
  ])
</head>
<body class="bg-light">

  {{-- Navbar ÚNICA --}}
  @include('layouts.navigation')

  <main class="py-4">
    @yield('content')
  </main>

  <footer class="mt-auto py-3 bg-light">
    <div class="container">
        <p class="text-center text-muted">Versão: {{ $gitVersion ?? 'N/A' }}</p>
    </div>
  </footer>

  @stack('scripts')
</body>
</html>
