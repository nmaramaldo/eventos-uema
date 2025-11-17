@extends('layouts.app')
@section('title', 'Modelos de certificado')

@section('content')
<div class="container py-4">

    @php
        // pega o evento_id da URL (quando você vem do botão "Certificados" em Gerenciar Eventos)
        $eventoId   = request('evento_id');
        $eventoParam = $eventoId ? ['evento_id' => $eventoId] : [];
    @endphp

    {{-- Abas: Modelos / Emitir certificados --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" href="#">
                Modelos de certificado
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link"
               href="{{ route('certificados.create', $eventoParam) }}">
                Emitir certificados
            </a>
        </li>
    </ul>

    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center">
            <div>
                <h3 class="mb-0">Modelos de certificado</h3>
                <small class="text-muted">
                    Vinculados aos eventos.
                </small>
            </div>

            {{-- botão "Novo modelo" preservando o evento_id, se existir --}}
            <a href="{{ route('certificado-modelos.create', $eventoParam) }}"
               class="btn btn-primary ms-auto">
                Novo modelo
            </a>
        </div>

        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Evento</th>
                        <th>Título</th>
                        <th>Categoria</th>
                        <th>Atribuição</th>
                        <th>Publicado?</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($modelos as $m)
                        <tr>
                            <td>{{ $m->evento?->nome ?? '—' }}</td>
                            <td>{{ $m->titulo }}</td>
                            <td>{{ ucfirst($m->slug_tipo) }}</td>
                            <td>{{ $m->atribuicao ?? '—' }}</td>
                            <td>
                                @if($m->publicado)
                                    <span class="badge bg-success">Publicado</span>
                                @else
                                    <span class="badge bg-secondary">Rascunho</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('certificado-modelos.edit', $m) }}"
                                   class="btn btn-sm btn-primary">
                                    Editar
                                </a>
                                <form action="{{ route('certificado-modelos.destroy', $m) }}"
                                      method="post"
                                      class="d-inline"
                                      onsubmit="return confirm('Deseja realmente excluir este modelo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Nenhum modelo cadastrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p class="text-center text-muted mt-4">Versão: 1.0-rc.1</p>
</div>
@endsection
