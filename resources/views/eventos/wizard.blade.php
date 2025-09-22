@extends('layouts.new-event')
@section('title', isset($evento) ? 'Editar evento' : 'Novo evento')

@section('content')
@php
  // Descobre passo inicial se houve erro de validação
  $initialStep = 1;
  if ($errors->has('data_inicio_inscricao') || $errors->has('data_fim_inscricao') || $errors->has('status')) {
      $initialStep = 2;
  }
  // (se no futuro tiver erros de personalização, pode setar $initialStep = 3;)
@endphp

<style>
  /* visual do wizard */
  .wizard-steps { margin: 10px 0 20px; display:flex; gap:12px; }
  .wizard-steps li a { border-radius:10px; padding:14px 18px; border:1px solid #e1e1e1; color:#444; background:#fff; }
  .wizard-steps li.active a { background:#e91e63; color:#fff; border-color:#e91e63; }
  .wizard-steps li.done a { background:#2e7d32; color:#fff; border-color:#2e7d32; }
  .wizard-step { display:none; }
  .wizard-step.active { display:block; }
  .section-card { background:#fff; border:1px solid #eee; border-radius:10px; padding:18px; margin-bottom:18px; }
  .helper { font-size:12px; color:#888; }
  .badge-switch { display:inline-block; padding:6px 10px; border-radius:20px; background:#eee; cursor:pointer; user-select:none; }
  .badge-switch.active { background:#2e7d32; color:#fff; }
</style>

<div class="container" style="padding:30px 0; max-width:1100px">
  <div class="row">
    <div class="col-sm-8">
      <h3 style="margin-top:0">{{ isset($evento) ? 'Editar evento' : 'Novo evento' }}</h3>
    </div>
    <div class="col-sm-4 text-right">
      <a href="{{ route('eventos.index') }}" class="btn btn-default">Voltar</a>
    </div>
  </div>

  {{-- STEPS --}}
  <ul class="nav nav-pills wizard-steps" id="wizardTabs">
    <li class="{{ $initialStep === 1 ? 'active' : '' }}"><a href="#" data-step="1">Passo 1<br><small>Informações</small></a></li>
    <li class="{{ $initialStep === 2 ? 'active' : '' }}"><a href="#" data-step="2">Passo 2<br><small>Inscrições</small></a></li>
    <li class="{{ $initialStep === 3 ? 'active' : '' }}"><a href="#" data-step="3">Passo 3<br><small>Personalização</small></a></li>
  </ul>

  {{-- Form principal --}}
  <form method="post" action="{{ isset($evento) ? route('eventos.update',$evento) : route('eventos.store') }}" autocomplete="off">
    @csrf
    @if(isset($evento)) @method('PUT') @endif

    {{-- PASSO 1 – INFORMAÇÕES --}}
    <div class="wizard-step {{ $initialStep === 1 ? 'active' : '' }}" data-step="1">
      <div class="section-card">
        <h4>Sobre o evento</h4>

        {{-- TIPO DO EVENTO (valores normalizados) --}}
        @php
          $tipos = [
            'presencial' => 'Presencial',
            'online'     => 'Online',
            'hibrido'    => 'Híbrido',
            'videoconf'  => 'Videoconf.'
          ];
          $tipoAtual = old('tipo_evento', $evento->tipo_evento ?? 'presencial');
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
          <label>Nome do evento *</label>
          <input name="nome" class="form-control" required
                 value="{{ old('nome', $evento->nome ?? '') }}">
          @error('nome') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
          <label>Descrição</label>
          <textarea name="descricao" rows="7" class="form-control">{{ old('descricao', $evento->descricao ?? '') }}</textarea>
          @error('descricao') <div class="text-danger">{{ $message }}</div> @enderror
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
            <div class="form-group">
              <label>Início *</label>
              <input type="datetime-local" name="data_inicio_evento" class="form-control" required value="{{ $di }}">
              @error('data_inicio_evento') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label>Término *</label>
              <input type="datetime-local" name="data_fim_evento" class="form-control" required value="{{ $df }}">
              @error('data_fim_evento') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>
        <p class="helper">Esses campos são salvos no seu modelo (<em>data_inicio_evento</em>, <em>data_fim_evento</em>).</p>
      </div>

      @isset($coordenadores)
      <div class="section-card">
        <h4>Coordenador</h4>
        <div class="form-group">
          @php $sel = old('coordenador_id', $evento->coordenador_id ?? auth()->id()); @endphp
          <select name="coordenador_id" class="form-control">
            @foreach($coordenadores as $c)
              <option value="{{ $c->id }}" @selected($sel==$c->id)>{{ $c->name }} ({{ $c->email }})</option>
            @endforeach
          </select>
          @error('coordenador_id') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
      </div>
      @endisset
    </div>

    {{-- PASSO 2 – INSCRIÇÕES --}}
    <div class="wizard-step {{ $initialStep === 2 ? 'active' : '' }}" data-step="2">
      <div class="section-card">
        <h4>Liberar inscrições</h4>
        <p class="helper" style="margin-top:-8px">Defina o período de inscrição. O botão “Inscrever-se” só aparece dentro desta janela.</p>

        @php
          $ii = old('data_inicio_inscricao', isset($evento)? $evento->data_inicio_inscricao?->format('Y-m-d\TH:i') : '' );
          $if = old('data_fim_inscricao',    isset($evento)? $evento->data_fim_inscricao?->format('Y-m-d\TH:i')    : '' );
        @endphp

        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
              <label>Início das inscrições *</label>
              <input type="datetime-local" name="data_inicio_inscricao" class="form-control" required value="{{ $ii }}">
              @error('data_inicio_inscricao') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
              <label>Fim das inscrições *</label>
              <input type="datetime-local" name="data_fim_inscricao" class="form-control" required value="{{ $if }}">
              @error('data_fim_inscricao') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>

        <div class="form-group">
          <label>Status do evento</label><br>
          @php $st = old('status', $evento->status ?? 'rascunho'); @endphp
          <span class="badge-switch {{ in_array($st,['ativo','publicado'])?'active':'' }}" id="switchInscr">
            Inscrições {{ in_array($st,['ativo','publicado'])?'Liberadas':'Fechadas' }}
          </span>
          <input type="hidden" name="status" id="statusField" value="{{ $st }}">
          <p class="helper" style="margin-top:6px">Clique para alternar: rascunho ⇄ ativo</p>
          @error('status') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- (Opcional) Vagas — só salva se você criar a coluna depois --}}
        {{-- <div class="form-group">
            <label>Número de vagas</label>
            <input type="number" name="vagas" class="form-control" min="1">
            <p class="helper">Para salvar “vagas”, crie a coluna no banco. Por enquanto é visual.</p>
        </div> --}}
      </div>
    </div>

    {{-- PASSO 3 – PERSONALIZAÇÃO --}}
    <div class="wizard-step {{ $initialStep === 3 ? 'active' : '' }}" data-step="3">
      <div class="section-card">
        <h4>Personalização</h4>

        <div class="form-group">
          <label>Logomarca (URL)</label>
          <input type="url" name="logomarca_url" class="form-control"
                 value="{{ old('logomarca_url', $evento->logomarca_url ?? '') }}"
                 placeholder="https://…">
          @error('logomarca_url') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
          <label>Cor do tema (visual/apenas front)</label>
          <input type="color" class="form-control" style="height:40px; width:90px; padding:0">
          <p class="helper">A cor não é salva (apenas pré-visualização). Se quiser persistir, criamos coluna depois.</p>
        </div>

        <div class="form-group">
          <label>Observações para credenciamento (não salvo)</label>
          <textarea rows="5" class="form-control" placeholder="Texto livre para credenciamento…"></textarea>
        </div>
      </div>
    </div>

    {{-- Navegação inferior --}}
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
    var maxStep = 3;

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
      // botões
      document.getElementById('btnPrev').style.display = (step===1)?'none':'inline-block';
      document.getElementById('btnNext').style.display = (step===maxStep)?'none':'inline-block';
      document.getElementById('btnSave').style.display = (step===maxStep)?'inline-block':'none';
    }

    // tabs
    document.querySelectorAll('#wizardTabs a').forEach(function(a){
      a.addEventListener('click', function(e){ e.preventDefault(); go(parseInt(a.dataset.step)); });
    });

    document.getElementById('btnPrev').addEventListener('click', function(){ go(step-1); });
    document.getElementById('btnNext').addEventListener('click', function(){ go(step+1); });

    // switch de inscrições (status)
    var switchInscr = document.getElementById('switchInscr');
    var statusField = document.getElementById('statusField');
    if (switchInscr) {
      switchInscr.addEventListener('click', function() {
        var st = statusField.value || 'rascunho';
        var novo = (st === 'rascunho') ? 'ativo' : 'rascunho';
        statusField.value = novo;
        switchInscr.classList.toggle('active', novo !== 'rascunho');
        switchInscr.textContent = 'Inscrições ' + (novo !== 'rascunho' ? 'Liberadas' : 'Fechadas');
      });
    }

    go(step);
  })();
</script>
@endsection
