@extends('layouts.new-event')
@section('title','Eventos')

@section('content')
<div class="container" style="padding:60px 0; max-width:1100px">
  <div class="row" style="margin-bottom:12px">
    <div class="col-sm-8">
      <h2 style="margin-top:0">Eventos</h2>
      <p class="text-muted" style="margin:0">Lista de eventos cadastrados.</p>
    </div>
    <div class="col-sm-4 text-right">
      @can('manage-users')
        <a href="{{ route('eventos.create') }}" class="btn btn-primary">Novo Evento</a>
      @endcan
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-striped" style="margin-top:10px">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Período</th>
          <th>Inscrições</th>
          <th>Tipo</th>
          <th>Status</th>
          <th style="width:200px">Ações</th>
        </tr>
      </thead>
      <tbody>
        @forelse($eventos as $e)
          @php
            $status = strtolower((string)($e->status ?? ''));
            $statusClass = match ($status) {
              'ativo'      => 'label-success',
              'publicado'  => 'label-primary',
              'rascunho'   => 'label-default',
              default      => 'label-default',
            };
          @endphp

          <tr>
            <td>
              <a href="{{ route('eventos.show', $e) }}"><strong>{{ $e->nome }}</strong></a>
              @if($e->inscricoesAbertas())
                <span class="label label-success" style="margin-left:6px">Inscrições abertas</span>
              @endif
            </td>
            <td>{{ $e->periodo_evento }}</td>
            <td>{{ $e->periodo_inscricao }}</td>
            <td>{{ $e->tipo_evento ?? '—' }}</td>
            <td><span class="label {{ $statusClass }}">{{ $e->status ? ucfirst($e->status) : '—' }}</span></td>
            <td>
              <a class="btn btn-xs btn-default" href="{{ route('eventos.show', $e) }}">Ver</a>
              @can('manage-users')
                <a class="btn btn-xs btn-primary" href="{{ route('eventos.edit', $e) }}">Editar</a>
                {{-- Para habilitar exclusão, remova o comentário abaixo e garanta o destroy() ativo na controller
                <form action="{{ route('eventos.destroy', $e) }}" method="post" style="display:inline">
                  @csrf @method('DELETE')
                  <button class="btn btn-xs btn-danger" onclick="return confirm('Remover este evento?')">Excluir</button>
                </form>
                --}}
              @endcan
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-muted">Nenhum evento cadastrado.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="text-center">
    {{ $eventos->links() }}
  </div>
</div>
@endsection
