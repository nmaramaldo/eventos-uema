<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Eventos - UEMA</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header img {
            height: 70px;
        }

        .header h1 {
            margin: 5px 0 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 10px;
            color: #555;
        }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('new-event/images/uema-logo.png') }}" alt="UEMA">
        <h1>Universidade Estadual do Maranhão</h1>
        <h2>Relatório de Eventos</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>Evento</th>
                <th>Status</th>
                <th>Período</th>
                <th>Inscritos</th>
                <th>Tipo</th>
                <th>Classificação</th>
                <th>Área Temática</th>
            </tr>
        </thead>
       <tbody>
            @forelse ($eventos as $evento)
                <tr>
                    <td>{{ $evento->nome }}</td>
                    <td>{{ ucfirst($evento->status) }}</td>
                    <td>{{ $evento->data_inicio_evento->format('d/m/Y') }} - {{ $evento->data_fim_evento->format('d/m/Y') }}</td>
                    <td>{{ $evento->inscricoes_count }}</td>
                    <td>{{ $evento->tipo_evento }}</td>
                    <td>{{ $evento->tipo_classificacao }}</td>
                    <td>{{ $evento->area_tematica }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Nenhum evento encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Gerado automaticamente pelo sistema UEMA — {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
