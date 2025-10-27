@extends('layouts.app')
@section('title', 'Adicionar Palestrante - ' . ($evento->nome ?? 'Evento'))

@section('content')
@php
    // 1) Garante que temos $evento (Model ou ID) e sempre extrai um ID
    $evento = $evento ?? request()->route('evento');              // pode ser Model ou string
    $eventoId = is_object($evento) ? ($evento->getKey() ?? $evento->id ?? null) : $evento;

    // 2) Se por algum motivo ainda não tiver, evita quebrar e deixa visível o problema
    if (!$eventoId) { $eventoId = old('evento_id'); }

    // 3) Action totalmente robusta (sem route()) para não estourar Missing parameter
    $storeUrl  = url('app/eventos/'.$eventoId.'/palestrantes');           // POST
    $indexUrl  = url('app/eventos/'.$eventoId.'/palestrantes');           // GET (lista)
@endphp

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-10">
      <div class="card shadow-sm">
        <div class="card-header">
          <h3>Adicionar Palestrante ao Evento: {{ $evento->nome ?? $eventoId }}</h3>
        </div>

        <div class="card-body">
          @if ($errors->any())
            <div class="alert alert-danger">
              <div class="fw-semibold mb-2">Ops! Corrija os erros abaixo:</div>
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          {{-- mantenho um hidden para o backend se precisar --}}
          <form action="{{ $storeUrl }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="evento_id" value="{{ $eventoId }}">

            <div class="border p-3 rounded mb-4 bg-light">
              <h5 class="mb-3">Adicionar Palestrante à Lista</h5>

              <div class="row align-items-end">
                <div class="col-md-6 mb-2">
                  <label class="form-label">Nome *</label>
                  <input type="text" id="new_speaker_name" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                  <label class="form-label">E-mail (opcional)</label>
                  <input type="email" id="new_speaker_email" class="form-control">
                </div>
                <div class="col-md-12 mb-2 mt-2">
                  <label class="form-label">Biografia (opcional)</label>
                  <textarea id="new_speaker_bio" class="form-control" rows="2"></textarea>
                </div>

                <div class="col-md-6 mb-2">
                  <label class="form-label">Foto (opcional)</label>
                  <div id="new_speaker_foto_wrapper">
                    <input type="file" id="new_speaker_foto" class="form-control" accept="image/*">
                  </div>
                  <small class="text-muted">Máx. 2MB</small>
                </div>

                <div class="col-md-6 mb-2">
                  <label class="form-label">Atividades (opcional)</label>
                  <select id="new_speaker_atividades" class="form-select" multiple>
                    @foreach(($evento->programacao ?? $evento->programacao()->ordenado()->get() ?? []) as $at)
                      <option value="{{ $at->id }}">{{ $at->titulo }}</option>
                    @endforeach
                  </select>
                  <small class="text-muted">Segure Ctrl (Windows) ou ⌘ (Mac) para múltiplas seleções.</small>
                </div>
              </div>

              <button type="button" id="add_speaker_btn" class="btn btn-secondary mt-2">
                Adicionar à Lista
              </button>
            </div>

            <h5>Palestrantes Adicionados</h5>
            <div id="speakers-container">
              <p id="no-speakers-text" class="text-muted">Nenhum palestrante adicionado ainda.</p>
            </div>

            <div class="d-flex justify-content-between mt-4">
              <a href="{{ $indexUrl }}" class="btn btn-outline-secondary">Voltar</a>
              <button type="submit" class="btn btn-success">Salvar Palestrantes</button>
            </div>
          </form>

          <template id="speaker-template">
            <div class="speaker-row d-flex flex-wrap align-items-start gap-3 border-top pt-2 mt-2">
              <input type="hidden" name="palestrantes[INDEX][nome]">
              <input type="hidden" name="palestrantes[INDEX][email]">
              <input type="hidden" name="palestrantes[INDEX][biografia]">
              <div class="flex-grow-1">
                <strong class="speaker-name"></strong>
                <div class="text-muted small speaker-email"></div>
                <div class="text-muted small speaker-atividades"></div>
                <div class="text-muted small speaker-filename"></div>
              </div>
              <button type="button" class="btn btn-danger btn-sm remove-speaker-btn">Remover</button>
            </div>
          </template>

        </div>
      </div>
    </div>
  </div>
