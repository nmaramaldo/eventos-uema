@php
  // helper para datetime-local
  function dtval($dt) { return $dt ? $dt->format('Y-m-d\TH:i') : ''; }
@endphp

<div class="row">
  <div class="col-sm-8">
    <div class="form-group">
      <label>Nome do evento</label>
      <input name="nome" class="form-control" value="{{ old('nome', $evento->nome ?? '') }}" required>
      @error('nome') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
      <label>Descrição</label>
      <textarea name="descricao" rows="6" class="form-control">{{ old('descricao', $evento->descricao ?? '') }}</textarea>
      @error('descricao') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="row">
      <div class="col-sm-6">
        <div class="form-group">
          <label>Tipo do evento</label>
          <input name="tipo_evento" class="form-control" value="{{ old('tipo_evento', $evento->tipo_evento ?? '') }}">
          @error('tipo_evento') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group">
          <label>Status</label>
          <select name="status" class="form-control">
            @php $st = old('status', $evento->status ?? 'rascunho'); @endphp
            @foreach(['rascunho'=>'Rascunho','ativo'=>'Ativo','publicado'=>'Publicado'] as $k=>$label)
              <option value="{{ $k }}" @selected($st===$k)>{{ $label }}</option>
            @endforeach
          </select>
          @error('status') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
      </div>
    </div>

    <div class="form-group">
      <label>URL da logomarca (opcional)</label>
      <input type="url" name="logomarca_url" class="form-control" value="{{ old('logomarca_url', $evento->logomarca_url ?? '') }}" placeholder="https://…">
      @error('logomarca_url') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    @isset($coordenadores)
    <div class="form-group">
      <label>Coordenador</label>
      <select name="coordenador_id" class="form-control">
        @php $sel = old('coordenador_id', $evento->coordenador_id ?? auth()->id()); @endphp
        @foreach($coordenadores as $c)
          <option value="{{ $c->id }}" @selected($sel == $c->id)>{{ $c->name }} ({{ $c->email }})</option>
        @endforeach
      </select>
      @error('coordenador_id') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    @endisset
  </div>

  <div class="col-sm-4">
    <div class="form-group">
      <label>Início do evento</label>
      <input type="datetime-local" name="data_inicio_evento" class="form-control"
             value="{{ old('data_inicio_evento', isset($evento) ? dtval($evento->data_inicio_evento) : '') }}" required>
      @error('data_inicio_evento') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
      <label>Fim do evento</label>
      <input type="datetime-local" name="data_fim_evento" class="form-control"
             value="{{ old('data_fim_evento', isset($evento) ? dtval($evento->data_fim_evento) : '') }}" required>
      @error('data_fim_evento') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
      <label>Início das inscrições</label>
      <input type="datetime-local" name="data_inicio_inscricao" class="form-control"
             value="{{ old('data_inicio_inscricao', isset($evento) ? dtval($evento->data_inicio_inscricao) : '') }}" required>
      @error('data_inicio_inscricao') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
      <label>Fim das inscrições</label>
      <input type="datetime-local" name="data_fim_inscricao" class="form-control"
             value="{{ old('data_fim_inscricao', isset($evento) ? dtval($evento->data_fim_inscricao) : '') }}" required>
      @error('data_fim_inscricao') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
  </div>
</div>
