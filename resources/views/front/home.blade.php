@extends('layouts.app')
@section('title', 'Início')

@section('content')
  <style>
    /* cola o conteúdo na navbar (o layout tem <main class="py-4">) */
    main.py-4 { padding-top: 0 !important; padding-bottom: 0 !important; }

    /* remove qualquer margem/linha que vira “faixa” */
    .navbar { box-shadow: none !important; border-bottom: 0 !important; margin-bottom: 0 !important; }
    main > section.hero-academico:first-child { margin-top: 0 !important; }

    /* usa a MESMA cor do navbar (bg-primary) */
    .hero-academico {
      background: var(--bs-primary) !important;
      color: #fff;
      padding: 28px 0 36px; /* compacto pra “grudar” */
      text-align: center;
    }
    .hero-academico h1 {
      font-weight: 700;
      letter-spacing: .2px;
      margin-bottom: .5rem;
    }
    .hero-academico .lead {
      opacity: .95;
      margin-bottom: 1.1rem;
    }

    .card-evento {
      transition: transform .12s ease, box-shadow .12s ease;
      border: 0;
      box-shadow: 0 4px 14px rgba(16, 24, 40, .06);
      height: 100%;
    }
    .card-evento:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(16, 24, 40, .10);
    }
    .card-evento .thumb-wrap {
      position: relative;
      width: 100%;
      aspect-ratio: 16 / 9;
      background: #f3f4f6;
      overflow: hidden;
      border-top-left-radius: .5rem;
      border-top-right-radius: .5rem;
    }
    .card-evento img.thumb {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }
    .thumb-fallback {
      width: 100%;
      height: 100%;
      display: grid;
      place-items: center;
      font-weight: 600;
      color: #6b7280;
      background: repeating-linear-gradient(
        45deg, #f9fafb, #f9fafb 10px, #f3f4f6 10px, #f3f4f6 20px
      );
    }
    .badge-status { font-weight: 600; letter-spacing: .2px; }
  </style>

  {{-- HERO acadêmico (mesma cor do navbar) --}}
  <section class="hero-academico">
    <div class="container">
      <h1>Encontre eventos, jornadas e simpósios acadêmicos.</h1>
      <p class="lead">Explore a agenda científica da universidade e participe das próximas atividades.</p>
      <a href="{{ route('front.eventos.index') }}" class="btn btn-light btn-lg">
        <i class="bi bi-search me-2"></i>Explorar eventos
      </a>
    </div>
  </section>

  {{-- DESTAQUES --}}
  @if($destaques->count())
  <section class="container py-4">
    <h2 class="h4 mb-3">Em destaque</h2>
    <div id="carouselDestaques" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        @foreach($destaques as $i => $e)
          <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
            <div class="card border-0 shadow-sm">
              @if($e->logomarca_path)
                <img src="{{ Storage::url($e->logomarca_path) }}" class="card-img-top" alt="{{ $e->nome }}" style="object-fit:cover; max-height:380px;">
              @endif
              <div class="card-body">
                <h5 class="card-title mb-1">{{ $e->nome }}</h5>
                <div class="text-muted small mb-2">{{ $e->periodo_evento }}</div>
                <a href="{{ route('front.eventos.show', $e) }}" class="btn btn-primary btn-sm">Ver detalhes</a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselDestaques" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselDestaques" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
      </button>
    </div>
  </section>
  @endif

  {{-- ÁREAS (com ícone específico por área) --}}
  <section class="container pb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="h4 mb-0">Eventos por área</h2>
      <a href="{{ route('front.eventos.index') }}" class="small text-decoration-none">Ver mais</a>
    </div>

    <div class="row g-3">
      @foreach($areas as $area)
        @php
          // pega o ícone do mapa; fallback para grid
          $icon = $areaIcons[$area] ?? 'bi-grid-3x3-gap';
        @endphp
        <div class="col-6 col-md-4 col-lg-3">
          <a href="{{ route('front.eventos.index', ['area_tematica' => $area]) }}" class="text-decoration-none">
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex align-items-center">
                <i class="bi {{ $icon }} me-2"></i>
                <span class="fw-semibold">{{ $area }}</span>
              </div>
            </div>
          </a>
        </div>
      @endforeach
    </div>
  </section>

  {{-- RECENTES --}}
  <section class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="h4 mb-0">Vistos recentemente</h2>
      <a href="{{ route('front.eventos.index') }}" class="small text-decoration-none">Ver mais</a>
    </div>

    <div class="row g-3">
      @forelse($recentes as $e)
        <div class="col-12 col-md-6 col-lg-4">
          <a href="{{ route('front.eventos.show', $e) }}" class="text-decoration-none">
            <div class="card card-evento">
              <div class="thumb-wrap">
                @if($e->logomarca_path)
                  <img class="thumb" src="{{ Storage::url($e->logomarca_path) }}" alt="Capa de {{ $e->nome }}">
                @else
                  <div class="thumb-fallback">{{ \Illuminate\Support\Str::limit($e->nome, 22) }}</div>
                @endif
              </div>
              <div class="card-body">
                <h5 class="card-title mb-1 text-dark">{{ $e->nome }}</h5>
                <div class="text-muted small mb-2">{{ $e->periodo_evento }}</div>
                @if(View::exists('front.partials.event-badge'))
                  @include('front.partials.event-badge', ['ev' => $e])
                @else
                  <span class="badge bg-primary badge-status">{{ $e->status ?? '—' }}</span>
                @endif
              </div>
            </div>
          </a>
        </div>
      @empty
        <p class="text-muted">Ainda não há eventos cadastrados.</p>
      @endforelse
    </div>
  </section>
@endsection
