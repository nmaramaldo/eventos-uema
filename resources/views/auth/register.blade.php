{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.guest')
@section('title', 'Criar conta')

@section('content')
    <div class="text-center mb-4">
        <img src="{{ asset('new-event/images/uema-logo.png') }}" alt="UEMA" height="48" class="mb-2">
        <h3 class="mb-1">Criar conta</h3>
        <div class="text-muted">Preencha os dados abaixo para se registrar</div>
    </div>

    {{-- Mensagens de erro --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Ops! Verifique os campos abaixo:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus class="form-control">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="form-control">
        </div>

        <div class="mb-3">
            <label for="cpf" class="form-label">CPF</label>
            <input id="cpf" type="text" name="cpf" class="form-control @error('cpf') is-invalid @enderror" value="{{ old('cpf') }}" required placeholder="000.000.000-00">
            <div id="cpf-feedback" class="invalid-feedback"></div>
            @error('cpf')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input id="password" name="password" type="password" required class="form-control">
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirmar senha</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100">Registrar</button>
    </form>

    <div class="text-center mt-3">
        <small class="text-muted">
            Já tem conta?
            <a href="{{ route('login') }}">Entre aqui</a>
        </small>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cpfInput = document.getElementById('cpf');
    const cpfFeedback = document.getElementById('cpf-feedback');

    // Função para validar o CPF (algoritmo padrão)
    function validarCPF(cpf) {
        cpf = cpf.replace(/[^\d]+/g,'');
        if(cpf == '') return false;
        if (cpf.length != 11 || /^(\d)\1+$/.test(cpf)) return false;
        let add = 0;
        for (let i=0; i < 9; i++) add += parseInt(cpf.charAt(i)) * (10 - i);
        let rev = 11 - (add % 11);
        if (rev == 10 || rev == 11) rev = 0;
        if (rev != parseInt(cpf.charAt(9))) return false;
        add = 0;
        for (let i = 0; i < 10; i++) add += parseInt(cpf.charAt(i)) * (11 - i);
        rev = 11 - (add % 11);
        if (rev == 10 || rev == 11) rev = 0;
        if (rev != parseInt(cpf.charAt(10))) return false;
        return true;
    }

    // Evento que acontece enquanto o usuário digita
    cpfInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.substring(0, 11); // Limita a 11 dígitos
        
        let maskedValue = value;
        if (value.length > 3) maskedValue = value.substring(0,3) + '.' + value.substring(3);
        if (value.length > 6) maskedValue = maskedValue.substring(0,7) + '.' + maskedValue.substring(7);
        if (value.length > 9) maskedValue = maskedValue.substring(0,11) + '-' + maskedValue.substring(11);
        
        e.target.value = maskedValue;
    });

    // Evento que acontece quando o usuário sai do campo
    cpfInput.addEventListener('blur', function (e) {
        const cpf = e.target.value;
        if (cpf.length === 14 && validarCPF(cpf)) {
            cpfInput.classList.remove('is-invalid');
            cpfInput.classList.add('is-valid');
            cpfFeedback.textContent = '';
        } else if (cpf.length > 0) {
            cpfInput.classList.remove('is-valid');
            cpfInput.classList.add('is-invalid');
            cpfFeedback.textContent = 'CPF inválido.';
        } else {
            cpfInput.classList.remove('is-valid', 'is-invalid');
            cpfFeedback.textContent = '';
        }
    });
});
</script>
@endpush