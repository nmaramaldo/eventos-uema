// resources/js/app.js
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

// -----------------------------------------------------------
// Autoabrir modal quando houver erro de inscrição (server-side)
// -----------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
  const hasErrorBanner = document.querySelector('.alert-danger');
  const modalEl = document.getElementById('inscricaoBloqueadaModal');

  // suporta tanto window.bootstrap quanto global "bootstrap"
  const ModalCtor =
    (typeof window !== 'undefined' && window.bootstrap && window.bootstrap.Modal)
      ? window.bootstrap.Modal
      : (typeof bootstrap !== 'undefined' ? bootstrap.Modal : null);

  if (hasErrorBanner && modalEl && ModalCtor) {
    const modal = new ModalCtor(modalEl);
    modal.show();
  }
});

// -----------------------------------------------------------
// CPF: máscara + validação em tempo real nos inputs [data-cpf]
// -----------------------------------------------------------
function onlyDigits(v) {
  return (v || '').replace(/\D/g, '');
}

function maskCpf(v) {
  const d = onlyDigits(v).slice(0, 11);
  let out = '';
  if (d.length <= 3) out = d;
  else if (d.length <= 6) out = d.replace(/(\d{3})(\d{1,3})/, '$1.$2');
  else if (d.length <= 9) out = d.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
  else out = d.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
  return out;
}

function isValidCpf(value) {
  const cpf = onlyDigits(value);
  if (!cpf || cpf.length !== 11) return false;
  if (/^(\d)\1{10}$/.test(cpf)) return false;

  const calc = (factor) => {
    let sum = 0;
    for (let i = 0; i < factor - 1; i++) {
      sum += parseInt(cpf.charAt(i), 10) * (factor - i);
    }
    const digit = (sum * 10) % 11;
    return digit === 10 ? 0 : digit;
  };

  const d1 = calc(10);
  const d2 = calc(11);
  return d1 === parseInt(cpf.charAt(9), 10) && d2 === parseInt(cpf.charAt(10), 10);
}

function hookCpfInputs() {
  const inputs = document.querySelectorAll('input[data-cpf]');
  inputs.forEach((input) => {
    const feedback = document.getElementById('cpf-feedback-js');

    input.addEventListener('input', () => {
      const start = input.selectionStart || 0;
      input.value = maskCpf(input.value);
      try {
        input.setSelectionRange(start, start);
      } catch (_) {}

      const ok = !onlyDigits(input.value) || isValidCpf(input.value);
      if (ok) {
        input.classList.remove('is-invalid');
        if (feedback) feedback.classList.add('d-none');
      } else {
        input.classList.add('is-invalid');
        if (feedback) feedback.classList.remove('d-none');
      }
    });

    input.addEventListener('blur', () => {
      const hasValue = onlyDigits(input.value).length > 0;
      const ok = !hasValue || isValidCpf(input.value);
      if (ok) {
        input.classList.remove('is-invalid');
        if (feedback) feedback.classList.add('d-none');
      } else {
        input.classList.add('is-invalid');
        if (feedback) feedback.classList.remove('d-none');
      }
    });
  });

  // impede submit se houver CPF inválido
  document.querySelectorAll('form').forEach((form) => {
    form.addEventListener('submit', (ev) => {
      const target = form.querySelector('input[data-cpf]');
      if (target) {
        const digits = onlyDigits(target.value);
        const hasValue = digits.length > 0;
        const ok = !hasValue || isValidCpf(target.value);
        if (!ok) {
          ev.preventDefault();
          target.classList.add('is-invalid');
          const feedback = document.getElementById('cpf-feedback-js');
          if (feedback) feedback.classList.remove('d-none');
          target.focus();
        } else {
          // opcional: envia só os dígitos
          target.value = digits;
        }
      }
    });
  });
}

document.addEventListener('DOMContentLoaded', hookCpfInputs);
