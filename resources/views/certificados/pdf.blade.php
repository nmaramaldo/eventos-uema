<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Certificado</title>

    <style>
        @page {
            margin: 0px;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }

        /* Container principal A4 */
        .page-container {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        .certificate-border {
            position: absolute;
            top: 1cm;
            bottom: 1cm;
            left: 1cm;
            right: 1cm;
            border: 6px solid #1d4ed8;
            padding: 20px 40px;
        }

        .header {
            text-align: center;
            margin-top: 20px;
        }

        .header img {
            height: 60px;
            margin-bottom: 5px;
        }

        .title {
            font-size: 32px;
            letter-spacing: 0.15em;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 14px;
            color: #475569;
            margin-bottom: 40px;
        }

        .body-text {
            font-size: 18px;
            line-height: 1.5;
            text-align: justify;
            margin-bottom: 30px;
            min-height: 150px;
        }

        .signatures {
            width: 100%;
            text-align: center;
            margin-top: 40px;
        }

        .signature-block {
            display: inline-block;
            width: 40%;
            vertical-align: top;
        }

        .signature-line {
            border-top: 1px solid #475569;
            margin: 0 auto 5px auto;
            width: 80%;
        }

        /* BLOCO DO QR CODE + VALIDAÇÃO */
        .validation-block {
            position: absolute;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }

        .validation-block img {
            width: 70px;
            height: 70px;
            margin-bottom: 6px;
        }

        .footer {
            position: absolute;
            bottom: 30px;
            left: 40px;
            right: 40px;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>

<body>

@php
    $inscricao = $certificado->inscricao;
    $user      = $inscricao?->user ?? $inscricao?->usuario;
    $evento    = $inscricao?->evento;

    $dataEmissao = $certificado->data_emissao
        ? $certificado->data_emissao->format('d/m/Y')
        : now()->format('d/m/Y');

    $hash = $certificado->hash_verificacao;

    $urlValidacaoTexto = isset($urlValidacao)
        ? $urlValidacao
        : route('certificados.verificar', $hash);

    $logoPath = null;
    if (!empty($evento?->logomarca_path)) {
        $tmp = public_path('storage/' . $evento->logomarca_path);
        if (file_exists($tmp)) $logoPath = $tmp;
    }
    if (!$logoPath) {
        $tmp = public_path('images/logo-uema.png');
        if (file_exists($tmp)) $logoPath = $tmp;
    }
@endphp

<div class="page-container">
    <div class="certificate-border">

        <!-- HEADER -->
        <div class="header">
            @if($logoPath)
                <img src="{{ $logoPath }}" alt="Logo">
            @endif

            <div class="title">Certificado</div>

            @if($evento)
                <div class="subtitle">{{ $evento->nome }}</div>
            @endif
        </div>

        <!-- TEXTO -->
        <div class="body-text">
            {!! $certificado->texto_renderizado !!}
        </div>

        <!-- ASSINATURAS -->
        <div class="signatures">
            <div class="signature-block">
                <div class="signature-line"></div>
                <div><strong>{{ $evento?->coordenador?->name ?? 'Coordenador(a)' }}</strong></div>
                <div style="font-size: 12px;">Coordenação</div>
            </div>

            <div class="signature-block">
                <div class="signature-line"></div>
                <div><strong>{{ $evento?->owner?->name ?? 'Organizador(a)' }}</strong></div>
                <div style="font-size: 12px;">Organização</div>
            </div>
        </div>

        <!-- QR CODE + CÓDIGO DE VALIDAÇÃO -->
        <div class="validation-block">
            @if(isset($qrCode))
                <img src="data:image/svg+xml;base64,{{ $qrCode }}">
            @endif

            <div>Código de validação</div>
            <div><strong>{{ $hash }}</strong></div>

            <div style="margin-top: 4px;">
                <a href="{{ $urlValidacaoTexto }}" style="color: #6b7280; text-decoration: none;">
                    Verificar autenticidade
                </a>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            Emitido em {{ $dataEmissao }}<br>
            Plataforma Eventos UEMA
        </div>

    </div>
</div>

</body>
</html>
