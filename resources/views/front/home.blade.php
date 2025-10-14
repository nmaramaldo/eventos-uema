@extends('layouts.app')
@section('title', 'Início')

@section('content')
  {{-- HERO --}}
  <section class="hero text-center">
    <div class="container">
      <h1 class="mb-3">Descubra, participe, conecte-se.</h1>
      <p class="lead mb-4">Encontre os eventos mais relevantes da sua área.</p>
      <a href="{{ route('front.eventos.index') }}" class="btn btn-light btn-lg">
        <i class="bi bi-search me-2"></i>Explorar eventos
      </a>
    </div>
  </section>

  {{-- DESTAQUES --}}
  @if($destaques->count())
  <section class="container py-5">
    <h2 class="h4 mb-3">Em destaque</h2>
    <div id="carouselDestaques" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        @foreach($destaques as $i => $e)
          <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
            <div class="card border-0 shadow-sm">
              @if($e->logomarca_path)
                <img src="{{ Storage::url($e->logomarca_path) }}" class="card-img-top" alt="{{ $e->nome }}">
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

  {{-- ÁREAS --}}
  <section class="container pb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="h4 mb-0">Eventos por área</h2>
      <a href="{{ route('front.eventos.index') }}" class="small text-decoration-none">Ver mais</a>
    </div>

    <div class="row g-3">
      @foreach($areas as $area)
        <div class="col-6 col-md-4 col-lg-3">
          <a href="{{ route('front.eventos.index', ['area_tematica' => $area]) }}" class="text-decoration-none">
            <div class="card h-100 shadow-sm">
              <div class="card-body d-flex align-items-center">
                <i class="bi bi-grid-3x3-gap me-2"></i>
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
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title mb-1">
                <a href="{{ route('front.eventos.show', $e) }}" class="text-decoration-none">{{ $e->nome }}</a>
              </h5>
              <div class="text-muted small mb-2">{{ $e->periodo_evento }}</div>
              <span class="badge bg-primary badge-status">{{ $e->status ?? '—' }}</span>
            </div>
          </div>
        </div>
      @empty
        <p class="text-muted">Ainda não há eventos cadastrados.</p>
      @endforelse
    </div>
  </section>
@endsection
