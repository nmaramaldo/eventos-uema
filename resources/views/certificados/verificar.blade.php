{{-- resources/views/certificados/verificar.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validação de Certificado - UEMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f3f4f6; }
        .card-validacao { 
            max-width: 600px; 
            margin: 60px auto; 
            border: none; 
            border-top: 6px solid #198754; /* Verde sucesso */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .icon-box {
            font-size: 60px;
            color: #198754;
            margin-bottom: 20px;
        }
        .data-label {
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .data-value {
            font-size: 1.1rem;
            font-weight: 500;
            color: #212529;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card card-validacao">
        <div class="card-body text-center p-5">
            
            {{-- Ícone de Check --}}
            <div class="icon-box">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
            </div>

            <h2 class="mb-2 fw-bold text-success">Certificado Autêntico</h2>
            <p class="text-muted mb-5">Este documento foi emitido oficialmente pela plataforma de eventos.</p>

            <div class="text-start bg-light p-4 rounded-3 border">
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="data-label">Participante</div>
                        <div class="data-value">{{ $certificado->inscricao->user->name ?? $certificado->inscricao->usuario->name }}</div>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <div class="data-label">Evento</div>
                        <div class="data-value">{{ $certificado->inscricao->evento->nome }}</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="data-label">Data de Emissão</div>
                        <div class="data-value">{{ $certificado->data_emissao->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="data-label">Tipo</div>
                        <div class="data-value text-capitalize">{{ $certificado->tipo ?? 'Participação' }}</div>
                    </div>

                    <div class="col-12">
                        <div class="data-label">Código de Validação</div>
                        <div class="data-value font-monospace text-primary">{{ $certificado->hash_verificacao }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('front.home') }}" class="btn btn-outline-secondary">Ir para página inicial</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>