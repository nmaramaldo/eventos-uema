@extends('layouts.app') {{-- padroniza o menu --}}

@section('title', 'Usuários')
@section('content')
<div class="container" style="padding:60px 0">
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

  <div class="row">
    <div class="col-md-8"><h2>Usuários</h2></div>
    <div class="col-md-4 text-end">
      <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">Novo usuário</a>
    </div>
  </div>

  <table class="table table-striped" style="margin-top:20px">
    <thead>
      <tr>
        <th>Nome</th><th>E-mail</th><th>Tipo</th><th>Status</th><th style="width:220px">Ações</th>
      </tr>
    </thead>
    <tbody>
      @foreach($usuarios as $u)
      <tr>
        <td>{{ $u->name }}</td>
        <td>{{ $u->email }}</td>
        <td>{{ $u->tipo_usuario instanceof \BackedEnum ? $u->tipo_usuario->value : $u->tipo_usuario }}</td>
        <td>{!! $u->ativo ? '<span class="label label-success">Ativo</span>' : '<span class="label label-default">Inativo</span>' !!}</td>
        <td>
          <a class="btn btn-xs btn-default" href="{{ route('admin.usuarios.edit',$u) }}">Editar</a>

          @if($u->ativo)
            <form method="post" action="{{ route('admin.usuarios.desativar',$u) }}" style="display:inline">
              @csrf @method('PATCH')
              <button class="btn btn-xs btn-warning">Desativar</button>
            </form>
          @else
            <form method="post" action="{{ route('admin.usuarios.ativar',$u) }}" style="display:inline">
              @csrf @method('PATCH')
              <button class="btn btn-xs btn-success">Ativar</button>
            </form>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{ $usuarios->links() }}
</div>
@endsection
