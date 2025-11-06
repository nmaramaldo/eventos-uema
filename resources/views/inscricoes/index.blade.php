@extends('layouts.new-event')
@section('title','Minhas inscrições')

@section('content')
<div class="container" style="padding:60px 0">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif

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
        @php $ev = $i->evento; @endphp
        <tr>
          <td>
            @if($ev)
              <a href="{{ route('front.eventos.show', $ev) }}">
                {{ $ev->nome }}
              </a>
            @else
              <em class="text-muted">Evento indisponível</em>
            @endif
          </td>

          <td>{{ $ev?->periodo_evento ?? '—' }}</td>
          <td>{{ $ev?->periodo_inscricao ?? '—' }}</td>

          <td class="text-right">
            @if($ev && method_exists($ev,'isEncerrado') && !$ev->isEncerrado())
              <form method="post"
                    action="{{ route('inscricoes.cancelar', $i) }}"
                    onsubmit="return confirm('Cancelar esta inscrição?')"
                    style="display:inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-xs btn-danger">Cancelar</button>
              </form>
            @else
              <span class="label label-default">Encerrado</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="4" class="text-muted">Você ainda não possui inscrições.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{ $inscricoes->links() }}
</div>
@endsection
