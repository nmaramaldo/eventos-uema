@extends('layouts.new-event')
@section('title','Nova atividade - '.$evento->nome)

@section('content')
<div class="container" style="padding:30px 0; max-width:800px">
  <h3 style="margin-top:0">Nova atividade — {{ $evento->nome }}</h3>

  <form method="post" action="{{ route('eventos.programacao.store', $evento) }}" autocomplete="off">
    @csrf

    <div class="form-group">
      <label>Título *</label>
      <input name="titulo" class="form-control" required value="{{ old('titulo') }}">
      @error('titulo') <div class="text-danger">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
      <label>Descrição</label>
      <textarea name="descricao" rows="5" class="form-control">{{ old('descricao') }}</textarea>
      @error('descricao') <div class="text-danger">{{ $message }}</div> @enderror
    </div>

    <div class="row">
      <div class="col-sm-6">
        <div class="form-group">
          <label>Início *</label>
          <input type="datetime-local" name="data_hora_inicio" class="form-control" required value="{{ old('data_hora_inicio') }}">
          @error('data_hora_inicio') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group">
          <label>Fim *</label>
          <input type="datetime-local" name="data_hora_fim" class="form-control" required value="{{ old('data_hora_fim') }}">
          @error('data_hora_fim') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
      </div>
    </div>

    <div class="form-group">
      <label>Local</label>
      <select name="local_id" class="form-control">
        <option value="">— Selecione —</option>
        @foreach($locais as $l)
          <option value="{{ $l->id }}" @selected(old('local_id')==$l->id)>{{ $l->nome }}</option>
        @endforeach
      </select>
      @error('local_id') <div class="text-danger">{{ $message }}</div> @enderror
      <p class="help-block">Cadastre locais em <em>Admin → Locais</em> (ou via seeder).</p>
    </div>

    <div class="row">
      <div class="col-sm-6">
        <div class="checkbox">
          <label>
            <input type="checkbox" name="requer_inscricao" value="1" {{ old('requer_inscricao') ? 'checked' : '' }}>
            Requer inscrição específica?
          </label>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group">
          <label>Vagas (opcional)</label>
          <input type="number" min="1" name="vagas" class="form-control" value="{{ old('vagas') }}">
          @error('vagas') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
      </div>
    </div>

    <div class="text-right">
      <a href="{{ route('eventos.programacao.index', $evento) }}" class="btn btn-default">Cancelar</a>
      <button class="btn btn-primary">Salvar</button>
    </div>
  </form>
</div>
@endsection
