<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'UEMA Eventos'))</title>

    <!-- Bootstrap + Icons (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root{
            --brand-1:#0d47a1;
            --brand-2:#1976d2;
        }
        .auth-left{
            background: linear-gradient(135deg,var(--brand-1),var(--brand-2));
            color:#fff;
        }
        .auth-card{ max-width: 460px; width:100% }
        .form-control:focus{ box-shadow: none }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid">
    <div class="row min-vh-100">
        <!-- Lado esquerdo (hero) -->
        <div class="col-lg-6 d-none d-lg-flex auth-left align-items-center">
            <div class="px-5 w-100">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <!-- ajuste o caminho do logo se necessário -->
                    <img src="{{ asset('new-event/images/uema-logo.png') }}" alt="UEMA" height="36">
                    <strong>Sistema de Eventos</strong>
                </div>
                <h1 class="fw-bold mb-3">Gerencie eventos, inscrições e certificados</h1>
                <p class="lead mb-0">Feito para a comunidade acadêmica da UEMA.</p>
            </div>
        </div>

        <!-- Lado direito (conteúdo) -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center py-5">
            <div class="auth-card">
                @yield('content')
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
