<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório - {{ $evento->nome }}</title>
    <style>
        /* Estilos básicos para o PDF */
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .container { width: 100%; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        
        /* CABEÇALHO DE EXPORTAÇÃO */
        .export-info {
            text-align: right;
            font-size: 9px;
            color: #555;
            margin-bottom: 5px;
        }

        /* Cabeçalho principal com a logo */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .header img { width: 150px; }
        .header h1 { margin: 10px 0 0 0; font-size: 24px; }
        .header h2 { margin: 0; font-size: 18px; color: #333; }
        
        .details { margin-bottom: 20px; }
        .details strong { display: inline-block; min-width: 120px; }
    </style>
</head>
<body>
    <div class="container">
        
        {{-- ✅ NOVO BLOCO DE HTML PARA AS INFORMAÇÕES DE EXPORTAÇÃO --}}
        <div class="export-info">
            Exportado por: {{ $usuarioExportador->name ?? 'N/A' }}<br>
            Data: {{ $dataExportacao->format('d/m/Y') }}<br>
            Hora: {{ $dataExportacao->format('H:i:s') }}
        </div>
        
        {{-- CABEÇALHO COM A LOGO DA UEMA --}}
        <div class="header">
            <img src="{{ public_path('new-event/images/uema-logo.png')}}" alt="Logo UEMA">
            <h1>Relatório de Evento</h1>
            <h2>{{ $evento->nome }}</h2>
        </div>

        {{-- Detalhes do Evento --}}
        <div class="details">
            <p><strong>Período:</strong> {{ $evento->periodo_evento }}</p>
            <p><strong>Classificação:</strong> {{ $evento->tipo_classificacao ?? 'N/A' }}</p>
            <p><strong>Total de Inscritos:</strong> {{ $participantes->count() }}</p>
        </div>

        {{-- LISTA DE PARTICIPANTES --}}
        <h3>Lista de Participantes</h3>
        <table>
            <thead>
                <tr>
                    <th>Nome do Participante</th>
                    <th>E-mail</th>
                    <th>Data da Inscrição</th>
                    <th>Status do Check-in</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participantes as $inscricao)
                <tr>
                    <td>{{ $inscricao->user->name ?? 'Usuário não encontrado' }}</td>
                    <td>{{ $inscricao->user->email ?? 'N/A' }}</td>
                    <td>{{ $inscricao->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($inscricao->checkin_at)
                            <span>Presente</span>
                        @else
                            <span>Ausente</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Nenhum participante inscrito.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>