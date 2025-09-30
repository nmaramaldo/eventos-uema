{{-- resources/views/eventos/wizard.blade.php --}}
@extends('layouts.new-event')
@section('title', isset($evento) ? 'Editar evento' : 'Novo evento')

@section('content')
@php
  $initialStep = 1;
  if ($errors->has('data_inicio_inscricao') || $errors->has('data_fim_inscricao') || $errors->has('status')) {
      $initialStep = 2;
  }
@endphp

<style>
  .wizard-steps { margin:10px 0 20px; display:flex; gap:12px; flex-wrap:wrap }
  .wizard-steps li { list-style:none }
  .wizard-steps li a { border-radius:10px; padding:14px 18px; border:1px solid #e1e1e1; color:#444; background:#fff; display:block; text-decoration:none }
  .wizard-steps li.active a { background:#e91e63; color:#fff; border-color:#e91e63 }
  .wizard-steps li.done a { background:#2e7d32; color:#fff; border-color:#2e7d32 }
  .wizard-step { display:none }
  .wizard-step.active { display:block }
  .section-card { background:#fff; border:1px solid #eee; border-radius:10px; padding:18px; margin-bottom:18px }
  .helper { font-size:12px; color:#888 }
  .badge-switch { display:inline-block; padding:6px 10px; border-radius:20px; background:#eee; cursor:pointer; user-select:none }
  .badge-switch.active { background:#2e7d32; color:#fff }
  .mini-table th, .mini-table td { padding:6px 8px; font-size:13px }
</style>

<div class="container" style="padding:30px 0; max-width:1100px">
  <div class="row">
    <div class="col-sm-8"><h3 style="margin-top:0">{{ isset($evento) ? 'Editar evento' : 'Novo evento' }}</h3></div>
    <div class="col-sm-4 text-right"><a href="{{ route('eventos.index') }}" class="btn btn-default">Voltar</a></div>
  </div>

  <ul class="nav nav-pills wizard-steps" id="wizardTabs">
    <li class="{{ $initialStep === 1 ? 'active' : '' }}"><a href="#" data-step="1">Passo 1<br><small>Informações</small></a></li>
    <li class="{{ $initialStep === 2 ? 'active' : '' }}"><a href="#" data-step="2">Passo 2<br><small>Inscrições</small></a></li>
    <li class="{{ $initialStep === 3 ? 'active' : '' }}"><a href="#" data-step="3">Passo 3<br><small>Personalização</small></a></li>
    <li class=""><a href="#" data-step="4">Passo 4<br><small>Programação</small></a></li>
  </ul>

  <form
      id="wizardForm"
      method="post"
      action="{{ isset($evento) ? route('eventos.update',$evento) : route('eventos.store') }}"
      autocomplete="off"
      enctype="multipart/form-data"
      {{-- IMPORTANTE: desativa validação nativa do browser para não travar submit com campos escondidos --}}
      novalidate
  >
    @csrf @if(isset($evento)) @method('PUT') @endif

    {{-- PASSO 1 --}}
    <div class="wizard-step {{ $initialStep === 1 ? 'active' : '' }}" data-step="1">
      <div class="section-card">
        <h4>Sobre o evento</h4>
        @php
          $tipos = ['presencial'=>'Presencial','online'=>'Online','hibrido'=>'Híbrido','videoconf'=>'Videoconf.'];
          $tipoAtual = old('tipo_evento', $evento->tipo_evento ?? 'presencial');

          $classificacoes = ['-- Selecione uma opção --','Acadêmico - Seminário/Jornada','Científico - Congresso/Simpósio','Corporativo - Empresarial','Curso - Workshop/Palestra','Entretenimento - Show/Festa','Religioso - Retiro/Encontro/Beneficente','Esportivo - Jogos/Torneios','Exibição - Feira/Exposição','Networking - Encontro/Meetup','Outro'];
          $areas = ['-- Selecione uma opção --','Agricultura, pesca e veterinária','Artes e humanidades','Ciências sociais e jornalismo','Computação e Tecnologias da Informação','Direito','Educação','Empreendedorismo e inovação','Engenharias','Gastronomia','Medicina','Negócios e administração','Saúde e bem-estar','Desenvolvimento pessoal','Religioso e Espiritualidade','Outros'];

          $selClass = old('tipo_classificacao', $evento->tipo_classificacao ?? '');
          $selArea  = old('area_tematica',      $evento->area_tematica      ?? '');
        @endphp

        <div class="form-group">
          <label><strong>Tipo do evento</strong></label><br>
          @foreach ($tipos as $valor => $rotulo)
            <label class="radio-inline" style="margin-right:12px">
              <input type="radio" name="tipo_evento" value="{{ $valor }}" {{ $tipoAtual === $valor ? 'checked' : '' }}>
              {{ $rotulo }}
            </label>
          @endforeach
          @error('tipo_evento') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
          <label>Categoria do evento</label>
          <select name="tipo_classificacao" class="form-control">
            @foreach($classificacoes as $op)
              <option value="{{ $op === '-- Selecione uma opção --' ? '' : $op }}" {{ $selClass===$op ? 'selected' : '' }}>{{ $op }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label>Área temática</label>
          <select name="area_tematica" class="form-control">
            @foreach($areas as $op)
              <option value="{{ $op === '-- Selecione uma opção --' ? '' : $op }}" {{ $selArea===$op ? 'selected' : '' }}>{{ $op }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label>Nome do evento *</label>
          <input name="nome" class="form-control" required value="{{ old('nome', $evento->nome ?? '') }}">
          @error('nome') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
          <label>Descrição</label>
          <textarea name="descricao" rows="7" class="form-control">{{ old('descricao', $evento->descricao ?? '') }}</textarea>
        </div>
      </div>

      <div class="section-card">
        <h4>Data e hora</h4>
        @php
          $di = old('data_inicio_evento', isset($evento)? $evento->data_inicio_evento?->format('Y-m-d\TH:i') : '' );
          $df = old('data_fim_evento',    isset($evento)? $evento->data_fim_evento?->format('Y-m-d\TH:i')    : '' );
        @endphp
        <div class="row">
          <div class="col-sm-6">
            <label>Início *</label>
            <input type="datetime-local" name="data_inicio_evento" class="form-control" required value="{{ $di }}">
          </div>
          <div class="col-sm-6">
            <label>Término *</label>
            <input type="datetime-local" name="data_fim_evento" class="form-control" required value="{{ $df }}">
          </div>
        </div>
      </div>

      @isset($coordenadores)
      <div class="section-card">
        <h4>Coordenador</h4>
        @php $sel = old('coordenador_id', $evento->coordenador_id ?? auth()->id()); @endphp
        <select name="coordenador_id" class="form-control">
          @foreach($coordenadores as $c)
            <option value="{{ $c->id }}" @selected($sel==$c->id)>{{ $c->name }} ({{ $c->email }})</option>
          @endforeach
        </select>
      </div>
      @endisset
    </div>

    {{-- PASSO 2 --}}
    <div class="wizard-step {{ $initialStep === 2 ? 'active' : '' }}" data-step="2">
      <div class="section-card">
        <h4>Liberar inscrições</h4>
        @php
          $ii = old('data_inicio_inscricao', isset($evento)? $evento->data_inicio_inscricao?->format('Y-m-d\TH:i') : '' );
          $if = old('data_fim_inscricao',    isset($evento)? $evento->data_fim_inscricao?->format('Y-m-d\TH:i')    : '' );
          $st = old('status', $evento->status ?? 'rascunho');
        @endphp
        <div class="row">
          <div class="col-sm-6">
            <label>Início das inscrições *</label>
            <input type="datetime-local" name="data_inicio_inscricao" class="form-control" required value="{{ $ii }}">
          </div>
          <div class="col-sm-6">
            <label>Fim das inscrições *</label>
            <input type="datetime-local" name="data_fim_inscricao" class="form-control" required value="{{ $if }}">
          </div>
        </div>

        <div class="form-group" style="margin-top:8px">
          <label>Status do evento</label><br>
          <span class="badge-switch {{ in_array($st,['ativo','publicado'])?'active':'' }}" id="switchInscr">
            Inscrições {{ in_array($st,['ativo','publicado'])?'Liberadas':'Fechadas' }}
          </span>
          <input type="hidden" name="status" id="statusField" value="{{ $st }}">
          <p class="helper" style="margin-top:6px">Clique para alternar: rascunho ⇄ ativo</p>
        </div>

        <div class="form-group">
          <label>Vagas do evento (opcional)</label>
          <input type="number" min="1" class="form-control" name="vagas" value="{{ old('vagas', $evento->vagas ?? '') }}">
          <p class="helper">Se sua tabela <em>eventos</em> tiver a coluna <code>vagas</code>, esse valor será salvo.</p>
        </div>
      </div>
    </div>

    {{-- PASSO 3 --}}
    <div class="wizard-step {{ $initialStep === 3 ? 'active' : '' }}" data-step="3">
      <div class="section-card">
        <h4>Personalização</h4>

        <div class="form-group">
          <label>Logomarca (URL)</label>
          {{-- type="text" + novalidate no form => não trava submit quando o campo estiver escondido --}}
          <input type="text" name="logomarca_url" inputmode="url" class="form-control"
                 value="{{ old('logomarca_url', $evento->logomarca_url ?? '') }}"
                 placeholder="https://…">
        </div>

        <div class="form-group">
          <label>Capa do evento (1100×440)</label>
          <input type="file" name="capa" accept="image/*" class="form-control">
          <div style="margin-top:8px; display:flex; gap:8px; flex-wrap:wrap">
            <a href="https://www.canva.com/design/DAG0YtdvT_Y/xRa6bYsX9pvzsRNA2Bu4Fw/edit"
               target="_blank" class="btn btn-default">
              Criar com o Canva
            </a>
            @if(!empty($evento?->logomarca_url))
              <a href="{{ $evento->logomarca_url }}" target="_blank" class="btn btn-default">Ver capa atual</a>
            @endif
          </div>
          @error('capa') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
          <label>Cor do tema (visual)</label>
          <input type="color" class="form-control" style="height:40px; width:90px; padding:0">
        </div>
      </div>
    </div>

    {{-- PASSO 4 – PROGRAMAÇÃO --}}
    <div class="wizard-step" data-step="4">
      {{-- LOCAIS --}}
      <div class="section-card">
        <h4>Locais</h4>
        <div class="row" style="gap:10px">
          <div class="col-sm-6">
            <input id="localNome" class="form-control" placeholder="Ex.: CCT - Auditório 1">
          </div>
          <div class="col-sm-3">
            <button type="button" class="btn btn-default" id="btnAddLocal">+ Adicionar local</button>
          </div>
        </div>
        <div style="margin-top:10px">
          <table class="table table-bordered mini-table" id="tblLocais">
            <thead><tr><th>Local</th><th style="width:60px">Ações</th></tr></thead>
            <tbody></tbody>
          </table>
        </div>
      </div>

      {{-- CONVIDADOS / PALESTRANTES --}}
      <div class="section-card">
        <h4>Convidados / Palestrantes</h4>
        <div class="row" style="gap:10px">
          <div class="col-sm-3"><input id="palNome" class="form-control" placeholder="Nome"></div>
          <div class="col-sm-3"><input id="palEmail" class="form-control" placeholder="E-mail (opcional)"></div>
          <div class="col-sm-3"><input id="palCargo" class="form-control" placeholder="Cargo/Instituição (opcional)"></div>
          <div class="col-sm-2"><button type="button" class="btn btn-default" id="btnAddPal">+ Adicionar</button></div>
        </div>
        <div style="margin-top:10px">
          <table class="table table-bordered mini-table" id="tblPalestrantes">
            <thead><tr><th>Nome</th><th>E-mail</th><th>Cargo/Inst.</th><th style="width:60px">Ações</th></tr></thead>
            <tbody></tbody>
          </table>
        </div>
      </div>

      {{-- ATIVIDADES --}}
      <div class="section-card">
        <h4>Atividades</h4>
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
          <div class="col-sm-2"><input id="atvInicio" type="datetime-local" class="form-control" placeholder="Início"></div>
          <div class="col-sm-2"><input id="atvFim" type="datetime-local" class="form-control" placeholder="Fim"></div>
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
                <th>Título</th><th>Tipo</th><th>Início</th><th>Fim</th><th>Local</th><th>Cap.</th><th>Inscr.</th><th style="width:60px">Ações</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- NAV --}}
    <div class="text-right" style="margin-top:10px">
      <button type="button" class="btn btn-default" id="btnPrev">Voltar</button>
      <button type="button" class="btn btn-default" id="btnNext">Próximo</button>
      <button class="btn btn-primary" id="btnSave">{{ isset($evento) ? 'Salvar alterações' : 'Salvar' }}</button>
    </div>
  </form>
</div>

<script>
(function() {
  var step = {{ (int) $initialStep }};
  var maxStep = 4;

  function go(s) {
    step = Math.max(1, Math.min(maxStep, s));
    document.querySelectorAll('.wizard-step').forEach(function(el){
      el.classList.toggle('active', el.getAttribute('data-step') == step);
    });
    document.querySelectorAll('.wizard-steps li').forEach(function(li, idx){
      li.classList.remove('active','done');
      if (idx+1 === step) li.classList.add('active');
      if (idx+1 < step)    li.classList.add('done');
    });
    var prev = document.getElementById('btnPrev');
    var next = document.getElementById('btnNext');
    var save = document.getElementById('btnSave');
    if (prev && next && save) {
      prev.style.display = (step===1)?'none':'inline-block';
      next.style.display = (step===maxStep)?'none':'inline-block';
      save.style.display = (step===maxStep)?'inline-block':'none';
    }
  }

  // navegação
  document.querySelectorAll('#wizardTabs a').forEach(function(a){
    a.addEventListener('click', function(e){ e.preventDefault(); go(parseInt(a.dataset.step || '1')); });
  });
  var btnPrev = document.getElementById('btnPrev');
  var btnNext = document.getElementById('btnNext');
  if (btnPrev) btnPrev.addEventListener('click', function(){ go(step-1); });
  if (btnNext) btnNext.addEventListener('click', function(){ go(step+1); });

  // switch inscrições
  var switchInscr = document.getElementById('switchInscr');
  var statusField = document.getElementById('statusField');
  if (switchInscr && statusField) {
    switchInscr.addEventListener('click', function() {
      var st = statusField.value || 'rascunho';
      var novo = (st === 'rascunho') ? 'ativo' : 'rascunho';
      statusField.value = novo;
      switchInscr.classList.toggle('active', novo !== 'rascunho');
      switchInscr.textContent = 'Inscrições ' + (novo !== 'rascunho' ? 'Liberadas' : 'Fechadas');
    });
  }

  // ------- Passo 4: estruturas em memória ----------
  var locais = [];         // {key, nome}
  var palestrantes = [];   // {nome,email,cargo}
  var atividades = [];     // {titulo,tipo,inicio,fim,local_key,capacidade,requer_inscricao}

  var tblLocais = document.querySelector('#tblLocais tbody');
  var tblPales = document.querySelector('#tblPalestrantes tbody');
  var tblAtivs = document.querySelector('#tblAtividades tbody');
  var selLocal = document.getElementById('atvLocal');
  var formEl = document.getElementById('wizardForm');

  function addHidden(name, value){
    if (!formEl) return;
    var input = document.createElement('input');
    input.type = 'hidden'; input.name = name; input.value = value;
    formEl.appendChild(input);
  }

  function localName(key){
    var l = locais.find(function(x){ return x.key===key; });
    return l ? l.nome : '';
  }

  function redrawLocais(){
    if (!tblLocais || !selLocal) return;
    tblLocais.innerHTML = '';
    selLocal.innerHTML = '<option value="">Local</option>';
    locais.forEach(function(l, i){
      var tr = document.createElement('tr');
      tr.innerHTML = '<td>'+l.nome+'</td>'+
                     '<td><button class="btn btn-xs btn-danger" data-i="'+i+'" type="button">rem</button></td>';
      tblLocais.appendChild(tr);

      var opt = document.createElement('option');
      opt.value = l.key;
      opt.textContent = l.nome;
      selLocal.appendChild(opt);
    });
    // recreate hidden inputs
    document.querySelectorAll('input[name^="locais["]').forEach(function(x){ x.parentNode && x.parentNode.removeChild(x); });
    locais.forEach(function(l, i){
      addHidden('locais['+i+'][nome]', l.nome);
    });
  }

  function redrawPales(){
    if (!tblPales) return;
    tblPales.innerHTML = '';
    palestrantes.forEach(function(p, i){
      var tr = document.createElement('tr');
      tr.innerHTML = '<td>'+p.nome+'</td><td>'+(p.email||'')+'</td><td>'+(p.cargo||'')+'</td>'+
                     '<td><button class="btn btn-xs btn-danger" data-i="'+i+'" type="button">rem</button></td>';
      tblPales.appendChild(tr);
    });
    document.querySelectorAll('input[name^="palestrantes["]').forEach(function(x){ x.parentNode && x.parentNode.removeChild(x); });
    palestrantes.forEach(function(p, i){
      addHidden('palestrantes['+i+'][nome]', p.nome);
      if (p.email) addHidden('palestrantes['+i+'][email]', p.email);
      if (p.cargo) addHidden('palestrantes['+i+'][cargo]', p.cargo);
    });
  }

  function redrawAtivs(){
    if (!tblAtivs) return;
    tblAtivs.innerHTML = '';
    atividades.forEach(function(a, i){
      var tr = document.createElement('tr');
      tr.innerHTML = '<td>'+a.titulo+'</td><td>'+(a.tipo||'')+'</td>'+
                     '<td>'+(a.inicio||'')+'</td><td>'+(a.fim||'')+'</td>'+
                     '<td>'+(localName(a.local_key)||'')+'</td>'+
                     '<td>'+(a.capacidade||'')+'</td>'+
                     '<td>'+(a.requer_inscricao ? 'Sim':'Não')+'</td>'+
                     '<td><button class="btn btn-xs btn-danger" data-i="'+i+'" type="button">rem</button></td>';
      tblAtivs.appendChild(tr);
    });
    document.querySelectorAll('input[name^="atividades["]').forEach(function(x){ x.parentNode && x.parentNode.removeChild(x); });
    atividades.forEach(function(a, i){
      addHidden('atividades['+i+'][titulo]', a.titulo);
      if (a.tipo) addHidden('atividades['+i+'][tipo]', a.tipo);
      if (a.inicio) addHidden('atividades['+i+'][inicio]', a.inicio);
      if (a.fim) addHidden('atividades['+i+'][fim]', a.fim);
      if (a.local_key) addHidden('atividades['+i+'][local_key]', a.local_key);
      if (a.capacidade) addHidden('atividades['+i+'][capacidade]', a.capacidade);
      addHidden('atividades['+i+'][requer_inscricao]', a.requer_inscricao ? 1 : 0);
    });
  }

  // adicionar/remover local
  var btnAddLocal = document.getElementById('btnAddLocal');
  if (btnAddLocal) btnAddLocal.addEventListener('click', function(){
    var nomeEl = document.getElementById('localNome');
    var nome = (nomeEl && nomeEl.value ? nomeEl.value : '').trim();
    if (!nome) return;
    var key  = 'row'+(locais.length);
    locais.push({ key:key, nome:nome });
    if (nomeEl) nomeEl.value='';
    redrawLocais();
  });
  if (tblLocais) tblLocais.addEventListener('click', function(e){
    if (e.target && e.target.matches('button[data-i]')) {
      locais.splice(parseInt(e.target.dataset.i),1);
      redrawLocais();
      redrawAtivs();
    }
  });

  // adicionar/remover palestrante
  var btnAddPal = document.getElementById('btnAddPal');
  if (btnAddPal) btnAddPal.addEventListener('click', function(){
    var nEl = document.getElementById('palNome');
    var mEl = document.getElementById('palEmail');
    var cEl = document.getElementById('palCargo');
    var n = (nEl && nEl.value ? nEl.value : '').trim();
    var m = (mEl && mEl.value ? mEl.value : '').trim();
    var c = (cEl && cEl.value ? cEl.value : '').trim();
    if (!n) return;
    palestrantes.push({nome:n, email:m||null, cargo:c||null});
    if (nEl) nEl.value=''; if (mEl) mEl.value=''; if (cEl) cEl.value='';
    redrawPales();
  });
  if (tblPales) tblPales.addEventListener('click', function(e){
    if (e.target && e.target.matches('button[data-i]')) {
      palestrantes.splice(parseInt(e.target.dataset.i),1);
      redrawPales();
    }
  });

  // adicionar/remover atividade
  var btnAddAtv = document.getElementById('btnAddAtv');
  if (btnAddAtv) btnAddAtv.addEventListener('click', function(){
    var tEl = document.getElementById('atvTitulo');
    var tipoEl = document.getElementById('atvTipo');
    var iniEl = document.getElementById('atvInicio');
    var fimEl = document.getElementById('atvFim');
    var localEl = document.getElementById('atvLocal');
    var capEl = document.getElementById('atvCap');
    var reqEl = document.getElementById('atvReqInscricao');

    var t = (tEl && tEl.value ? tEl.value : '').trim();
    if (!t) return;
    var a = {
      titulo: t,
      tipo:  tipoEl ? (tipoEl.value || null) : null,
      inicio:iniEl ? (iniEl.value || null) : null,
      fim:   fimEl ? (fimEl.value || null) : null,
      local_key: localEl ? (localEl.value || null) : null,
      capacidade: capEl && capEl.value ? capEl.value : null,
      requer_inscricao: reqEl ? !!reqEl.checked : false
    };
    atividades.push(a);
    if (tEl) tEl.value=''; if (tipoEl) tipoEl.value=''; if (iniEl) iniEl.value='';
    if (fimEl) fimEl.value=''; if (localEl) localEl.value=''; if (capEl) capEl.value='';
    if (reqEl) reqEl.checked=false;
    redrawAtivs();
  });
  if (tblAtivs) tblAtivs.addEventListener('click', function(e){
    if (e.target && e.target.matches('button[data-i]')) {
      atividades.splice(parseInt(e.target.dataset.i),1);
      redrawAtivs();
    }
  });

  // -------- seed de old() para não perder dados quando a página volta --------
  try {
    var seedLocais = @json(old('locais', []));
    var seedPales  = @json(old('palestrantes', []));
    var seedAtivs  = @json(old('atividades', []));

    if (Array.isArray(seedLocais) && seedLocais.length) {
      locais = seedLocais.map(function(l, i){ return { key:'row'+i, nome: (l && l.nome) ? l.nome : '' }; });
      redrawLocais();
    }
    if (Array.isArray(seedPales) && seedPales.length) {
      palestrantes = seedPales.map(function(p){
        return { nome: p?.nome || '', email: p?.email || null, cargo: p?.cargo || null };
      });
      redrawPales();
    }
    if (Array.isArray(seedAtivs) && seedAtivs.length) {
      atividades = seedAtivs.map(function(a){
        return {
          titulo: a?.titulo || '',
          tipo: a?.tipo || null,
          inicio: a?.inicio || null,
          fim: a?.fim || null,
          local_key: a?.local_key || null,
          capacidade: a?.capacidade || null,
          requer_inscricao: !!(+a?.requer_inscricao || a?.requer_inscricao)
        };
      });
      redrawAtivs();
    }
  } catch(e) { /* ignore */ }

  // garante que os inputs ocultos existam no POST
  if (formEl) {
    formEl.addEventListener('submit', function(){
      redrawLocais();
      redrawPales();
      redrawAtivs();
    });
  }

  go(step);
})();
</script>
@endsection
