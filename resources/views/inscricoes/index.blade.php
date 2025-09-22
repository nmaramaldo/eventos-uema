@extends('layouts.new-event')
@section('title','Minhas inscrições')

@section('content')
<div class="container" style="padding:60px 0">
  <h2>Minhas inscrições</h2>

  <table class="table table-striped" style="margin-top:20px">
    <thead>
      <tr>
        <th>Evento</th>
        <th>Período</th>
        <th>Inscrições</th>
        <th class="text-right">Ações</th>
      </tr>
    </thead>
    <tbody>
      @forelse($inscricoes as $i)
        <tr>
          <td>
            <a href="{{ route('eventos.show', $i->evento) }}">
              {{ $i->evento->nome }}
            </a>
          </td>
          <td>{{ $i->evento->periodo_evento }}</td>
          <td>{{ $i->evento->periodo_inscricao }}</td>
          <td class="text-right">
            <form method="post" action="{{ route('inscricoes.destroy', $i) }}"
                  onsubmit="return confirm('Cancelar esta inscrição?')" style="display:inline">
              @csrf @method('DELETE')
              <button class="btn btn-xs btn-danger">Cancelar</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="4" class="text-muted">Você ainda não possui inscrições.</td></tr>
      @endforelse
    </tbody>
  </table>

  {{ $inscricoes->links() }}
</div>
@endsection
