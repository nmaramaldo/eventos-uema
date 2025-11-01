@extends('layouts.app')
@section('title', 'Nova Atividade - ' . $evento->nome)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Nova Atividade</h3>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="activityForm">
                        @csrf

                        {{-- Formulário para adicionar nova atividade --}}
                        <div class="border p-3 rounded mb-4 bg-light">
                            <h5 class="mb-3">Adicionar Atividade</h5>
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label for="new_titulo" class="form-label">Título *</label>
                                    <input type="text" id="new_titulo" class="form-control" placeholder="Ex: Palestra de Abertura">
                                </div>
                                <div class="col-12 mb-2">
                                    <label for="new_descricao" class="form-label">Descrição (opcional)</label>
                                    <textarea id="new_descricao" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="new_modalidade" class="form-label">Modalidade *</label>
                                    <select id="new_modalidade" class="form-control">
                                        <option value="">Selecione...</option>
                                        <option value="Palestra">Palestra</option>
                                        <option value="Minicurso">Minicurso</option>
                                        <option value="Mesa-redonda">Mesa-redonda</option>
                                        <option value="Workshop">Workshop</option>
                                        <option value="Conferência">Conferência</option>
                                        <option value="Oficina">Oficina</option>
                                        <option value="Outro">Outro</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="new_data_hora_inicio" class="form-label">Início *</label>
                                    <input type="datetime-local" id="new_data_hora_inicio" class="form-control">
                                </div>
                                <div class="col-md-4 mb-2">
                                    <label for="new_data_hora_fim" class="form-label">Fim *</label>
                                    <input type="datetime-local" id="new_data_hora_fim" class="form-control">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="new_localidade" class="form-label">Local *</label>
                                    <input type="text" id="new_localidade" class="form-control" placeholder="Ex: Auditório Central">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label for="new_capacidade" class="form-label">Vagas (opcional)</label>
                                    <input type="number" id="new_capacidade" class="form-control" min="0">
                                </div>
                                <div class="col-md-3 d-flex align-items-center mt-3">
                                    <div class="form-check">
                                        <input type="checkbox" id="new_requer_inscricao" class="form-check-input">
                                        <label for="new_requer_inscricao" class="form-check-label">Requer Inscrição?</label>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add_activity_btn" class="btn btn-secondary mt-3">Adicionar e Salvar</button>
                        </div>

                        {{-- Lista de atividades --}}
                        <h5 class="mt-4">Atividades Adicionadas</h5>
                        <div id="atividades-container">
                            <p id="no-activities-text" class="text-muted">Nenhuma atividade adicionada ainda.</p>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('eventos.programacao.index', $evento) }}" class="btn btn-outline-secondary">Voltar</a>
                            <a href="{{ route('eventos.index') }}" class="btn btn-primary">Finalizar</a>
                        </div>
                    </form>

                    {{-- Template invisível para clonar novas linhas --}}
                    <template id="activity-template">
                        <div class="activity-row border p-3 rounded mb-2 bg-white">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 activity-title"></h6>
                                    <p class="mb-1 text-muted small activity-description"></p>
                                    <div class="small">
                                        <span class="badge bg-primary activity-modality"></span>
                                        <span class="mx-2">|</span>
                                        <strong>Local:</strong> <span class="activity-location"></span>
                                        <span class="mx-2">|</span>
                                        <strong>Início:</strong> <span class="activity-time-start"></span>
                                        <span class="mx-2">|</span>
                                        <strong>Fim:</strong> <span class="activity-time-end"></span>
                                        <span class="activity-capacity-container" style="display: none;">
                                            <span class="mx-2">|</span>
                                            <strong>Vagas:</strong> <span class="activity-capacity"></span>
                                        </span>
                                        <span class="activity-inscricao-container" style="display: none;">
                                            <span class="mx-2">|</span>
                                            <span class="badge bg-warning">Requer Inscrição</span>
                                        </span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger remove-activity-btn ms-2">×</button>
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
    const activityForm = document.getElementById('activityForm');
    const csrfToken = document.querySelector('input[name="_token"]').value;

    const checkEmptyState = () => {
        noActivitiesText.style.display = container.querySelector('.activity-row') ? 'none' : 'block';
    };

    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const addActivityToDOM = (activityData) => {
        const clone = template.content.cloneNode(true);
        const newRow = clone.querySelector('.activity-row');

        newRow.querySelector('.activity-title').textContent = activityData.titulo;
        newRow.querySelector('.activity-description').textContent = activityData.descricao || 'Sem descrição';
        newRow.querySelector('.activity-modality').textContent = activityData.modalidade;
        newRow.querySelector('.activity-location').textContent = activityData.localidade;
        newRow.querySelector('.activity-time-start').textContent = formatDate(activityData.data_hora_inicio);
        newRow.querySelector('.activity-time-end').textContent = formatDate(activityData.data_hora_fim);

        if (activityData.capacidade) {
            newRow.querySelector('.activity-capacity').textContent = activityData.capacidade;
            newRow.querySelector('.activity-capacity-container').style.display = 'inline';
        }

        if (activityData.requer_inscricao) {
            newRow.querySelector('.activity-inscricao-container').style.display = 'inline';
        }

        container.appendChild(newRow);
        checkEmptyState();
    };

    addBtn.addEventListener('click', async function () {
        const newActivityData = {
            titulo: document.getElementById('new_titulo').value.trim(),
            descricao: document.getElementById('new_descricao').value.trim(),
            modalidade: document.getElementById('new_modalidade').value,
            data_hora_inicio: document.getElementById('new_data_hora_inicio').value,
            data_hora_fim: document.getElementById('new_data_hora_fim').value,
            localidade: document.getElementById('new_localidade').value.trim(),
            capacidade: document.getElementById('new_capacidade').value || null,
            requer_inscricao: document.getElementById('new_requer_inscricao').checked ? 1 : 0,
        };

        // Validações
        if (!newActivityData.titulo || !newActivityData.modalidade || !newActivityData.data_hora_inicio || !newActivityData.data_hora_fim || !newActivityData.localidade) {
            alert('Preencha todos os campos obrigatórios (*).');
            return;
        }

        if (new Date(newActivityData.data_hora_fim) <= new Date(newActivityData.data_hora_inicio)) {
            alert('A data de fim deve ser posterior à data de início.');
            return;
        }

        addBtn.disabled = true;
        addBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Salvando...';

        try {
            const response = await fetch('{{ route("eventos.programacao.store.ajax", $evento) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ atividades: [newActivityData] })
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    addActivityToDOM(result.atividade);
                    // Limpa os campos do formulário
                    document.getElementById('new_titulo').value = '';
                    document.getElementById('new_descricao').value = '';
                    document.getElementById('new_modalidade').value = '';
                    document.getElementById('new_data_hora_inicio').value = '';
                    document.getElementById('new_data_hora_fim').value = '';
                    document.getElementById('new_localidade').value = '';
                    document.getElementById('new_capacidade').value = '';
                    document.getElementById('new_requer_inscricao').checked = false;
                } else {
                    alert('Ocorreu um erro ao salvar a atividade.');
                }
            } else {
                alert('Erro de comunicação com o servidor.');
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Ocorreu um erro inesperado.');
        } finally {
            addBtn.disabled = false;
            addBtn.innerHTML = 'Adicionar e Salvar';
        }
    });

    container.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-activity-btn')) {
            // Futuramente, adicionar uma chamada AJAX para remover a atividade do banco de dados
            e.target.closest('.activity-row').remove();
            checkEmptyState();
        }
    });

    checkEmptyState();
});
</script>
@endpush
@endsection