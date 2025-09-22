@extends('layouts.new-event')
@section('title', 'Início')

@section('content')
  {{-- HERO / INTRO --}}
  <section id="intro" class="text-center" style="padding:120px 0; background:#222; color:#fff;">
    <div class="container">
      <h1 style="margin-bottom:10px;">Bem-vindo(a) ao Portal de Eventos</h1>
      <p>Descubra eventos, confira a programação, conheça os palestrantes e faça sua inscrição.</p>
      <a href="{{ route('front.eventos.index') }}" class="btn btn-primary" style="margin-top:20px;">Explorar eventos</a>
    </div>
  </section>

  {{-- PALESTRANTES (placeholder; depois ligamos ao banco) --}}
  <section id="palestrantes" class="container" style="padding:60px 0;">
    <header class="text-center">
      <h2 class="section-title">Palestrantes</h2>
      <p>Conheça alguns dos profissionais confirmados.</p>
    </header>
    <div class="row">
      <div class="col-md-3"><div class="speaker-card text-center"><i class="fa fa-user fa-4x"></i><h4>Nome</h4></div></div>
      <div class="col-md-3"><div class="speaker-card text-center"><i class="fa fa-user fa-4x"></i><h4>Nome</h4></div></div>
      <div class="col-md-3"><div class="speaker-card text-center"><i class="fa fa-user fa-4x"></i><h4>Nome</h4></div></div>
      <div class="col-md-3"><div class="speaker-card text-center"><i class="fa fa-user fa-4x"></i><h4>Nome</h4></div></div>
    </div>
  </section>

  {{-- PROGRAMAÇÃO (placeholder) --}}
  <section id="programacao" class="container" style="padding:60px 0;">
    <header class="text-center">
      <h2 class="section-title">Programação</h2>
      <p>Agenda de atividades por dia e horário.</p>
    </header>
    <div class="program-item"><strong>09:00–10:00</strong> — Abertura oficial</div>
    <div class="program-item"><strong>10:15–11:30</strong> — Mesa redonda</div>
  </section>

  {{-- INSCRIÇÃO (placeholder) --}}
  <section id="inscricao" class="container text-center" style="padding:60px 0;">
    <h2 class="section-title">Inscrição</h2>
    <p>Faça sua inscrição nos eventos disponíveis.</p>
    <a href="{{ route('front.eventos.index') }}" class="btn btn-success">Ver eventos disponíveis</a>
  </section>
@endsection