</div>

{{-- Script inline para não depender de @stack --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const addBtn = document.getElementById('add_speaker_btn');
  const cont   = document.getElementById('speakers-container');
  const tpl    = document.getElementById('speaker-template');
  const empty  = document.getElementById('no-speakers-text');

  const iNome  = document.getElementById('new_speaker_name');
  const iEmail = document.getElementById('new_speaker_email');
  const iBio   = document.getElementById('new_speaker_bio');
  const fotoWrapper = document.getElementById('new_speaker_foto_wrapper');
  let   iFoto  = document.getElementById('new_speaker_foto');
  const iAtv   = document.getElementById('new_speaker_atividades');

  if (!addBtn || !cont || !tpl) return;

  let idx = 0;

  function toggleEmpty() {
    empty.style.display = cont.querySelector('.speaker-row') ? 'none' : 'block';
  }

  addBtn.addEventListener('click', function () {
    try {
      const nome  = (iNome?.value || '').trim();
      const email = (iEmail?.value || '').trim();
      const bio   = (iBio?.value || '').trim();
      if (!nome) { alert('O nome do palestrante é obrigatório.'); iNome.focus(); return; }

      const atvs = iAtv
        ? Array.from(iAtv.selectedOptions).map(o => ({id:o.value, titulo:o.text}))
        : [];

      const frag = tpl.content.cloneNode(true);
      const el   = frag.querySelector('.speaker-row');
      if (!el) return;

      el.querySelector('[name="palestrantes[INDEX][nome]"]').name      = `palestrantes[${idx}][nome]`;
      el.querySelector('[name="palestrantes[INDEX][email]"]').name     = `palestrantes[${idx}][email]`;
      el.querySelector('[name="palestrantes[INDEX][biografia]"]').name = `palestrantes[${idx}][biografia]`;

      el.querySelector(`[name="palestrantes[${idx}][nome]"]`).value      = nome;
      el.querySelector(`[name="palestrantes[${idx}][email]"]`).value     = email;
      el.querySelector(`[name="palestrantes[${idx}][biografia]"]`).value = bio;

      if (atvs.length) {
        const holder = document.createElement('div');
        atvs.forEach(a => {
          const h = document.createElement('input');
          h.type = 'hidden';
          h.name = `palestrantes[${idx}][atividades][]`;
          h.value = a.id;
          holder.appendChild(h);
        });
        el.appendChild(holder);
        el.querySelector('.speaker-atividades').textContent =
          'Atividades: ' + atvs.map(a=>a.titulo).join(', ');
      }

      if (iFoto && iFoto.files && iFoto.files.length > 0) {
        iFoto.name = `palestrantes[${idx}][foto]`;
        el.appendChild(iFoto);
        const nameSpan = el.querySelector('.speaker-filename');
        if (nameSpan) nameSpan.textContent = 'Foto: ' + iFoto.files[0].name;

        const novo = document.createElement('input');
        novo.type = 'file';
        novo.id   = 'new_speaker_foto';
        novo.className = 'form-control';
        novo.accept = 'image/*';
        fotoWrapper.replaceChildren(novo);
        iFoto = novo;
      }

      el.querySelector('.speaker-name').textContent  = nome;
      el.querySelector('.speaker-email').textContent = email;

      cont.appendChild(el);
      idx++;

      if (iNome)  iNome.value = '';
      if (iEmail) iEmail.value = '';
      if (iBio)   iBio.value = '';
      if (iAtv)   Array.from(iAtv.options).forEach(o => o.selected = false);

      toggleEmpty();
    } catch (err) {
      console.error(err);
      alert('Erro ao adicionar palestrante. Verifique o console (F12).');
    }
  });

  cont.addEventListener('click', e => {
    if (e.target.classList.contains('remove-speaker-btn')) {
      e.target.closest('.speaker-row').remove();
      toggleEmpty();
    }
  });

  toggleEmpty();
});
</script>
@endsection
