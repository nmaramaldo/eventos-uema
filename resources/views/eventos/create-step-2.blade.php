@extends('layouts.app')
@section('title', 'Criar Evento - Passo 2: Programação')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3>Novo Evento (Passo 2 de 3): Programação</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('eventos.create.store.step2') }}" method="POST">
                        @csrf
                        
                        {{-- Formulário para adicionar nova atividade --}}
                        <div class="border p-3 rounded mb-4 bg-light">
                            <h5 class="mb-3">Adicionar Atividade</h5>
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label for="new_titulo" class="form-label">Título *</label>
                                    <input type="text" id="new_titulo" class="form-control">
                                </div>
                                <div class="col-12 mb-2">
                                    <label for="new_descricao" class="form-label">Descrição (opcional)</label>
                                    <textarea id="new_descricao" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="new_modalidade" class="form-label">Modalidade *</label>
                                    <input type="text" id="new_modalidade" class="form-control" placeholder="Ex: Palestra, Workshop">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="new_localidade" class="form-label">Local *</label>
                                    <input type="text" id="new_localidade" class="form-control" placeholder="Ex: Auditório Central">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="new_capacidade" class="form-label">Vagas (opcional)</label>
                                    <input type="number" id="new_capacidade" class="form-control" min="0">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="new_data_hora_inicio" class="form-label">Início *</label>
                                    <input type="datetime-local" id="new_data_hora_inicio" class="form-control">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="new_data_hora_fim" class="form-label">Fim *</label>
                                    <input type="datetime-local" id="new_data_hora_fim" class="form-control">
                                </div>
                                <div class="col-md-4 d-flex align-items-center mt-3">
                                    <div class="form-check">
                                        <input type="checkbox" id="new_requer_inscricao" class="form-check-input">
                                        <label for="new_requer_inscricao" class="form-check-label">Requer Inscrição?</label>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add_activity_btn" class="btn btn-secondary mt-3">Adicionar à Lista</button>
                        </div>
                        
                        {{-- Lista de atividades adicionadas --}}
                        <h5 class="mt-4">Programação Adicionada</h5>
                        <div id="atividades-container">
                            <p id="no-activities-text" class="text-muted">Nenhuma atividade adicionada ainda.</p>
                            {{-- Repopula com dados da sessão, se houver --}}
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('eventos.create.step1') }}" class="btn btn-outline-secondary">Voltar (Info Gerais)</a>
                            <button type="submit" class="btn btn-primary">Próximo: Palestrantes</button>
                        </div>
                    </form>

                    {{-- Template invisível para clonar novas linhas --}}
                    <template id="activity-template">
                        <div class="activity-row border-top pt-2 mt-2">
                            <input type="hidden" name="atividades[INDEX][titulo]">
                            <input type="hidden" name="atividades[INDEX][descricao]">
                            <input type="hidden" name="atividades[INDEX][modalidade]">
                            <input type="hidden" name="atividades[INDEX][data_hora_inicio]">
                            <input type="hidden" name="atividades[INDEX][data_hora_fim]">
                            <input type="hidden" name="atividades[INDEX][localidade]">
                            <input type="hidden" name="atividades[INDEX][capacidade]">
                            <input type="hidden" name="atividades[INDEX][requer_inscricao]">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <strong class="activity-title"></strong>
                                    <p class="text-muted mb-1 activity-description" style="font-size: 0.9em;"></p>
                                    <small class="d-block text-muted">
                                        <span class="activity-time"></span> | 
                                        <span class="activity-location"></span> | 
                                        <span class="activity-modality"></span>
                                    </small>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-activity-btn">Remover</button>
                            </div>
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
    const addBtn = document.getElementById('add_activity_btn');
    const container = document.getElementById('atividades-container');
    const template = document.getElementById('activity-template');
    const noActivitiesText = document.getElementById('no-activities-text');
    let activityIndex = 0;

    const checkEmptyState = () => {
        noActivitiesText.style.display = container.children.length > 1 ? 'none' : 'block';
    };

    const addActivity = (activityData) => {
        const clone = template.content.cloneNode(true);
        const newRow = clone.querySelector('.activity-row');

        // Preenche os inputs hidden
        for (const key in activityData) {
            const input = newRow.querySelector(`[name="atividades[INDEX][${key}]"]`);
            if (input) {
                input.name = `atividades[${activityIndex}][${key}]`;
                input.value = activityData[key];
            }
        }
        
        // Preenche o texto visível
        newRow.querySelector('.activity-title').textContent = activityData.titulo;
        newRow.querySelector('.activity-description').textContent = activityData.descricao;
        newRow.querySelector('.activity-modality').textContent = `Modalidade: ${activityData.modalidade}`;
        newRow.querySelector('.activity-location').textContent = `Local: ${activityData.localidade}`;
        const startTime = new Date(activityData.data_hora_inicio).toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' });
        const endTime = new Date(activityData.data_hora_fim).toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' });
        newRow.querySelector('.activity-time').textContent = `Período: ${startTime} - ${endTime}`;
        
        container.appendChild(newRow);
        activityIndex++;
        checkEmptyState();
    };

    // Repopula o formulário com dados da sessão, se existirem
    const initialActivities = @json($eventData['atividades'] ?? []);
    initialActivities.forEach(addActivity);

    addBtn.addEventListener('click', function () {
        const newActivityData = {
            titulo: document.getElementById('new_titulo').value,
            descricao: document.getElementById('new_descricao').value,
            modalidade: document.getElementById('new_modalidade').value,
            data_hora_inicio: document.getElementById('new_data_hora_inicio').value,
            data_hora_fim: document.getElementById('new_data_hora_fim').value,
            localidade: document.getElementById('new_localidade').value,
            capacidade: document.getElementById('new_capacidade').value,
            requer_inscricao: document.getElementById('new_requer_inscricao').checked ? 1 : 0,
        };

        if (!newActivityData.titulo || !newActivityData.data_hora_inicio || !newActivityData.data_hora_fim || !newActivityData.modalidade || !newActivityData.localidade) {
            alert('Por favor, preencha todos os campos obrigatórios (*).');
            return;
        }

        addActivity(newActivityData);

        // Limpa os campos
        document.getElementById('new_titulo').value = '';
        document.getElementById('new_descricao').value = '';
        document.getElementById('new_modalidade').value = '';
        document.getElementById('new_data_hora_inicio').value = '';
        document.getElementById('new_data_hora_fim').value = '';
        document.getElementById('new_localidade').value = '';
        document.getElementById('new_capacidade').value = '';
        document.getElementById('new_requer_inscricao').checked = false;
    });

    container.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-activity-btn')) {
            e.target.closest('.activity-row').remove();
            checkEmptyState();
        }
    });

    checkEmptyState();
});
</script>
@endpush
@endsection