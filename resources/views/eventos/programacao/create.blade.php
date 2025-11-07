@extends('layouts.app')
@section('title', 'Nova Atividade - ' . $evento->nome)

@section('content')
    @push('styles')
        <meta name="csrf-token" content="{{ csrf_token() }}">
    @endpush
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
                                        <input type="text" id="new_titulo" class="form-control"
                                            placeholder="Ex: Palestra de Abertura">
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
                                        <input type="text" id="new_localidade" class="form-control"
                                            placeholder="Ex: Auditório Central">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label for="new_capacidade" class="form-label">Vagas (opcional)</label>
                                        <input type="number" id="new_capacidade" class="form-control" min="0">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-center mt-3">
                                        <div class="form-check">
                                            <input type="checkbox" id="new_requer_inscricao" class="form-check-input">
                                            <label for="new_requer_inscricao" class="form-check-label">Requer
                                                Inscrição?</label>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="add_activity_btn" class="btn btn-secondary mt-3">Adicionar e
                                    Salvar</button>
                            </div>

                            {{-- Lista de atividades --}}
                            <h5 class="mt-4">Atividades Adicionadas</h5>
                            <div id="atividades-container">
                                <p id="no-activities-text" class="text-muted">Nenhuma atividade adicionada ainda.</p>
                            </div>

                            @php
                                // Verifica se o evento já tem atividades cadastradas
                                $hasAtividades = $evento->programacao()->exists();
                                $nextRoute = $hasAtividades
                                    ? route('eventos.programacao.index', $evento) // Já tem atividades: volta para lista
                                    : route('eventos.palestrantes.create', $evento); // Não tem: vai para palestrantes

                                $btnText = $hasAtividades ? 'Finalizar' : 'Continuar para Palestrantes';
                            @endphp

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('eventos.programacao.index', $evento) }}"
                                    class="btn btn-outline-secondary">
                                    ← Voltar
                                </a>
                                <a href="{{ $nextRoute }}" class="btn btn-primary">
                                    {{ $btnText }}
                                </a>
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
                                    <button type="button"
                                        class="btn btn-sm btn-primary edit-activity-btn me-1">Editar</button>
                                    <button type="button"
                                        class="btn btn-sm btn-danger remove-activity-btn ms-2">×</button>
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
            document.addEventListener('DOMContentLoaded', function() {
                const addBtn = document.getElementById('add_activity_btn');
                const container = document.getElementById('atividades-container');
                const template = document.getElementById('activity-template');
                const noActivitiesText = document.getElementById('no-activities-text');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const form = document.getElementById('activityForm');
                const hiddenIdInput = document.createElement('input');
                hiddenIdInput.type = 'hidden';
                hiddenIdInput.id = 'editing_activity_id';
                form.appendChild(hiddenIdInput);

                let activities = [];
                let isSaving = false;

                console.log('Script carregado - CSRF Token:', csrfToken); // Debug

                const checkEmptyState = () => {
                    noActivitiesText.style.display = activities.length === 0 ? 'block' : 'none';
                };

                const formatDate = (dateString) => {
                    if (!dateString) return '';
                    const d = new Date(dateString);
                    if (Number.isNaN(d.getTime())) return dateString;
                    return d.toLocaleString('pt-BR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                };

                const formatToDateTimeLocal = (dateString) => {
                    if (!dateString) return '';
                    const d = new Date(dateString);
                    if (Number.isNaN(d.getTime())) return '';
                    const pad = (num) => num.toString().padStart(2, '0');
                    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
                };

                const renderActivities = () => {
                    console.log('Renderizando atividades:', activities); // Debug
                    container.innerHTML = '';
                    activities.forEach(activity => {
                        const clone = template.content.cloneNode(true);
                        const row = clone.querySelector('.activity-row');
                        row.dataset.activityId = activity.id;

                        row.querySelector('.activity-title').textContent = activity.titulo;
                        row.querySelector('.activity-description').textContent = activity.descricao ||
                            'Sem descrição';
                        row.querySelector('.activity-modality').textContent = activity.modalidade || '—';
                        row.querySelector('.activity-location').textContent = activity.localidade || '—';
                        row.querySelector('.activity-time-start').textContent = formatDate(activity
                            .data_hora_inicio);
                        row.querySelector('.activity-time-end').textContent = formatDate(activity
                            .data_hora_fim);

                        const vagas = activity.capacidade;
                        if (vagas) {
                            row.querySelector('.activity-capacity').textContent = vagas;
                            row.querySelector('.activity-capacity-container').style.display = 'inline';
                        }
                        if (activity.requer_inscricao) {
                            row.querySelector('.activity-inscricao-container').style.display = 'inline';
                        }

                        container.appendChild(row);
                    });
                    checkEmptyState();
                };

                const validateForm = () => {
                    const titulo = document.getElementById('new_titulo').value.trim();
                    const modalidade = document.getElementById('new_modalidade').value;
                    const dataInicio = document.getElementById('new_data_hora_inicio').value;
                    const dataFim = document.getElementById('new_data_hora_fim').value;
                    const localidade = document.getElementById('new_localidade').value.trim();

                    // Validação básica
                    if (!titulo) {
                        alert('Título é obrigatório');
                        return false;
                    }
                    if (!modalidade) {
                        alert('Modalidade é obrigatória');
                        return false;
                    }
                    if (!dataInicio) {
                        alert('Data de início é obrigatória');
                        return false;
                    }
                    if (!dataFim) {
                        alert('Data de fim é obrigatória');
                        return false;
                    }
                    if (!localidade) {
                        alert('Local é obrigatório');
                        return false;
                    }

                    // Validação de datas
                    if (new Date(dataFim) <= new Date(dataInicio)) {
                        alert('A data de fim deve ser posterior à data de início');
                        return false;
                    }

                    return true;
                };

                const saveActivity = async () => {
                    if (isSaving) return;

                    console.log('Iniciando salvamento...'); // Debug

                    // Validação
                    if (!validateForm()) {
                        return;
                    }

                    const editingId = document.getElementById('editing_activity_id').value;
                    const payload = {
                        id: editingId || null,
                        titulo: document.getElementById('new_titulo').value.trim(),
                        descricao: document.getElementById('new_descricao').value.trim(),
                        modalidade: document.getElementById('new_modalidade').value,
                        data_hora_inicio: document.getElementById('new_data_hora_inicio').value,
                        data_hora_fim: document.getElementById('new_data_hora_fim').value,
                        localidade: document.getElementById('new_localidade').value.trim(),
                        capacidade: document.getElementById('new_capacidade').value || null,
                        requer_inscricao: document.getElementById('new_requer_inscricao').checked ? 1 : 0,
                    };

                    console.log('Payload:', payload); // Debug

                    isSaving = true;
                    addBtn.disabled = true;
                    addBtn.innerHTML =
                        `<span class="spinner-border spinner-border-sm" role="status"></span> Salvando...`;

                    try {

                        const url = '{{ route('eventos.programacao.store.ajax', $evento) }}';

                        console.log('Fazendo requisição para:', url); // Debug

                        const resp = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        console.log('Resposta status:', resp.status); // Debug

                        const data = await resp.json();
                        console.log('Resposta data:', data); // Debug

                        if (!resp.ok) {
                            if (resp.status === 422 && data.errors) {
                                const firstError = Object.values(data.errors)[0][0];
                                alert(firstError);
                            } else {
                                alert(data.message || `Erro de comunicação com o servidor. (${resp.status})`);
                            }
                            return;
                        }

                        if (data.success) {
                            if (editingId) {
                                const index = activities.findIndex(a => a.id == editingId);
                                if (index > -1) {
                                    activities[index] = data.atividade;
                                }
                            } else {
                                activities.push(data.atividade);
                            }
                            renderActivities();
                            form.reset();
                            hiddenIdInput.value = '';
                            addBtn.textContent = 'Adicionar e Salvar';

                            alert('Atividade salva com sucesso!');
                        } else {
                            alert('Ocorreu um erro ao salvar a atividade.');
                        }
                    } catch (err) {
                        console.error('Erro na requisição:', err);
                        alert('Erro de comunicação com o servidor. Verifique o console para mais detalhes.');
                    } finally {
                        isSaving = false;
                        addBtn.disabled = false;
                        addBtn.innerHTML = 'Adicionar e Salvar';
                    }
                };

                addBtn.addEventListener('click', saveActivity);

                // **REMOVA ou COMENTE esta parte problemática:**
                /*
                const requiredFieldsIds = ['new_titulo', 'new_modalidade', 'new_data_hora_inicio', 'new_data_hora_fim', 'new_localidade'];
                requiredFieldsIds.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    field.addEventListener('blur', saveActivity);
                });
                */

                container.addEventListener('click', function(e) {
                    const editBtn = e.target.closest('.edit-activity-btn');
                    if (editBtn) {
                        const row = editBtn.closest('.activity-row');
                        const activityId = row.dataset.activityId;
                        const activity = activities.find(a => a.id == activityId);

                        if (activity) {
                            hiddenIdInput.value = activity.id;
                            document.getElementById('new_titulo').value = activity.titulo;
                            document.getElementById('new_descricao').value = activity.descricao || '';
                            document.getElementById('new_modalidade').value = activity.modalidade;
                            document.getElementById('new_data_hora_inicio').value = formatToDateTimeLocal(
                                activity.data_hora_inicio);
                            document.getElementById('new_data_hora_fim').value = formatToDateTimeLocal(activity
                                .data_hora_fim);
                            document.getElementById('new_localidade').value = activity.localidade;
                            document.getElementById('new_capacidade').value = activity.capacidade || '';
                            document.getElementById('new_requer_inscricao').checked = activity.requer_inscricao;

                            addBtn.textContent = 'Atualizar Atividade';
                            window.scrollTo(0, 0);
                        }
                    }

                    const removeBtn = e.target.closest('.remove-activity-btn');
                    if (removeBtn) {
                        if (!confirm('Tem certeza que deseja remover esta atividade?')) return;

                        const row = removeBtn.closest('.activity-row');
                        const activityId = row.dataset.activityId;
                        activities = activities.filter(a => a.id != activityId);
                        renderActivities();
                    }
                });

                checkEmptyState();
            });
        </script>
    @endpush
@endsection
