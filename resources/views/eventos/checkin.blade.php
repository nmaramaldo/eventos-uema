@extends('layouts.app')
@section('title', 'Check-in - ' . $evento->nome)

@section('content')
<div class="container py-5">
    {{-- Mensagens de feedback --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-0">Check-in — {{ $evento->nome }}</h2>
            <p class="text-muted mb-0">Credenciamento geral dos participantes inscritos no evento.</p>
        </div>
        <div class="ms-auto d-flex gap-2">
           <a href="{{ route('admin.eventos.qrcode.exibir', $evento) }}" target="_blank" class="btn btn-primary">
                <i class="bi bi-projector me-2"></i>
                Projetar QR Code (Telão)
            </a>
            <a href="{{ route('eventos.index') }}" class="btn btn-outline-secondary">
                Voltar para eventos
            </a>

                        
        </div>
    </div>

    {{-- Card de Geração de Certificados --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0">Gerar Certificados em Massa</h5>
        </div>
        <div class="card-body">
            <p class="card-text">
                Selecione um modelo de certificado e clique em "Gerar" para emitir os certificados para todos os
                participantes com check-in realizado (presentes) neste evento.
            </p>
            <p class="text-muted small">
                Atenção: Apenas participantes marcados como "Presente" receberão o certificado. Certificados que já foram emitidos para um participante com o mesmo modelo não serão gerados novamente.
            </p>

            @if($modelos->count() > 0)
                <form action="{{ route('certificados.gerar_todos', $evento) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja gerar os certificados para todos os presentes? Esta ação não pode ser desfeita.');">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <label for="modelo_id" class="form-label">Modelo de Certificado</label>
                            <select name="modelo_id" id="modelo_id" class="form-select" required>
                                <option value="">Selecione um modelo</option>
                                @foreach($modelos as $modelo)
                                    <option value="{{ $modelo->id }}">{{ $modelo->titulo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-patch-check-fill me-2"></i>
                                Gerar para todos os presentes
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <div class="alert alert-warning mb-0">
                    Não há modelos de certificado publicados para este evento.
                    <a href="{{ route('certificado-modelos.create', ['evento_id' => $evento->id]) }}" class="alert-link">Crie um modelo de certificado</a>
                    primeiro.
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <div class="row g-2 align-items-center">
                <div class="col-md-6">
                    <strong>Total de inscrições:</strong> {{ $inscricoes->count() }}
                </div>

                {{-- 🔍 Campo de busca por nome ou e-mail --}}
                <div class="col-md-6 text-md-end">
                    <label class="form-label mb-1">Pesquisar inscrito</label>
                    <input
                        type="text"
                        id="search-inscrito"
                        class="form-control"
                        placeholder="Digite nome ou e-mail para filtrar..."
                    >
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tabela-inscricoes">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Data de inscrição</th>
                            <th>Status</th>
                            <th>Presença (Check-in)</th>
                            <th class="text-end" style="width: 140px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inscricoes as $insc)
                            @php
                                $u   = $insc->usuario ?? $insc->user;
                                $nome  = $u?->name ?? '—';
                                $email = $u?->email ?? '—';
                            @endphp
                            <tr
                                class="linha-inscrito"
                                data-nome="{{ Str::lower($nome) }}"
                                data-email="{{ Str::lower($email) }}"
                            >
                                <td>{{ $nome }}</td>
                                <td>{{ $email }}</td>
                                <td>
                                    {{ optional($insc->data_inscricao)->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td>{{ ucfirst($insc->status ?? 'ativa') }}</td>
                                <td>
                                    @if($insc->presente)
                                        <span class="badge bg-success">Presente</span>
                                    @else
                                        <span class="badge bg-secondary">Não presente</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <form
                                        action="{{ route('eventos.checkin.toggle', ['evento' => $evento->id, 'inscricao' => $insc->id]) }}"
                                        method="post"
                                    >
                                        @csrf
                                        <button
                                            class="btn btn-sm {{ $insc->presente ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                            onclick="return confirm('Confirmar {{ $insc->presente ? 'remoção' : 'registro' }} de presença deste participante?')"
                                        >
                                            {{ $insc->presente ? 'Desmarcar' : 'Fazer check-in' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted p-4">
                                    Nenhuma inscrição encontrada para este evento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('search-inscrito');
    const linhas = Array.from(document.querySelectorAll('#tabela-inscricoes .linha-inscrito'));

    if (!input || linhas.length === 0) {
        return;
    }

    input.addEventListener('input', function () {
        const termo = this.value.trim().toLowerCase();

        // Se não digitou nada, mostra tudo
        if (!termo) {
            linhas.forEach(l => l.style.display = '');
            return;
        }

        linhas.forEach(linha => {
            const nome  = (linha.dataset.nome  || '').toLowerCase();
            const email = (linha.dataset.email || '').toLowerCase();

            const match = nome.includes(termo) || email.includes(termo);
            linha.style.display = match ? '' : 'none';
        });
    });
});
</script>
@endpush
@endsection
