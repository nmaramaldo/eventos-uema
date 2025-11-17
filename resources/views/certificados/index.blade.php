@extends('layouts.app')
@section('title', 'Certificados')

@section('content')
<div class="container py-5">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center">
            <div>
                <h2 class="mb-0">Certificados</h2>
                <p class="text-muted mb-0">
                    Lista de certificados já emitidos
                    @can('manage-users')
                        (admin).
                    @else
                        do seu usuário.
                    @endcan
                </p>
            </div>

            {{-- 🔒 Só admin/master pode ver o botão de gerar --}}
            @can('manage-users')
                <a href="{{ route('certificados.create') }}" class="btn btn-primary ms-auto">
                    Gerar certificado
                </a>
            @endcan
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">ID</th>
                            <th class="px-3">Participante</th>
                            <th class="px-3">Evento</th>
                            <th class="px-3">Modelo</th>
                            <th class="px-3">Data de emissão</th>
                            <th class="px-3">URL</th>
                            <th class="px-3 text-end" style="width: 240px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($certificados as $c)
                            @php
                                $insc   = $c->inscricao ?? null;
                                $user   = $insc?->user;
                                $evento = $insc?->evento;
                                $modelo = $c->modelo ?? null;
                            @endphp
                            <tr>
                                <td class="px-3">
                                    <code class="small">{{ $c->id }}</code>
                                </td>
                                <td class="px-3">{{ $user?->name ?? '—' }}</td>
                                <td class="px-3">{{ $evento?->nome ?? '—' }}</td>
                                <td class="px-3">{{ $modelo?->titulo ?? '—' }}</td>
                                <td class="px-3">
                                    {{ $c->data_emissao ? \Carbon\Carbon::parse($c->data_emissao)->format('d/m/Y') : '—' }}
                                </td>
                                <td class="px-3">
                                    @if(!empty($c->url_certificado))
                                        <a href="{{ $c->url_certificado }}" target="_blank">Abrir</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-3 text-end">
                                    {{-- 🔽 Botão de download – aparece pra TODO mundo --}}
                                    <a href="{{ route('certificados.download', $c) }}"
                                       class="btn btn-sm btn-success">
                                        Baixar
                                    </a>

                                    {{-- 🔒 Só admin/master pode ver "Ver" e "Excluir" --}}
                                    @can('manage-users')
                                        <a href="{{ route('certificados.show', $c) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            Ver
                                        </a>

                                        <form action="{{ route('certificados.destroy', $c) }}"
                                              method="post"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Deseja realmente excluir este certificado?')">
                                                Excluir
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted p-4">
                                    Nenhum certificado encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($certificados->hasPages())
            <div class="card-footer">
                {{ $certificados->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
