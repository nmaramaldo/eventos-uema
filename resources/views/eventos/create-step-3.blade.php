@extends('layouts.app')
@section('title', 'Criar Evento - Passo 3: Palestrantes')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3>Novo Evento (Passo 3 de 3): Palestrantes</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('eventos.create.store.step3') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="border p-3 rounded mb-4 bg-light">
                            <h5 class="mb-3">Adicionar Palestrante</h5>
                            <div class="row align-items-end">
                                <div class="col-md-6 mb-2"><label for="new_speaker_name" class="form-label">Nome *</label><input type="text" id="new_speaker_name" class="form-control"></div>
                                <div class="col-md-6 mb-2"><label for="new_speaker_email" class="form-label">E-mail (opcional)</label><input type="email" id="new_speaker_email" class="form-control"></div>
                                <div class="col-md-12 mb-2 mt-2"><label for="new_speaker_bio" class="form-label">Biografia (opcional)</label><textarea id="new_speaker_bio" class="form-control" rows="2"></textarea></div>
                            </div>
                            <button type="button" id="add_speaker_btn" class="btn btn-secondary mt-2">Adicionar à Lista</button>
                        </div>

                        <h5>Palestrantes Adicionados</h5>
                        <div id="speakers-container">
                            <p id="no-speakers-text" class="text-muted">Nenhum palestrante adicionado ainda.</p>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('eventos.create.step2') }}" class="btn btn-outline-secondary">Voltar (Programação)</a>
                            <button type="submit" class="btn btn-success">Finalizar e Criar Evento</button>
                        </div>
                    </form>
                    <template id="speaker-template">
                        <div class="speaker-row d-flex align-items-center gap-3 border-top pt-2 mt-2">
                            <input type="hidden" name="palestrantes[INDEX][nome]"><input type="hidden" name="palestrantes[INDEX][email]"><input type="hidden" name="palestrantes[INDEX][biografia]">
                            <div class="flex-grow-1"><strong class="speaker-name"></strong><p class="text-muted mb-0 speaker-email"></p></div>
                            <button type="button" class="btn btn-danger btn-sm remove-speaker-btn">Remover</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const addBtn = document.getElementById('add_speaker_btn');
    const container = document.getElementById('speakers-container');
    const template = document.getElementById('speaker-template');
    const noSpeakersText = document.getElementById('no-speakers-text');
    let speakerIndex = 0;

    const checkEmptyState = () => { /* ... (lógica js como antes) ... */ };

    addBtn.addEventListener('click', function () {
        const nameInput = document.getElementById('new_speaker_name');
        const emailInput = document.getElementById('new_speaker_email');
        const bioInput = document.getElementById('new_speaker_bio');

        if (!nameInput.value) { alert('O nome do palestrante é obrigatório.'); return; }

        const clone = template.content.cloneNode(true);
        const newRow = clone.querySelector('.speaker-row');
        
        newRow.querySelector('[name="palestrantes[INDEX][nome]"]').name = `palestrantes[${speakerIndex}][nome]`;
        newRow.querySelector('[name="palestrantes[INDEX][email]"]').name = `palestrantes[${speakerIndex}][email]`;
        newRow.querySelector('[name="palestrantes[INDEX][biografia]"]').name = `palestrantes[${speakerIndex}][biografia]`;

        newRow.querySelector(`[name="palestrantes[${speakerIndex}][nome]"]`).value = nameInput.value;
        newRow.querySelector(`[name="palestrantes[${speakerIndex}][email]"]`).value = emailInput.value;
        newRow.querySelector(`[name="palestrantes[${speakerIndex}][biografia]"]`).value = bioInput.value;

        newRow.querySelector('.speaker-name').textContent = nameInput.value;
        newRow.querySelector('.speaker-email').textContent = emailInput.value;
        
        container.appendChild(newRow);
        
        nameInput.value = ''; emailInput.value = ''; bioInput.value = '';
        speakerIndex++;
        checkEmptyState();
    });

    container.addEventListener('click', function(e) { /* ... (lógica js como antes) ... */ });
    checkEmptyState();
});
</script>
@endpush
@endsection