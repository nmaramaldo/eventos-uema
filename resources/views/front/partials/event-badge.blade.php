@php
    // Aceita $e OU $evento (usa o que existir)
    $ev = $e ?? $evento ?? null;

    $vagas     = $ev?->vagasDisponiveis();
    $semVagas  = !is_null($vagas) && $vagas <= 0;
    $encerrado = $ev && method_exists($ev, 'isEncerrado') && $ev->isEncerrado();
@endphp

@if(!$ev)
  <span class="badge bg-secondary">—</span>
@elseif($encerrado)
  <span class="badge bg-dark">Encerrado</span>
@elseif($ev->inscricoesAbertas() && !$semVagas)
  <span class="badge bg-success">Inscrições abertas</span>
@elseif($semVagas)
  <span class="badge bg-danger">Vagas encerradas</span>
@else
  <span class="badge bg-secondary">Inscrições fechadas</span>
@endif
