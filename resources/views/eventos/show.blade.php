@extends('layouts.new-event')
@section('title', $evento->nome)

@section('content')
@php
  // Badge do status
  $status = strtolower((string)($evento->status ?? ''));
  $statusClass = match ($status) {
    'ativo'      => 'label-success',
    'publicado'  => 'label-primary',
    'rascunho'   => 'label-default',
    default      => 'label-default',
  };

  // Rótulo amigável para o tipo (slug -> texto)
  $tipoEventoMap = [
    'presencial' => 'Presencial',
    'online'     => 'Online',
    'hibrido'    => 'Híbrido',
    'videoconf'  => 'Videoconferência',
  ];
  $tipoEventoLabel = $tipoEventoMap[$evento->tipo_evento] ?? ($evento->tipo_evento ?: '—');

  // Novos campos
  $classificacao = $evento->tipo_classificacao ?: null;
  $areaTematica  = $evento->area_tematica ?: null;

  // Vagas (se sua tabela tiver a coluna 'vagas')
  $vagasDisp = method_exists($evento, 'vagasDisponiveis') ? $evento->vagasDisponiveis() : null;
@endphp

<div class="container" style="padding:60px 0; max-width:1000px">
  <div class="row">
    <div class="col-sm-9">
      <h2 style="margin-bottom:5px">{{ $evento->nome }}</h2>

      <p class="text-muted" style="margin-bottom:10px">
        <span class="label {{ $statusClass }}" title="Status do evento">
          {{ $evento->status ? ucfirst($evento->status) : '—' }}
        </span>

        @if($evento->inscricoesAbertas())
          <span class="label label-success" style="margin-left:6px">Inscrições abertas</span>
        @else
          <span class="label label-default" style="margin-left:6px">Inscrições fechadas</span>
        @endif

        @if(!is_null($vagasDisp))
          <span class="label label-info" style="margin-left:6px">Vagas: {{ $vagasDisp }}</span>
        @endif
      </p>

      <div class="panel panel-default">
        <div class="panel-heading"><strong>Informações gerais</strong></div>
        <div class="panel-body">
          <p style="margin-bottom:8px">
            <strong>Período do evento:</strong><br>
            {{ $evento->periodo_evento }}
          </p>

          <p style="margin-bottom:8px">
            <strong>Período de inscrições:</strong><br>
            {{ $evento->periodo_inscricao }}
          </p>

          <p style="margin-bottom:8px">
            <strong>Tipo de realização:</strong>
            {{ $tipoEventoLabel }}
          </p>

          @if($classificacao)
            <p style="margin-bottom:8px">
              <strong>Classificação do evento:</strong>
              {{ $classificacao }}
            </p>
          @endif

          @if($areaTematica)
            <p style="margin-bottom:0">
              <strong>Área temática:</strong>
              {{ $areaTematica }}
            </p>
          @endif
        </div>
      </div>

      @if($evento->descricao)
        <div class="panel panel-default">
          <div class="panel-heading"><strong>Descrição</strong></div>
          <div class="panel-body">{!! nl2br(e($evento->descricao)) !!}</div>
        </div>
      @endif

      <div class="panel panel-default">
        <div class="panel-heading"><strong>Programação</strong></div>
        <div class="panel-body">
          @if($evento->programacoes->count() > 0)
            @foreach($evento->programacoes as $programacao)
              <div class="panel panel-default">
                <div class="panel-heading">{{ $programacao->titulo }}</div>
                <div class="panel-body">
                  <p><strong>Período:</strong> {{ $programacao->periodo }}</p>
                  <p><strong>Descrição:</strong> {{ $programacao->descricao }}</p>
                  @if($programacao->requer_inscricao)
                    <h4>Participantes</h4>
                    @if($programacao->users->count() > 0)
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Nome</th>
                            <th>Presença</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($programacao->users as $user)
                            <tr>
                              <td>{{ $user->name }}</td>
                              <td>
                                @if($user->pivot->presente)
                                  <form action="{{ route('programacao.removerPresenca', ['programacao' => $programacao]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit" class="btn btn-xs btn-success">Presente</button>
                                  </form>
                                @else
                                  <form action="{{ route('programacao.registrarPresenca', ['programacao' => $programacao]) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button type="submit" class="btn btn-xs btn-default">Marcar Presença</button>
                                  </form>
                                @endif
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    @else
                      <p>Nenhum participante inscrito nesta atividade.</p>
                    @endif

                    @can('manage-users')
                      <hr>
                      <h5>Inscrever novo participante</h5>
                      <form action="{{ route('programacao.inscrever', ['programacao' => $programacao]) }}" method="POST" class="form-inline">
                        @csrf
                        <div class="form-group">
                          <label for="user_id">Usuário</label>
                          <select name="user_id" id="user_id" class="form-control">
                            @foreach(App\Models\User::all() as $user)
                              <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                          </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Inscrever</button>
                      </form>
                    @endcan
                  @endif
                </div>
              </div>
            @endforeach
          @else
            <p class="text-muted">Nenhuma programação cadastrada para este evento.</p>
          @endif
        </div>
      </div>

      <div style="margin-top:20px; display:flex; gap:8px; flex-wrap:wrap">
        <a href="{{ route('eventos.index') }}" class="btn btn-default">Voltar</a>
        @can('manage-users')
          <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-primary">Editar</a>
        @endcan
      </div>
    </div>

    <div class="col-sm-3">
      @if(!empty($evento->logomarca_url))
        <img src="{{ $evento->logomarca_url }}" alt="Logo do evento" class="img-responsive img-thumbnail" style="margin-bottom:10px">
      @endif

      @if($evento->coordenador)
        <div class="panel panel-default">
          <div class="panel-heading"><strong>Coordenador</strong></div>
          <div class="panel-body">
            <div>{{ $evento->coordenador->name }}</div>
            <div class="text-muted" style="font-size:12px">{{ $evento->coordenador->email }}</div>
          </div>
        </div>
      @endif

      {{-- Participação / Inscrição --}}
      <div class="panel panel-default">
        <div class="panel-heading"><strong>Participação</strong></div>
        <div class="panel-body">
          @auth
            @php
              $jaInscrito = $evento->inscricoes->contains('user_id', auth()->id());
            @endphp

            @if($jaInscrito)
              <div class="alert alert-success" style="margin:0">Você já está inscrito.</div>
            @elseif($evento->inscricoesAbertas())
              <form method="post" action="{{ route('inscricoes.store') }}" style="margin:0">
                @csrf
                <input type="hidden" name="evento_id" value="{{ $evento->id }}">
                <button class="btn btn-primary btn-block">Inscrever-se</button>
              </form>
            @else
              <div class="alert alert-info" style="margin:0">As inscrições não estão abertas.</div>
            @endif
          @else
            <a href="{{ route('login') }}" class="btn btn-default btn-block">Entre para se inscrever</a>
          @endauth
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
