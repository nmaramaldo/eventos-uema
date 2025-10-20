@extends('layouts.app')
@section('title', 'Nova inscrição')

@section('content')
<div class="container py-5">
  @if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
  @endif

  <div class="card shadow-sm">
    <div class="card-header d-flex align-items-center">
      <h2 class="mb-0">Fazer inscrição</h2>
      <a href="{{ route('inscricoes.index') }}" class="btn btn-outline-secondary ms-auto">Minhas inscrições</a>
    </div>

    <div class="card-body">
      <form method="POST" action="{{ route('inscricoes.store') }}" novalidate id="formInscricao">
        @csrf

        {{-- Evento --}}
        <div class="mb-3">
          <label class="form-label">Evento *</label>
          <select name="evento_id" class="form-select @error('evento_id') is-invalid @enderror" required>
            <option value="">Selecione...</option>
            @foreach($eventos as $ev)
              <option value="{{ $ev->id }}" @selected(old('evento_id')===$ev->id)>{{ $ev->nome }}</option>
            @endforeach
          </select>
          @error('evento_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- CPF (validação no campo, antes de salvar) --}}
        <div class="mb-3">
          <label for="cpf" class="form-label">CPF (somente números) *</label>
          <input type="text"
                 id="cpf"
                 name="cpf"
                 inputmode="numeric"
                 maxlength="14"
                 class="form-control"
                 placeholder="000.000.000-00"
                 required>
          <div id="cpfHelp" class="form-text">Validaremos o CPF automaticamente.</div>
          <div class="invalid-feedback" id="cpfError">CPF inválido.</div>
        </div>

        <div class="d-flex gap-2">
          <button class="btn btn-primary" id="btnEnviar" disabled>Confirmar inscrição</button>
          <a href="{{ route('front.eventos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // máscara simples
  const maskCPF = (v) => {
    v = v.replace(/\D/g, '');
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d)/, '$1.$2');
    v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    return v;
  };

  // algoritmo de validação de CPF
  function isValidCPF(raw) {
    const cpf = (raw || '').replace(/\D/g, '');
    if (!cpf || cpf.length !== 11) return false;
    if (/^(\d)\1+$/.test(cpf)) return false;

    let soma = 0, resto;
    for (let i = 1; i <= 9; i++) soma += parseInt(cpf.substring(i-1, i)) * (11 - i);
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.substring(9, 10))) return false;

    soma = 0;
    for (let i = 1; i <= 10; i++) soma += parseInt(cpf.substring(i-1, i)) * (12 - i);
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    return resto === parseInt(cpf.substring(10, 11));
  }

  const $cpf = document.getElementById('cpf');
  const $btn = document.getElementById('btnEnviar');
  const $err = document.getElementById('cpfError');

  $cpf.addEventListener('input', (e) => {
    e.target.value = maskCPF(e.target.value);
    const ok = isValidCPF(e.target.value);
    if (ok) {
      $cpf.classList.remove('is-invalid');
      $cpf.classList.add('is-valid');
      $err.style.display = 'none';
    } else {
      $cpf.classList.remove('is-valid');
      $cpf.classList.add('is-invalid');
      $err.style.display = 'block';
    }
    $btn.disabled = !ok || !document.querySelector('select[name="evento_id"]').value;
  });

  // habilita botão quando escolher evento
  document.querySelector('select[name="evento_id"]').addEventListener('change', () => {
    $btn.disabled = !isValidCPF($cpf.value) || !document.querySelector('select[name="evento_id"]').value;
  });
</script>
@endpush
@endsection
