{{-- resources/views/certificados/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Certificado</title>
    <style>
        @page {
            margin: 0cm 0cm;
        }

        body {
            margin: 2.5cm 2.5cm;
            font-family: "DejaVu Sans", sans-serif;
            color: #0f172a; /* azul bem escuro */
        }

        .certificate-border {
            border: 6px solid #1d4ed8; /* azul do sistema */
            padding: 40px 60px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header img {
            height: 60px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 30px;
            letter-spacing: 0.25em;
            font-weight: bold;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        .subtitle {
            font-size: 13px;
            color: #475569;
            margin-bottom: 30px;
        }

        .body-text {
            font-size: 16px;
            line-height: 1.6;
            text-align: justify;
        }

        .signatures {
            margin-top: 60px;
            text-align: center;
        }

        .signature-block {
            display: inline-block;
            width: 45%;
            font-size: 12px;
            margin: 0 2%;
        }

        .signature-line {
            margin-top: 40px;
            border-top: 1px solid #475569;
            margin-bottom: 4px;
        }

        .footer {
            margin-top: 40px;
            font-size: 10px;
            color: #6b7280;
        }

        .footer-left {
            float: left;
        }

        .footer-right {
            float: right;
            text-align: right;
        }
    </style>
</head>
<body>
@php
    $inscricao = $certificado->inscricao;
    $user      = $inscricao?->user ?? $inscricao?->usuario;
    $evento    = $inscricao?->evento;
    $modelo    = $certificado->modelo;

    $dataEmissao = $certificado->data_emissao
        ? $certificado->data_emissao->format('d/m/Y')
        : now()->format('d/m/Y');

    $hash = $certificado->hash_verificacao;

    // tenta usar a logomarca do evento; se não tiver, usa logo fixa da UEMA
    $logoPath = null;
    if (!empty($evento?->logomarca_path)) {
        $tmp = public_path('storage/' . $evento->logomarca_path);
        if (file_exists($tmp)) {
            $logoPath = $tmp;
        }
    }

    if (!$logoPath) {
        // ajuste esse caminho para onde estiver a logo oficial da UEMA no seu projeto
        $tmp = public_path('images/logo-uema.png');
        if (file_exists($tmp)) {
            $logoPath = $tmp;
        }
    }
@endphp

<div class="certificate-border">
    <div class="header">
        @if($logoPath)
            <img src="{{ $logoPath }}" alt="Logo UEMA">
        @endif

        <div class="title">CERTIFICADO</div>

        @if($evento)
            <div class="subtitle">
                {{ $evento->nome }}
            </div>
        @endif
    </div>

    <div class="body-text">
        {{-- Usa o texto do modelo com as tags já substituídas --}}
        {!! $certificado->texto_renderizado !!}
    </div>

    <div class="signatures">
        <div class="signature-block">
            <div class="signature-line"></div>
            <div>{{ $evento?->coordenador?->name ?? 'Coordenador(a) do Evento' }}</div>
            <div>Coordenação</div>
        </div>

        <div class="signature-block">
            <div class="signature-line"></div>
            <div>{{ $evento?->owner?->name ?? 'Organizador(a) Responsável' }}</div>
            <div>Organização</div>
        </div>
    </div>

    <div class="footer">
        <div class="footer-left">
            Emitido em {{ $dataEmissao }}
        </div>
        <div class="footer-right">
            Código de verificação: {{ $hash }}<br>
            Valide em {{ rtrim(config('app.url'), '/') }}/verificar-certificado
        </div>
    </div>
</div>
</body>
</html>
