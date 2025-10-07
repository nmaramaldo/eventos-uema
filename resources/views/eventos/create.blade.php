{{-- resources/views/eventos/create.blade.php --}}
@extends('layouts.new-event')

@section('title', 'Criar Evento - Eventos UEMA')

@section('content')
<style>
  .cardish{background:#fff;border:1px solid #eaeaea;border-radius:12px;padding:18px;box-shadow:0 1px 0 rgba(0,0,0,.02);margin-bottom:16px}
  .muted{color:#6b7280;font-size:12px}
  .mini-table th,.mini-table td{padding:6px 8px;font-size:13px}
  #map {height: 280px; border-radius: 10px; border:1px solid #e5e7eb;}
</style>

<div class="container" style="max-width:1100px;padding:26px 0 40px">
  <form id="eventoForm" action="{{ route('eventos.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off" novalidate>
    @csrf

    {{-- ===================== Informações básicas ===================== --}}
    <div class="cardish">
      <h3 style="margin-top:0">Informações básicas</h3>

      <div class="form-group">
        <label>Título do evento *</label>
        <input type="text" name="nome" class="form-control" value="{{ old('nome') }}" required>
        @error('nome') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <label>Categoria *</label>
            <select name="tipo_classificacao" class="form-control" required>
              <option value="">Selecione uma categoria</option>
              @foreach (['Tecnologia','Acadêmico','Cultural','Esportivo','Científico','Social','Competição','Workshop'] as $cat)
                <option value="{{ $cat }}" @selected(old('tipo_classificacao')==$cat)>{{ $cat }}</option>
              @endforeach
            </select>
            @error('tipo_classificacao') <small class="text-danger">{{ $message }}</small> @enderror
          </div>
        </div>

        <div class="col-sm-6">
          <div class="form-group">
            <label>Imagem do evento</label>
            {{-- o controller espera "capa" e salva URL em logomarca_url --}}
            <input type="file" name="capa" accept="image/*" class="form-control">
            <small class="muted">Formatos: JPG, PNG, GIF (máx. 3MB)</small>
            @error('capa') <small class="text-danger">{{ $message }}</small> @enderror
          </div>
        </div>
      </div>

      <div class="form-group">
        <label>Descrição do evento</label>
        <textarea name="descricao" rows="5" class="form-control">{{ old('descricao') }}</textarea>
        @error('descricao') <small class="text-danger">{{ $message }}</small> @enderror
      </div>
    </div>

    {{-- ===================== Tipo de evento ===================== --}}
    <div class="cardish">
      <h3 style="margin-top:0">Tipo de evento</h3>
      <label class="radio-inline">
        <input type="radio" name="tipo_evento" value="online" @checked(old('tipo_evento')==='online') required> Online
      </label>
      <label class="radio-inline" style="margin-left:10px">
        <input type="radio" name="tipo_evento" value="presencial" @checked(old('tipo_evento')==='presencial')> Presencial
      </label>
      <label class="radio-inline" style="margin-left:10px">
        <input type="radio" name="tipo_evento" value="hibrido" @checked(old('tipo_evento')==='hibrido')> Híbrido
      </label>
      @error('tipo_evento') <div><small class="text-danger">{{ $message }}</small></div> @enderror
    </div>

    {{-- ===================== Data (com calendário + hora) ===================== --}}
    @php
      $oldIni = old('data_inicio_evento');
      $oldFim = old('data_fim_evento');
      $iniDate = $oldIni ? \Carbon\Carbon::parse($oldIni)->format('Y-m-d') : '';
      $iniTime = $oldIni ? \Carbon\Carbon::parse($oldIni)->format('H:i')   : '';
      $fimDate = $oldFim ? \Carbon\Carbon::parse($oldFim)->format('Y-m-d') : '';
      $fimTime = $oldFim ? \Carbon\Carbon::parse($oldFim)->format('H:i')   : '';
    @endphp

    <div class="cardish">
      <h3 style="margin-top:0">Data</h3>

      {{-- ocultos que o backend usa --}}
      <input type="hidden" name="data_inicio_evento" id="dtInicioFull">
      <input type="hidden" name="data_fim_evento"    id="dtFimFull">

      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>Data início do evento *</label>
            <input type="date" id="dtInicioDate" value="{{ $iniDate }}" min="{{ date('Y-m-d') }}" class="form-control" required>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Hora início *</label>
            <input type="time" id="dtInicioTime" value="{{ $iniTime }}" min="06:00" max="22:00" step="900" list="horarios" class="form-control" required>
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            <label>Data fim do evento *</label>
            <input type="date" id="dtFimDate" value="{{ $fimDate }}" min="{{ date('Y-m-d') }}" class="form-control" required>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Hora fim *</label>
            <input type="time" id="dtFimTime" value="{{ $fimTime }}" min="06:00" max="22:00" step="900" list="horarios" class="form-control" required>
          </div>
        </div>
      </div>
    </div>

    {{-- ===================== Localização (1 local + mapa com busca) ===================== --}}
    <div class="cardish">
      <h3 style="margin-top:0">Localização</h3>

      {{-- Campo legado ainda usado em páginas públicas (mantemos) --}}
      <div class="form-group">
        <label>Local do evento (texto livre)</label>
        <input type="text" name="area_tematica" value="{{ old('area_tematica') }}" class="form-control" placeholder="Ex.: Auditório Central - Campus São Luís">
        <small class="muted">Você pode preencher manualmente ou escolher abaixo no mapa para gravar o mesmo local em <em>Locais</em>.</small>
        @error('area_tematica') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      {{-- Locais para o persistProgramacaoDoRequest (apenas 1) --}}
      <input type="hidden" name="locais[0][nome]" id="localNomeHidden">

      <div class="form-group">
        <label>Pesquisar no mapa (OpenStreetMap)</label>
        <div id="map"></div>
        <small class="muted">Pesquise e clique no mapa. Salvaremos o local escolhido.</small>
      </div>
    </div>

    {{-- ===================== Palestrantes ===================== --}}
    <div class="cardish">
      <h3 style="margin-top:0">Palestrantes</h3>

      <div class="row" style="gap:10px">
        <div class="col-sm-3"><input id="palNome" class="form-control" placeholder="Nome"></div>
        <div class="col-sm-3"><input id="palEmail" class="form-control" placeholder="E-mail (opcional)"></div>
        <div class="col-sm-3"><input id="palCargo" class="form-control" placeholder="Cargo/Instituição (opcional)"></div>
        <div class="col-sm-2"><button type="button" class="btn btn-default" id="btnAddPal">+ Adicionar</button></div>
      </div>

      <div style="margin-top:10px">
        <table class="table table-bordered mini-table" id="tblPalestrantes">
          <thead><tr><th>Nome</th><th>E-mail</th><th>Cargo/Inst.</th><th style="width:70px">Ações</th></tr></thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    {{-- ===================== Programação ===================== --}}
    <div class="cardish">
      <h3 style="margin-top:0">Programação</h3>

      <div class="row" style="gap:10px">
        <div class="col-sm-3"><input id="atvTitulo" class="form-control" placeholder="Título"></div>
        <div class="col-sm-2">
          <select id="atvTipo" class="form-control">
            <option value="">Tipo</option>
            <option>Palestra</option>
            <option>Minicurso</option>
            <option>Mesa-redonda</option>
            <option>Conferência</option>
            <option>Apresentação de Trabalho</option>
            <option>Oficina</option>
            <option>Outro</option>
          </select>
        </div>
        <div class="col-sm-2"><input id="atvInicio" type="datetime-local" class="form-control"></div>
        <div class="col-sm-2"><input id="atvFim" type="datetime-local" class="form-control"></div>
        <div class="col-sm-2">
          <select id="atvLocal" class="form-control"><option value="">Local</option></select>
        </div>
        <div class="col-sm-1"><input id="atvCap" type="number" min="1" class="form-control" placeholder="Cap."></div>
        <div class="col-sm-12" style="margin-top:8px">
          <label class="checkbox-inline"><input type="checkbox" id="atvReqInscricao"> Requer inscrição</label>
          <button type="button" class="btn btn-default pull-right" id="btnAddAtv">+ Adicionar atividade</button>
        </div>
      </div>

      <div style="margin-top:10px">
        <table class="table table-bordered mini-table" id="tblAtividades">
          <thead>
            <tr>
              <th>Título</th><th>Tipo</th><th>Início</th><th>Fim</th><th>Local</th><th>Cap.</th><th>Inscr.</th><th style="width:70px">Ações</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

    {{-- ===================== Inscrições (com calendário + hora) ===================== --}}
    @php
      $oldInsIni = old('data_inicio_inscricao');
      $oldInsFim = old('data_fim_inscricao');
      $insIniDate = $oldInsIni ? \Carbon\Carbon::parse($oldInsIni)->format('Y-m-d') : '';
      $insIniTime = $oldInsIni ? \Carbon\Carbon::parse($oldInsIni)->format('H:i')   : '';
      $insFimDate = $oldInsFim ? \Carbon\Carbon::parse($oldInsFim)->format('Y-m-d') : '';
      $insFimTime = $oldInsFim ? \Carbon\Carbon::parse($oldInsFim)->format('H:i')   : '';
    @endphp

    <div class="cardish">
      <h3 style="margin-top:0">Inscrições</h3>

      {{-- ocultos que o backend usa --}}
      <input type="hidden" name="data_inicio_inscricao" id="insInicioFull">
      <input type="hidden" name="data_fim_inscricao"    id="insFimFull">

      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label>Data início inscrições</label>
            <input type="date" id="insInicioDate" value="{{ $insIniDate }}" min="{{ date('Y-m-d') }}" class="form-control">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Hora início</label>
            <input type="time" id="insInicioTime" value="{{ $insIniTime }}" min="06:00" max="22:00" step="900" list="horarios" class="form-control">
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            <label>Data fim inscrições</label>
            <input type="date" id="insFimDate" value="{{ $insFimDate }}" min="{{ date('Y-m-d') }}" class="form-control">
          </div>
        </div>
        <div class="col-sm-3">
          <div class="form-group">
            <label>Hora fim</label>
            <input type="time" id="insFimTime" value="{{ $insFimTime }}" min="06:00" max="22:00" step="900" list="horarios" class="form-control">
          </div>
        </div>

        <div class="col-sm-3">
          <div class="form-group">
            <label>Número de vagas</label>
            {{-- o request usa "vagas" --}}
            <input type="number" min="1" name="vagas" value="{{ old('vagas') }}" class="form-control">
            @error('vagas') <small class="text-danger">{{ $message }}</small> @enderror
          </div>
        </div>
      </div>

      <div class="form-group">
        <label>Status do evento</label><br>
        <label class="radio-inline">
          <input type="radio" name="status" value="ativo" @checked(old('status','ativo')==='ativo')> Ativo (visível)
        </label>
        <label class="radio-inline" style="margin-left:10px">
          <input type="radio" name="status" value="rascunho" @checked(old('status')==='rascunho')> Rascunho
        </label>
      </div>

      <div class="text-right">
        <a href="{{ route('eventos.index') }}" class="btn btn-default">Cancelar</a>
        <button type="submit" name="action" value="draft" class="btn btn-warning">Salvar Rascunho</button>
        <button type="submit" name="action" value="publish" class="btn btn-primary">Publicar Evento</button>
      </div>
    </div>

    {{-- sugestões de horários (06:00 → 22:00 a cada 30 min) --}}
    <datalist id="horarios">
      @php
        for ($m = 6*60; $m <= 22*60; $m += 30) {
            echo '<option value="'.sprintf('%02d:%02d', intdiv($m,60), $m%60).'"></option>';
        }
      @endphp
    </datalist>
  </form>
</div>

{{-- Leaflet (mapa sem chave) --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

{{-- Script que junta data + hora, e gerencia mapa/palestrantes/atividades --}}
<script>
(function () {
  const $ = (id) => document.getElementById(id);
  const form = document.getElementById('eventoForm');

  /* ======= Datas do Evento/Inscrição ======= */
  function joinDateTime(dateEl, timeEl, targetEl, defaultTime='08:00') {
    const d = dateEl?.value?.trim();
    const t = timeEl?.value?.trim() || '';
    if (!d) { targetEl.value = ''; return; }
    const hhmm = t !== '' ? t : defaultTime;
    targetEl.value = d + ' ' + hhmm + ':00';
  }
  function clampTime(timeEl) {
    if (!timeEl || !timeEl.value) return;
    const min = timeEl.min || '06:00';
    const max = timeEl.max || '22:00';
    if (timeEl.value < min) timeEl.value = min;
    if (timeEl.value > max) timeEl.value = max;
  }
  function updateAllDateTimes() {
    ['dtInicioTime','dtFimTime','insInicioTime','insFimTime'].forEach(id => clampTime($(id)));
    joinDateTime($('dtInicioDate'), $('dtInicioTime'), $('dtInicioFull'));
    joinDateTime($('dtFimDate'),    $('dtFimTime'),    $('dtFimFull'));
    joinDateTime($('insInicioDate'), $('insInicioTime'), $('insInicioFull'));
    joinDateTime($('insFimDate'),    $('insFimTime'),    $('insFimFull'));
  }
  ['dtInicioDate','dtInicioTime','dtFimDate','dtFimTime','insInicioDate','insInicioTime','insFimDate','insFimTime']
    .forEach(id => { const el = $(id); el && el.addEventListener('input', updateAllDateTimes); });
  document.addEventListener('DOMContentLoaded', updateAllDateTimes);
  form?.addEventListener('submit', function(){ updateAllDateTimes(); ensureHiddenCollections(); });

  /* ======= Mapa (Leaflet + Geocoder) ======= */
  let locais = []; // {key, nome}
  const localNomeHidden = $('localNomeHidden');
  const atvLocalSelect  = $('atvLocal');

  let map, marker;
  try {
    map = L.map('map').setView([-2.532, -44.296], 12); // São Luís/MA aprox
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

    const geocoder = L.Control.Geocoder.nominatim();
    const searchControl = L.Control.geocoder({
      defaultMarkGeocode: false,
      geocoder: geocoder,
      placeholder: 'Pesquise endereço, prédio, campus…'
    }).on('markgeocode', function(e) {
      const center = e.geocode.center;
      setPoint(center.lat, center.lng, e.geocode.name || 'Local do evento');
    }).addTo(map);

    map.on('click', function(e){
      setPoint(e.latlng.lat, e.latlng.lng, 'Local escolhido no mapa');
    });

    function setPoint(lat, lng, label) {
      if (marker) map.removeLayer(marker);
      marker = L.marker([lat, lng]).addTo(map);
      const nome = `${label} (${lat.toFixed(5)}, ${lng.toFixed(5)})`;
      // Mantemos 1 local: row0
      locais = [{ key: 'row0', nome }];
      localNomeHidden.value = nome;
      redrawLocaisOnSelect();
    }

  } catch(e) {
    // se Leaflet não carregar, seguimos só com o campo texto
    console.warn('Mapa indisponível:', e);
  }

  function redrawLocaisOnSelect(){
    if (!atvLocalSelect) return;
    atvLocalSelect.innerHTML = '<option value="">Local</option>';
    locais.forEach(l => {
      const opt = document.createElement('option');
      opt.value = l.key; opt.textContent = l.nome;
      atvLocalSelect.appendChild(opt);
    });
  }

  /* ======= Palestrantes ======= */
  const tblPales = document.querySelector('#tblPalestrantes tbody');
  const pal = [];
  function redrawPales(){
    if (!tblPales) return;
    tblPales.innerHTML = '';
    // limpar inputs ocultos anteriores
    document.querySelectorAll('input[name^="palestrantes["]').forEach(x=>x.remove());
    pal.forEach(function(p, i){
      const tr = document.createElement('tr');
      tr.innerHTML = '<td>'+p.nome+'</td><td>'+(p.email||'')+'</td><td>'+(p.cargo||'')+'</td>'+
                     '<td><button class="btn btn-xs btn-danger" data-i="'+i+'" type="button">rem</button></td>';
      tblPales.appendChild(tr);

      addHidden('palestrantes['+i+'][nome]',  p.nome);
      if (p.email) addHidden('palestrantes['+i+'][email]', p.email);
      if (p.cargo) addHidden('palestrantes['+i+'][cargo]', p.cargo);
    });
  }
  document.getElementById('btnAddPal')?.addEventListener('click', function(){
    const n = (document.getElementById('palNome').value || '').trim();
    const m = (document.getElementById('palEmail').value || '').trim();
    const c = (document.getElementById('palCargo').value || '').trim();
    if (!n) return;
    pal.push({nome:n, email:m||null, cargo:c||null});
    document.getElementById('palNome').value='';
    document.getElementById('palEmail').value='';
    document.getElementById('palCargo').value='';
    redrawPales();
  });
  tblPales?.addEventListener('click', function(e){
    if (e.target && e.target.matches('button[data-i]')) {
      pal.splice(parseInt(e.target.dataset.i),1);
      redrawPales();
    }
  });

  /* ======= Atividades ======= */
  const tblAtivs = document.querySelector('#tblAtividades tbody');
  const atividades = [];
  function localName(key){
    const l = locais.find(x=>x.key===key); return l ? l.nome : '';
  }
  function redrawAtivs(){
    if (!tblAtivs) return;
    tblAtivs.innerHTML = '';
    document.querySelectorAll('input[name^="atividades["]').forEach(x=>x.remove());
    atividades.forEach(function(a, i){
      const tr = document.createElement('tr');
      tr.innerHTML = '<td>'+a.titulo+'</td><td>'+(a.tipo||'')+'</td>'+
                     '<td>'+(a.inicio||'')+'</td><td>'+(a.fim||'')+'</td>'+
                     '<td>'+(localName(a.local_key)||'')+'</td>'+
                     '<td>'+(a.capacidade||'')+'</td>'+
                     '<td>'+(a.requer_inscricao ? 'Sim':'Não')+'</td>'+
                     '<td><button class="btn btn-xs btn-danger" data-i="'+i+'" type="button">rem</button></td>';
      tblAtivs.appendChild(tr);

      addHidden('atividades['+i+'][titulo]', a.titulo);
      if (a.tipo) addHidden('atividades['+i+'][tipo]', a.tipo);
      if (a.inicio) addHidden('atividades['+i+'][inicio]', a.inicio);
      if (a.fim) addHidden('atividades['+i+'][fim]', a.fim);
      if (a.local_key) addHidden('atividades['+i+'][local_key]', a.local_key);
      if (a.capacidade) addHidden('atividades['+i+'][capacidade]', a.capacidade);
      addHidden('atividades['+i+'][requer_inscricao]', a.requer_inscricao ? 1 : 0);
    });
  }
  document.getElementById('btnAddAtv')?.addEventListener('click', function(){
    const t   = (document.getElementById('atvTitulo').value || '').trim();
    if (!t) return;
    const tp  = document.getElementById('atvTipo').value || null;
    const ini = document.getElementById('atvInicio').value || null;
    const fim = document.getElementById('atvFim').value || null;
    const l   = document.getElementById('atvLocal').value || null;
    const cap = document.getElementById('atvCap').value || null;
    const req = !!document.getElementById('atvReqInscricao').checked;

    atividades.push({titulo:t, tipo:tp, inicio:ini, fim:fim, local_key:l, capacidade:cap, requer_inscricao:req});

    ['atvTitulo','atvTipo','atvInicio','atvFim','atvLocal','atvCap'].forEach(id=>{ const el=document.getElementById(id); if(el) el.value=''; });
    document.getElementById('atvReqInscricao').checked=false;

    redrawAtivs();
  });
  tblAtivs?.addEventListener('click', function(e){
    if (e.target && e.target.matches('button[data-i]')) {
      atividades.splice(parseInt(e.target.dataset.i),1);
      redrawAtivs();
    }
  });

  /* ======= Utilitários ======= */
  const formEl = document.getElementById('eventoForm');
  function addHidden(name, value){
    if (!formEl) return;
    const input = document.createElement('input');
    input.type = 'hidden'; input.name = name; input.value = value;
    formEl.appendChild(input);
  }
  function ensureHiddenCollections(){
    // garante que os inputs ocultos existam antes de enviar
    redrawPales();
    redrawAtivs();
    // se escolheu local no mapa e não tocou mais, certifique hidden
    if (locais.length) localNomeHidden.value = locais[0].nome;
    redrawLocaisOnSelect();
  }

})();
</script>
@endsection
