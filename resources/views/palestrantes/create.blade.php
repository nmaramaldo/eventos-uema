@extends('layouts.app')
@section('title', 'Adicionar Palestrante - ' . $evento->nome)

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h3>Adicionar Palestrante ao Evento: {{ $evento->nome }}</h3>
                    </div>
                    <div class="card-body">
                        {{-- Mensagens de validação --}}
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

                        <form action="{{ route('eventos.palestrantes.store', $evento) }}" method="POST">
                            @csrf

                            {{-- Adicionar Palestrante --}}
                            <div class="border p-3 rounded mb-4 bg-light">
                                <h5 class="mb-3">Adicionar Palestrante à Lista</h5>
                                <div class="row align-items-end">
                                    <div class="col-md-6 mb-2">
                                        <label for="new_speaker_name" class="form-label">Nome *</label>
                                        <input type="text" id="new_speaker_name" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="new_speaker_email" class="form-label">E-mail (opcional)</label>
                                        <input type="email" id="new_speaker_email" class="form-control">
                                    </div>
                                    <div class="col-md-12 mb-2 mt-2">
                                        <label for="new_speaker_bio" class="form-label">Biografia (opcional)</label>
                                        <textarea id="new_speaker_bio" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                                <button type="button" id="add_speaker_btn" class="btn btn-secondary mt-2">Adicionar à
                                    Lista</button>
                            </div>

                            {{-- Lista de Palestrantes --}}
                            <h5>Palestrantes Adicionados</h5>
                            <div id="speakers-container">
                                @if (old('palestrantes'))
                                    @foreach (old('palestrantes') as $i => $p)
                                        <div class="speaker-row d-flex align-items-center gap-3 border-top pt-2 mt-2">
                                            <input type="hidden" name="palestrantes[{{ $i }}][nome]"
                                                value="{{ $p['nome'] }}">
                                            <input type="hidden" name="palestrantes[{{ $i }}][email]"
                                                value="{{ $p['email'] }}">
                                            <input type="hidden" name="palestrantes[{{ $i }}][biografia]"
                                                value="{{ $p['biografia'] }}">
                                            <div class="flex-grow-1">
                                                <strong class="speaker-name">{{ $p['nome'] }}</strong>
                                                <p class="text-muted mb-0 speaker-email">{{ $p['email'] }}</p>
                                            </div>
                                            <button type="button"
                                                class="btn btn-danger btn-sm remove-speaker-btn">Remover</button>
                                        </div>
                                    @endforeach
                                @else
                                    <p id="no-speakers-text" class="text-muted">Nenhum palestrante adicionado ainda.</p>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('eventos.palestrantes.index', $evento) }}"
                                    class="btn btn-outline-secondary">Voltar</a>
                                <button type="submit" class="btn btn-success">Salvar Palestrantes</button>
                            </div>
                        </form>

                        {{-- Template JS --}}
                        <template id="speaker-template">
                            <div class="speaker-row d-flex align-items-center gap-3 border-top pt-2 mt-2">
                                <input type="hidden" name="palestrantes[INDEX][nome]">
                                <input type="hidden" name="palestrantes[INDEX][email]">
                                <input type="hidden" name="palestrantes[INDEX][biografia]">
                                <div class="flex-grow-1">
                                    <strong class="speaker-name"></strong>
                                    <p class="text-muted mb-0 speaker-email"></p>
                                </div>
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
            document.addEventListener('DOMContentLoaded', function() {
                const addBtn = document.getElementById('add_speaker_btn');
                const container = document.getElementById('speakers-container');
                const template = document.getElementById('speaker-template');
                const noSpeakersText = document.getElementById('no-speakers-text');
                let speakerIndex = 0;

                const checkEmptyState = () => {
                    noSpeakersText.style.display = container.children.length === 0 ? 'block' : 'none';
                };

                addBtn.addEventListener('click', function() {
                    const nameInput = document.getElementById('new_speaker_name');
                    const emailInput = document.getElementById('new_speaker_email');
                    const bioInput = document.getElementById('new_speaker_bio');

                    if (!nameInput.value.trim()) {
                        alert('O nome do palestrante é obrigatório.');
                        return;
                    }

                    // Clona o template
                    const clone = template.content.cloneNode(true);
                    const newRow = clone.querySelector('.speaker-row');

                    // Atualiza os nomes dos inputs com o índice
                    newRow.querySelector('[name="palestrantes[INDEX][nome]"]').name =
                        `palestrantes[${speakerIndex}][nome]`;
                    newRow.querySelector('[name="palestrantes[INDEX][email]"]').name =
                        `palestrantes[${speakerIndex}][email]`;
                    newRow.querySelector('[name="palestrantes[INDEX][biografia]"]').name =
                        `palestrantes[${speakerIndex}][biografia]`;

                    // Seta os valores somente se não estiverem vazios
                    newRow.querySelector(`[name="palestrantes[${speakerIndex}][nome]"]`).value = nameInput.value
                        .trim();
                    if (emailInput.value.trim()) {
                        newRow.querySelector(`[name="palestrantes[${speakerIndex}][email]"]`).value = emailInput
                            .value.trim();
                    }
                    if (bioInput.value.trim()) {
                        newRow.querySelector(`[name="palestrantes[${speakerIndex}][biografia]"]`).value =
                            bioInput.value.trim();
                    }

                    // Atualiza a visualização na tela
                    newRow.querySelector('.speaker-name').textContent = nameInput.value.trim();
                    newRow.querySelector('.speaker-email').textContent = emailInput.value.trim();

                    container.appendChild(newRow);

                    // Limpa os campos de input
                    nameInput.value = '';
                    emailInput.value = '';
                    bioInput.value = '';

                    speakerIndex++;
                    checkEmptyState();
                });

                // Remover palestrante
                container.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-speaker-btn')) {
                        e.target.closest('.speaker-row').remove();
                        checkEmptyState();
                    }
                });

                checkEmptyState();
            });
        </script>
    @endpush
@endsection
