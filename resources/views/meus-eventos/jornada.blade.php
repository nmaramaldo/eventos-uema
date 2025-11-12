@extends('layouts.app')
@section('title', 'Minha Jornada')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Minha Jornada</h2>

    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="mb-0">Eventos que Participei</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">Nome do Evento</th>
                            <th class="px-3">Período do Evento</th>
                            <th class="px-3">Status</th>
                            <th class="px-3 text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($eventos as $evento)
                            <tr>
                                <td class="px-3">
                                    <a href="{{ route('front.eventos.show', $evento) }}"><strong>{{ $evento->nome }}</strong></a>
                                </td>
                                <td class="px-3">{{ $evento->periodo_evento }}</td>
                                <td class="px-3">
                                    @php
                                        $map = [
                                            'Aberto'                    => 'success',
                                            'Inscrições encerradas'     => 'secondary',
                                            'Encerrado'                 => 'dark',
                                            'Não iniciado'              => 'warning',
                                            'Publicado'                 => 'primary',
                                            'Rascunho'                  => 'secondary',
                                        ];
                                        $rotulo = $evento->status_exibicao ?? ($evento->status ? ucfirst($evento->status) : '—');
                                        $cls = $map[$rotulo] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $cls }}">{{ $rotulo }}</span>
                                </td>
                                <td class="px-3 text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('front.eventos.show', $evento) }}">Ver Detalhes</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted p-5">Você ainda não participou de nenhum evento.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
