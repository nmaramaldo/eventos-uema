@extends('layouts.new-event')
@section('title','Editar usuário')

@section('content')
<div class="container" style="padding:60px 0; max-width:720px">
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

  <h3>Editar usuário</h3>

  <form method="post" action="{{ route('admin.usuarios.update', $usuario) }}">
    @csrf @method('PUT')

    <div class="form-group">
      <label>Nome</label>
      <input name="name" class="form-control" value="{{ old('name',$usuario->name) }}" required>
      @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
      <label>E-mail</label>
      <input type="email" name="email" class="form-control" value="{{ old('email',$usuario->email) }}" required>
      @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
      <label>Tipo</label>
      @php
        $tipoAtual = $usuario->tipo_usuario instanceof \BackedEnum ? $usuario->tipo_usuario->value : $usuario->tipo_usuario;
      @endphp
      <select name="tipo_usuario" class="form-control">
        @foreach(['comum'=>'Comum','admin'=>'Administrador','master'=>'Master'] as $k=>$lbl)
          <option value="{{ $k }}" @selected(old('tipo_usuario',$tipoAtual)===$k)>{{ $lbl }}</option>
        @endforeach
      </select>
      @error('tipo_usuario') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="checkbox">
      <label><input type="checkbox" name="ativo" value="1" {{ old('ativo',$usuario->ativo) ? 'checked' : '' }}> Ativo</label>
    </div>

    <hr>

    <div class="form-group">
      <label>Nova senha (opcional)</label>
      <input type="password" name="password" class="form-control" placeholder="Deixe em branco para manter">
      @error('password') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
      <label>Confirmar nova senha</label>
      <input type="password" name="password_confirmation" class="form-control">
    </div>

    <button class="btn btn-primary">Salvar alterações</button>
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-default">Voltar</a>
  </form>
</div>
@endsection
