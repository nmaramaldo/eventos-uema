{{-- resources/views/front/eventos-index.blade.php --}}
@extends('layouts.new-event')
@section('title', 'Eventos')

@section('content')
<section class="container" style="padding:40px 0; max-width:1140px">
  <div class="row">
    <div class="col-sm-8">
      <h2 style="margin:0 0 10px">Encontre eventos</h2>
      <p class="text-muted" style="margin:0 0 20px">Pesquise e filtre por tipo, categoria e área temática.</p>
    </div>
    <div class="col-sm-4 text-right" style="padding-top:10px">
      <a href="{{ route('front.home') }}" class="btn btn-link">Voltar para o início</a>
    </div>
  </div>

  {{-- Filtros --}}
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

      <div class="col-sm-12 text-right" style="margin-top:10px">
        <a href="{{ route('front.eventos.index') }}" class="btn btn-default">Limpar</a>
        <button class="btn btn-primary">Aplicar filtros</button>
      </div>
    </div>
  </form>

  {{-- Cards --}}
  <div class="row">
    @forelse ($eventos as $e)
      <div class="col-sm-6 col-md-4">
        <div class="panel panel-default" style="height:100%">
          @if($e->logomarca_url)
            <div class="panel-heading" style="padding:0; border-bottom:none">
              <img src="{{ $e->logomarca_url }}" alt="Logo" class="img-responsive" style="width:100%; max-height:160px; object-fit:cover">
            </div>
          @endif
          <div class="panel-body">
            <h4 style="margin-top:0; min-height:48px">
              <a href="{{ route('front.eventos.show', $e) }}">{{ $e->nome }}</a>
            </h4>
            <p class="text-muted" style="min-height:36px">
              <small>{{ $e->periodo_evento }}</small>
            </p>
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
          <div class="panel-footer text-right">
            <a class="btn btn-primary btn-sm" href="{{ route('front.eventos.show', $e) }}">Ver detalhes</a>
          </div>
        </div>
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
