{{-- resources/views/profile/update-profile-information-form.blade.php --}}
<section>
    <header>
        <h2 class="h5">
            Informações do perfil
        </h2>

        <p class="text-muted mb-0">
            Atualize o nome, e-mail e CPF da sua conta.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-4">
        @csrf
        @method('patch')

        {{-- Nome --}}
        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input id="name" name="name" type="text"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $user->name) }}" required autocomplete="name">
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- E-mail --}}
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" name="email" type="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- CPF (com máscara e validação em tempo real) --}}
        <div class="mb-3">
            <label for="cpf" class="form-label">CPF</label>
            <input id="cpf"
                   name="cpf"
                   type="text"
                   inputmode="numeric"
                   maxlength="14"
                   class="form-control @error('cpf') is-invalid @enderror"
                   value="{{ old('cpf', $user->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $user->cpf) : '') }}"
                   placeholder="000.000.000-00"
                   data-cpf>
            <div class="form-text">Digite apenas números; a máscara será aplicada automaticamente.</div>
            @error('cpf') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <div class="invalid-feedback d-none" id="cpf-feedback-js">CPF inválido.</div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</section>
