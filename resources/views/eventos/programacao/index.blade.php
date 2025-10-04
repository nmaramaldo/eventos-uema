@extends('layouts.new-event')
@section('title','Programação - '.$evento->nome)

@section('content')
<div class="container" style="padding:30px 0; max-width:1100px">
  <div class="row" style="margin-bottom:12px">
    <div class="col-sm-8">
      <h3 style="margin-top:0">Programação — {{ $evento->nome }}</h3>
      <p class="text-muted">Gerencie as atividades (palestras, mesas, oficinas...).</p>
    </div>
    <div class="col-sm-4 text-right">
      <a href="{{ route('eventos.programacao.create', $evento) }}" class="btn btn-primary">Adicionar atividade</a>
      <a href="{{ route('eventos.show', $evento) }}" class="btn btn-default">Voltar</a>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading"><strong>Atividades</strong></div>
    <div class="panel-body" style="padding:0">
      <table class="table table-striped" style="margin:0">
        <thead>
          <tr>
            <th>Título</th>
            <th>Início</th>
            <th>Fim</th>
            <th>Local</th>
            <th width="110"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($itens as $i)
            <tr>
              <td>{{ $i->titulo }}</td>
              <td>{{ $i->data_hora_inicio?->format('d/m/Y H:i') }}</td>
              <td>{{ $i->data_hora_fim?->format('d/m/Y H:i') }}</td>
              <td>{{ $i->local?->nome ?? '—' }}</td>
              <td class="text-right">
                {{-- Se quiser editar via CRUD genérico: --}}
                <a class="btn btn-xs btn-default" href="{{ route('eventos_detalhes.edit', $i) }}">Editar</a>
                <form action="{{ route('eventos_detalhes.destroy', $i) }}" method="post" style="display:inline">
                  @csrf @method('DELETE')
                  <button class="btn btn-xs btn-danger" onclick="return confirm('Remover?')">Excluir</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-muted">Nenhuma atividade adicionada.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
