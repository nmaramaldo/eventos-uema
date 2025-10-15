@extends('layouts.app')
@section('title', 'Palestrantes - ' . $evento->nome)

@section('content')
<div class="container py-5">
    {{-- Cabeçalho --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h3>Palestrantes — {{ $evento->nome }}</h3>
            <p class="text-muted">Gerencie os palestrantes do evento</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('eventos.palestrantes.create', $evento) }}" class="btn btn-primary">
                Adicionar palestrante
            </a>
            <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-secondary">
                Voltar
            </a>
        </div>
    </div>

    {{-- Mensagens de sucesso/erro --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Tabela --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Biografia</th>
                        <th class="text-end" style="width:140px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($palestrantes as $p)
                        <tr>
                            <td>{{ $p->nome }}</td>
                            <td>
                                @if($p->email)
                                    <a href="mailto:{{ $p->email }}">{{ $p->email }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($p->biografia)
                                    {{ Str::limit($p->biografia, 80) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('eventos.palestrantes.edit', ['evento' => $evento->id, 'palestrante' => $p->id]) }}">
                                    Editar
                                </a>
                                <form action="{{ route('eventos.palestrantes.destroy', ['evento' => $evento->id, 'palestrante' => $p->id]) }}" method="post" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Remover palestrante?')">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">
                                Nenhum palestrante cadastrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginação (se houver) --}}
        @if(method_exists($palestrantes, 'hasPages') && $palestrantes->hasPages())
            <div class="card-footer">
                {{ $palestrantes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
