<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'UEMA Eventos')</title>

    {{-- CSS do template (compilado via Vite) --}}
    @vite('resources/template/new-event/css/new-event.css')

    {{-- Ponto de extensão para páginas que precisem CSS extra (DataTables, Select2, etc.) --}}
    @stack('styles')

    {{-- Se o Tailwind/Breeze conflitar com o template nas páginas públicas, mantenha comentado. 
         Caso queira usar alguns componentes do Breeze em páginas específicas,
         carregue o app.css com @push('styles') somente nessas páginas. --}}
    {{-- @vite('resources/css/app.css') --}}
    @yield('head') {{-- espaço para metas/heads específicos de páginas --}}
</head>
<body class="@yield('body_class')" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">

    {{-- Navbar padrão do template --}}
    @include('partials.navbar-new-event')

    {{-- Espaçador por causa da navbar fixa --}}
    <div style="height:70px"></div>

    {{-- Mensagens globais (flash + validação) --}}
    <div class="container" style="max-width:1140px;">
        @include('partials.alerts')
    </div>

    {{-- Conteúdo da página --}}
    @yield('content')

    {{-- Rodapé padrão do template --}}
    @include('partials.footer-new-event')

    {{-- JS do template (compilado via Vite).
         IMPORTANTE: este bundle deve importar jQuery antes do bootstrap.min.js.
         Se o dropdown da navbar não abrir, confirme a ordem no seu new-event.js. --}}
    @vite('resources/template/new-event/js/new-event.js')

    {{-- Ponto de extensão para JS de páginas (DataTables, máscaras, etc.) --}}
    @stack('scripts')

    {{-- Se precisar dos scripts do Breeze/Alpine apenas em páginas específicas,
         use @push('scripts') nessas páginas. --}}
    {{-- @vite('resources/js/app.js') --}}
</body>
</html>
