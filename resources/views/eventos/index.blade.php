@extends('layouts.app')
@section('title', 'Eventos')

@section('content')
<div class="container py-5">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">Eventos</h2>
                    <p class="text-muted mb-0">Lista de eventos cadastrados.</p>
                </div>
                @can('create', App\Models\Event::class)
                    <a href="{{ route('eventos.create.step1') }}" class="btn btn-primary">Novo Evento</a>
                @endcan
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">Nome</th>
                            <th class="px-3">Período do Evento</th>
                            <th class="px-3">Período de Inscrições</th>
                            <th class="px-3">Tipo</th>
                            <th class="px-3">Status</th>
                            <th class="px-3 text-end" style="width: 220px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($eventos as $e)
                            <tr>
                                <td class="px-3">
                                    <a href="{{ route('eventos.show', $e) }}"><strong>{{ $e->nome }}</strong></a>
                                </td>
                                <td class="px-3">{{ $e->periodo_evento }}</td>
                                <td class="px-3">{{ $e->periodo_inscricao }}</td>
                                <td class="px-3">{{ $e->tipo_evento ?? '—' }}</td>
                                <td class="px-3">
                                    <span class="badge bg-primary">{{ $e->status ?? 'Não definido' }}</span>
                                </td>
                                <td class="px-3 text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('eventos.show', $e) }}">Ver</a>
                                    @can('update', $e)
                                        <a class="btn btn-sm btn-primary" href="{{ route('eventos.edit', $e) }}">Editar</a>
                                    @endcan
                                    @can('delete', $e)
                                        <form action="{{ route('eventos.destroy', $e) }}" method="post" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover este evento?')">Excluir</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted p-5">Nenhum evento cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($eventos->hasPages())
            <div class="card-footer">
                {{ $eventos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
