@extends('layouts.app')
@section('title', 'Meu Perfil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <h2 class="mb-1">Meu Perfil</h2>
            <p class="text-muted mb-4">
                Visualize e atualize seus dados cadastrais.
            </p>

            {{-- ALERTAS --}}
            @if (session('status') === 'profile-updated')
                <div class="alert alert-success">
                    Dados atualizados com sucesso.
                </div>
            @endif

            {{-- CARD: DADOS ATUAIS --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <strong>Dados cadastrados</strong>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Nome</dt>
                        <dd class="col-sm-9">{{ auth()->user()->name }}</dd>

                        <dt class="col-sm-3">E-mail</dt>
                        <dd class="col-sm-9">{{ auth()->user()->email }}</dd>

                        {{-- Se tiver outros campos no modelo User, pode exibir aqui também --}}
                    </dl>
                </div>
            </div>

            {{-- CARD: FORMULÁRIO DE EDIÇÃO --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <strong>Alterar dados</strong>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label class="form-label" for="name">Nome</label>
                            <input id="name"
                                   type="text"
                                   name="name"
                                   value="{{ old('name', auth()->user()->name) }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   required
                                   autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="email">E-mail</label>
                            <input id="email"
                                   type="email"
                                   name="email"
                                   value="{{ old('email', auth()->user()->email) }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Se quiser, aqui dá pra incluir campos extras (telefone, CPF, etc.) --}}

                        <div class="text-end">
                            <button class="btn btn-primary">
                                Salvar alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
