@extends('layouts.new-event')
@section('title', $evento->nome)

@section('content')
@php
    use Illuminate\Support\Str;

    $status    = strtolower((string) ($evento->status ?? ''));
    $isAtivo   = in_array($status, ['ativo','publicado'], true);
    $inscritos = $evento->inscricoes->count();
    $temVagas  = !is_null($evento->vagas);
    $restantes = $temVagas ? max(0, (int)$evento->vagas - $inscritos) : null;

    $jaInscrito = auth()->check()
        ? $evento->inscricoes->contains('user_id', auth()->id())
        : false;

    $muted = '#6b7280';
@endphp

<style>
  .event-hero { background: linear-gradient(90deg, #0b2e7a 0%, #1e40af 50%, #0b2e7a 100%); color:#fff; padding: 28px 0 22px; }
  .event-hero .thumb { width:100%; max-width:280px; aspect-ratio:16/9; background:#0b2e7a; border-radius:10px; overflow:hidden; border:2px solid rgba(255,255,255,.15); }
  .event-hero .thumb img { width:100%; height:100%; object-fit:cover; }
  .pill { display:inline-block; padding:6px 10px; border-radius:999px; font-size:12px; background:rgba(255,255,255,.14); border:1px solid rgba(255,255,255,.25); color:#fff; margin-right:6px; margin-bottom:6px; }
  .panel-ish { background:#fff; border:1px solid #eaeaea; border-radius:12px; padding:18px; box-shadow:0 1px 0 rgba(0,0,0,.02); }
  .meta-item { color: {{ $muted }}; font-size:13px; }
  .label { display:inline-block; padding:3px 8px; border-radius:999px; font-size:11px; }
  .label-success { background:#16a34a; color:#fff; }
  .label-primary { background:#2563eb; color:#fff; }
  .label-default { background:#e5e7eb; color:#111; }
  .related-card { border:1px solid #eee; border-radius:10px; overflow:hidden; background:#fff; transition:.15s transform ease; display:block; color:inherit; text-decoration:none; }
  .related-card:hover { transform: translateY(-2px); }
  .related-thumb { height:120px; background:#f5f7fb; display:flex; align-items:center; justify-content:center; }
  .related-thumb img { max-height:100%; max-width:100%; object-fit:cover; }
  .related-body { padding:12px 12px 14px; }
</style>

<section class="event-hero">
  <div class="container" style="max-width:1100px">
    <div class="row" style="align-items:center;">
      <div class="col-sm-4">
        <div class="thumb">
          @if(!empty($evento->logomarca_url))
            <img src="{{ $evento->logomarca_url }}" alt="Imagem do evento">
          @else
            <img src="https://dummyimage.com/800x450/163c9b/ffffff&text=UEMA+Eventos" alt="Evento">
          @endif
        </div>
      </div>
      <div class="col-sm-8">
        <h1 style="margin:0 0 6px; line-height:1.25">{{ $evento->nome }}</h1>

        <div class="meta-item" style="margin-bottom:12px">
          <span class="pill" title="Período do evento"><i class="fa fa-calendar"></i><span style="margin-left:6px">{{ $evento->periodo_evento }}</span></span>
          <span class="pill" title="Tipo do evento"><i class="fa fa-map-marker"></i><span style="margin-left:6px">{{ ucfirst($evento->tipo_evento ?? '-') }}</span></span>
          @if($evento->tipo_classificacao)<span class="pill" title="Categoria">{{ $evento->tipo_classificacao }}</span>@endif
          @if($evento->area_tematica)<span class="pill" title="Área temática">{{ $evento->area_tematica }}</span>@endif
        </div>

        {{-- CTA de inscrição + capacidade --}}
        <div>
          @if($jaInscrito)
            <span class="label label-success">Você já está inscrito</span>
          @elseif($evento->inscricoesAbertas())
            @if($temVagas && $restantes <= 0)
              <span class="label label-default">Vagas esgotadas</span>
              <div class="meta-item" style="margin-top:6px">Inscrições: <strong>{{ $evento->periodo_inscricao }}</strong></div>
            @else
              @auth
                <form method="post" action="{{ route('inscricoes.store') }}" style="display:inline">
                  @csrf
                  <input type="hidden" name="evento_id" value="{{ $evento->id }}">
                  <button class="btn btn-success" style="padding:10px 16px;">Realizar inscrição</button>
                </form>
                <div class="meta-item" style="margin-top:6px">Inscrições: <strong>{{ $evento->periodo_inscricao }}</strong></div>
              @else
                <a href="{{ route('login') }}" class="btn btn-default" style="padding:10px 16px; background:#fff; color:#111; border:0">Entrar para se inscrever</a>
                <div class="meta-item" style="margin-top:6px">Inscrições: <strong>{{ $evento->periodo_inscricao }}</strong></div>
              @endauth
            @endif
          @else
            <span class="label label-default">Inscrições fechadas</span>
            <div class="meta-item" style="margin-top:6px">Janela: <strong>{{ $evento->periodo_inscricao }}</strong></div>
          @endif

          @if($temVagas)
            <div class="meta-item" style="margin-top:6px">
              Capacidade: <strong>{{ $inscritos }}</strong> / <strong>{{ $evento->vagas }}</strong>
              — Restantes: <strong>{{ $restantes }}</strong>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

<section style="padding:28px 0 48px; background:#f7f8fb;">
  <div class="container" style="max-width:1100px">
    <div class="row">
      <div class="col-sm-8">
        {{-- SOBRE --}}
        <div class="panel-ish" style="margin-bottom:16px;">
          <h3 style="margin-top:0">Sobre o evento</h3>
          @if($evento->descricao)
            <div>{!! nl2br(e($evento->descricao)) !!}</div>
          @else
            <p class="meta-item" style="margin:0">A descrição deste evento será publicada em breve.</p>
          @endif
        </div>

        {{-- PROGRAMAÇÃO (usa accessor $d->periodo + $d->descricao e exibe localidade) --}}
        <div class="panel-ish" style="margin-bottom:16px;">
          <h3 style="margin-top:0">Programação</h3>
          @if($evento->detalhes->count())
            <ul style="padding-left:18px; margin:0">
              @foreach($evento->detalhes as $d)
                <li style="margin-bottom:6px">
                  <strong>{{ $d->periodo }}</strong>
                  — {{ $d->descricao ?? 'Atividade' }}
                  @if($d->localidade)
                    <span class="meta-item"> • {{ $d->localidade }}</span>
                  @endif
                </li>
              @endforeach
            </ul>
          @else
            <p class="meta-item" style="margin:0">A programação será divulgada em breve.</p>
          @endif
        </div>

        {{-- PALESTRANTES --}}
        <div class="panel-ish" style="margin-bottom:16px;">
          <h3 style="margin-top:0">Palestrantes</h3>
          @if($evento->palestrantes->count())
            <div class="row">
              @foreach($evento->palestrantes as $p)
                <div class="col-sm-6" style="margin-bottom:10px">
                  <div style="display:flex; gap:10px; align-items:center;">
                    <div style="width:44px; height:44px; background:#e5e7eb; border-radius:999px; display:flex; align-items:center; justify-content:center;">
                      <i class="fa fa-user"></i>
                    </div>
                    <div>
                      <div><strong>{{ $p->nome ?? $p->name ?? 'Palestrante' }}</strong></div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <p class="meta-item" style="margin:0">Os palestrantes serão anunciados em breve.</p>
          @endif
        </div>
      </div>

      <div class="col-sm-4">
        {{-- ORGANIZAÇÃO --}}
        @if($evento->coordenador)
        <div class="panel-ish" style="margin-bottom:16px;">
          <h4 style="margin-top:0">Organização</h4>
          <div style="display:flex; gap:10px; align-items:center;">
            <div style="width:40px; height:40px; background:#e5e7eb; border-radius:999px; display:flex; align-items:center; justify-content:center;">
              <i class="fa fa-user"></i>
            </div>
            <div>
              <div><strong>{{ $evento->coordenador->name }}</strong></div>
              <div class="meta-item">{{ $evento->coordenador->email }}</div>
            </div>
          </div>
        </div>
        @endif

        {{-- INFO RÁPIDA --}}
        <div class="panel-ish" style="margin-bottom:16px;">
          <h4 style="margin-top:0">Informações</h4>
          <div class="meta-item"><strong>Período:</strong> {{ $evento->periodo_evento }}</div>
          <div class="meta-item"><strong>Inscrições:</strong> {{ $evento->periodo_inscricao }}</div>
          <div class="meta-item"><strong>Tipo:</strong> {{ ucfirst($evento->tipo_evento ?? '-') }}</div>
          @if($evento->tipo_classificacao)
            <div class="meta-item"><strong>Categoria:</strong> {{ $evento->tipo_classificacao }}</div>
          @endif
          @if($evento->area_tematica)
            <div class="meta-item"><strong>Área:</strong> {{ $evento->area_tematica }}</div>
          @endif
          @php
            $cls = match($status) { 'ativo' => 'label-success', 'publicado' => 'label-primary', 'rascunho' => 'label-default', default => 'label-default' };
          @endphp
          <div class="meta-item"><strong>Status:</strong>
            <span class="label {{ $cls }}" style="vertical-align:middle; margin-left:4px;">
              {{ $evento->status ? ucfirst($evento->status) : '—' }}
            </span>
          </div>

          @can('manage-users')
            @if($temVagas)
              <div class="meta-item" style="margin-top:8px">
                <strong>Capacidade (admin):</strong> {{ $inscritos }} / {{ $evento->vagas }} — Restantes: {{ $restantes }}
              </div>
            @endif
          @endcan
        </div>
      </div>
    </div>

    {{-- RELACIONADOS --}}
    @if($relacionados->count())
    <div style="margin-top:8px">
      <h3 style="margin:18px 0">Você também pode se interessar</h3>
      <div class="row">
        @foreach($relacionados as $e)
          <div class="col-sm-4" style="margin-bottom:14px">
            <a class="related-card" href="{{ route('front.eventos.show', $e) }}">
              <div class="related-thumb">
                @if(!empty($e->logomarca_url))
                  <img src="{{ $e->logomarca_url }}" alt="Logo">
                @else
                  <span style="color:#94a3b8; font-size:12px;">sem imagem</span>
                @endif
              </div>
              <div class="related-body">
                <div style="font-weight:600; line-height:1.25; margin-bottom:4px">{{ Str::limit($e->nome, 80) }}</div>
                <div class="meta-item"><i class="fa fa-calendar"></i> <span style="margin-left:6px">{{ $e->data_inicio_evento?->format('d/m/Y H:i') }}</span></div>
                @if($e->area_tematica)
                  <div class="meta-item">{{ $e->area_tematica }}</div>
                @endif
              </div>
            </a>
          </div>
        @endforeach
      </div>
    </div>
    @endif
  </div>
</section>
@endsection
