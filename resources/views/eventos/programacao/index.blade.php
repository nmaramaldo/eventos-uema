@extends('layouts.app')
@section('title', 'Programação - ' . $evento->nome)

@section('content')
    <div class="container py-5">
        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <h3>Programação — {{ $evento->nome }}</h3>
                <p class="text-muted">Gerencie as atividades (palestras, mesas, oficinas...)</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('eventos.programacao.create', $evento) }}" class="btn btn-primary">Adicionar atividade</a>
                <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-secondary">Voltar</a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Título</th>
                            <th>Início</th>
                            <th>Fim</th>
                            <th>Local</th>
                            <th class="text-end" style="width:140px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($itens as $i)
                            <tr>
                                <td>{{ $i->titulo }}</td>
                                <td>
                                    @if ($i->data_hora_inicio)
                                        {{ \Carbon\Carbon::parse($i->data_hora_inicio)->format('d/m/Y H:i') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if ($i->data_hora_fim)
                                        {{ \Carbon\Carbon::parse($i->data_hora_fim)->format('d/m/Y H:i') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $i->local?->nome ?? ($i->localidade ?? '—') }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary"
                                        href="{{ route('eventos.programacao.edit', ['evento' => $evento->id, 'atividade' => $i->id]) }}">Editar</a>
                                    <form
                                        action="{{ route('eventos.programacao.destroy', ['evento' => $evento->id, 'atividade' => $i->id]) }}"
                                        method="post" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Remover?')">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Nenhuma atividade adicionada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
