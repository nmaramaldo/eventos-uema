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
        <a href="{{ route('eventos.index') }}" class="btn btn-outline-secondary ms-auto">
            Voltar para eventos
        </a>
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
