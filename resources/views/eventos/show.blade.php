@extends('layouts.new-event')
@section('title', $evento->nome)

@section('content')
@php
  $status = strtolower((string)($evento->status ?? ''));
  $statusClass = match ($status) {
    'ativo'      => 'label-success',
    'publicado'  => 'label-primary',
    'rascunho'   => 'label-default',
    default      => 'label-default',
  };
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
          <p style="margin-bottom:0">
            <strong>Tipo:</strong> {{ $evento->tipo_evento ?? '-' }}
          </p>
        </div>
      </div>

      @if($evento->descricao)
        <div class="panel panel-default">
          <div class="panel-heading"><strong>Descrição</strong></div>
          <div class="panel-body">{!! nl2br(e($evento->descricao)) !!}</div>
        </div>
      @endif

      {{-- Programação / Detalhes (placeholder; liste $evento->detalhes se desejar) --}}
      {{-- 
      <div class="panel panel-default">
        <div class="panel-heading"><strong>Programação</strong></div>
        <div class="panel-body">
          <p class="text-muted">Em breve…</p>
        </div>
      </div>
      --}}

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
              // Como o controller carrega $evento->inscricoes, dá pra verificar localmente
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
