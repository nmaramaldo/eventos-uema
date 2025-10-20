{{-- resources/views/front/eventos-index.blade.php --}}
@extends('layouts.app')
@section('title', 'Eventos')

@section('content')
<section class="container" style="padding:40px 0; max-width:1140px">
  <style>
    /* mesmo visual dos cards da Home */
    .card-evento{border:0;box-shadow:0 4px 14px rgba(16,24,40,.06);height:100%;transition:transform .12s ease,box-shadow .12s ease}
    .card-evento:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(16,24,40,.10)}
    .card-evento .thumb-wrap{position:relative;width:100%;aspect-ratio:16/9;background:#f3f4f6;overflow:hidden;border-top-left-radius:.5rem;border-top-right-radius:.5rem}
    .card-evento img.thumb{width:100%;height:100%;object-fit:cover;display:block}
    .thumb-fallback{width:100%;height:100%;display:grid;place-items:center;font-weight:600;color:#6b7280;
      background:repeating-linear-gradient(45deg,#f9fafb,#f9fafb 10px,#f3f4f6 10px,#f3f4f6 20px)}
    .badge-status{font-weight:600;letter-spacing:.2px}
  </style>

  <div class="row">
    <div class="col-sm-8">
      <h2 style="margin:0 0 10px">Encontre eventos</h2>
      <p class="text-muted" style="margin:0 0 20px">Pesquise e filtre por tipo, categoria e área temática.</p>
    </div>
    <div class="col-sm-4 text-end" style="padding-top:10px">
      <a href="{{ route('front.home') }}" class="btn btn-link">Voltar para o início</a>
    </div>
  </div>

  {{-- Filtros (mantidos) --}}
  <form method="get" class="panel panel-default" style="padding:15px; margin-bottom:20px">
    <div class="row" style="gap:10px 0">
      <div class="col-sm-4">
        <label class="control-label">Buscar</label>
        <input type="search" name="s" value="{{ request('s') }}" class="form-control" placeholder="Nome ou descrição do evento">
      </div>

      <div class="col-sm-3">
        <label class="control-label">Tipo do evento</label>
        <select name="tipo_evento" class="form-control">
          @php
            $tipoAtual = request('tipo_evento');
            $tipos = ['' => '-- Qualquer --', 'presencial' => 'Presencial', 'online' => 'Online', 'hibrido' => 'Híbrido', 'videoconf' => 'Videoconf.'];
          @endphp
          @foreach($tipos as $v => $rotulo)
            <option value="{{ $v }}" @selected($tipoAtual===$v)>{{ $rotulo }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-sm-3">
        <label class="control-label">Categoria</label>
        @php
          $categorias = [
            '' => '-- Qualquer --',
            'Acadêmico - Seminário/Jornada' => 'Acadêmico - Seminário/Jornada',
            'Científico - Congresso/Simpósio' => 'Científico - Congresso/Simpósio',
            'Corporativo - Empresarial' => 'Corporativo - Empresarial',
            'Curso - Workshop/Palestra' => 'Curso - Workshop/Palestra',
            'Entretenimento - Show/Festa' => 'Entretenimento - Show/Festa',
            'Religioso - Retiro/Encontro/Beneficente' => 'Religioso - Retiro/Encontro/Beneficente',
            'Esportivo - Jogos/Torneios' => 'Esportivo - Jogos/Torneios',
            'Exibição - Feira/Exposição' => 'Exibição - Feira/Exposição',
            'Networking - Encontro/Meetup' => 'Networking - Encontro/Meetup',
            'Outro' => 'Outro',
          ];
        @endphp
        <select name="tipo_classificacao" class="form-control">
          @foreach($categorias as $v => $r)
            <option value="{{ $v }}" @selected(request('tipo_classificacao')===$v)>{{ $r }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-sm-2">
        <label class="control-label">Área temática</label>
        @php
          $areas = [
            '' => '-- Qualquer --',
            'Agricultura, pesca e veterinária' => 'Agricultura, pesca e veterinária',
            'Artes e humanidades' => 'Artes e humanidades',
            'Ciências sociais e jornalismo' => 'Ciências sociais e jornalismo',
            'Computação e Tecnologias da Informação' => 'Computação e Tecnologias da Informação',
            'Direito' => 'Direito',
            'Educação' => 'Educação',
            'Empreendedorismo e inovação' => 'Empreendedorismo e inovação',
            'Engenharias' => 'Engenharias',
            'Gastronomia' => 'Gastronomia',
            'Medicina' => 'Medicina',
            'Negócios e administração' => 'Negócios e administração',
            'Saúde e bem-estar' => 'Saúde e bem-estar',
            'Desenvolvimento pessoal' => 'Desenvolvimento pessoal',
            'Religioso e Espiritualidade' => 'Religioso e Espiritualidade',
            'Outros' => 'Outros',
          ];
        @endphp
        <select name="area_tematica" class="form-control">
          @foreach($areas as $v => $r)
            <option value="{{ $v }}" @selected(request('area_tematica')===$v)>{{ $r }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-sm-12 text-end" style="margin-top:10px">
        <a href="{{ route('front.eventos.index') }}" class="btn btn-default">Limpar</a>
        <button class="btn btn-primary">Aplicar filtros</button>
      </div>
    </div>
  </form>

  {{-- Cards no mesmo padrão do Home --}}
  <div class="row">
    @forelse ($eventos as $e)
      <div class="col-sm-6 col-md-4">
        <a href="{{ route('front.eventos.show', $e) }}" class="text-decoration-none">
          <div class="card card-evento">
            <div class="thumb-wrap">
              @php
                // suporta tanto logomarca_path (Storage) quanto logomarca_url
                $thumb = null;
                if (!empty($e->logomarca_path)) {
                  $thumb = Storage::url($e->logomarca_path);
                } elseif (!empty($e->logomarca_url)) {
                  $thumb = $e->logomarca_url;
                }
              @endphp
              @if($thumb)
                <img class="thumb" src="{{ $thumb }}" alt="Capa de {{ $e->nome }}">
              @else
                <div class="thumb-fallback">{{ \Illuminate\Support\Str::limit($e->nome, 22) }}</div>
              @endif
            </div>

            <div class="card-body">
              <h4 class="card-title mb-1 text-dark" style="margin-top:0">{{ $e->nome }}</h4>
              <p class="text-muted small mb-2" style="min-height:18px">{{ $e->periodo_evento }}</p>

              {{-- badge de status igual ao Home --}}
              <div class="mb-2">
                @if(View::exists('front.partials.event-badge'))
                  @include('front.partials.event-badge', ['ev' => $e])
                @else
                  <span class="badge bg-primary badge-status">{{ $e->status ?? '—' }}</span>
                @endif
              </div>

              {{-- chips informativos (mantidos) --}}
              <div style="display:flex; gap:6px; flex-wrap:wrap">
                @if($e->tipo_evento)
                  <span class="label label-info">{{ ucfirst($e->tipo_evento) }}</span>
                @endif
                @if($e->tipo_classificacao)
                  <span class="label label-default">{{ $e->tipo_classificacao }}</span>
                @endif
                @if($e->area_tematica)
                  <span class="label label-primary">{{ $e->area_tematica }}</span>
                @endif
              </div>
            </div>
          </div>
        </a>
      </div>
    @empty
      <div class="col-sm-12">
        <div class="alert alert-info">Nenhum evento encontrado com os filtros atuais.</div>
      </div>
    @endforelse
  </div>

  <div class="text-center" style="margin-top:10px">
    {{ $eventos->links() }}
  </div>
</section>
@endsection
