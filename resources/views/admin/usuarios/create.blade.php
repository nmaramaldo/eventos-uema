@extends('layouts.new-event')
@section('title','Novo usuário')

@section('content')
<div class="container" style="padding:60px 0; max-width:720px">
  <h3>Novo usuário</h3>

  <form method="post" action="{{ route('admin.usuarios.store') }}">
    @csrf

    <div class="form-group">
      <label>Nome</label>
      <input name="name" class="form-control" value="{{ old('name') }}" required>
      @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
      <label>E-mail</label>
      <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
      @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
      <label>Senha</label>
      <input type="password" name="password" class="form-control" required>
      @error('password') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="form-group">
      <label>Confirmar senha</label>
      <input type="password" name="password_confirmation" class="form-control" required>
    </div>

    <div class="form-group">
      <label>Tipo</label>
      <select name="tipo_usuario" class="form-control">
        <option value="comum"  @selected(old('tipo_usuario')==='comum')>Comum</option>
        <option value="admin"  @selected(old('tipo_usuario')==='admin')>Administrador</option>
        <option value="master" @selected(old('tipo_usuario')==='master')>Master</option>
      </select>
      @error('tipo_usuario') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="checkbox">
      <label><input type="checkbox" name="ativo" value="1" {{ old('ativo',1) ? 'checked' : '' }}> Ativo</label>
    </div>

    <button class="btn btn-primary">Salvar</button>
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-default">Voltar</a>
  </form>
</div>
@endsection
