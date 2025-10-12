@extends('layouts.new-event')
@section('title', 'Nova atividade - ' . $evento->nome)

@section('content')
    <style>
        .cardish {
            background: #fff;
            border: 1px solid #eaeaea;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 1px 0 rgba(0, 0, 0, .02);
            margin-bottom: 16px
        }
    </style>

    <div class="container" style="max-width:1100px;padding:26px 0 40px">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ===================== FORM 1: ATIVIDADE ===================== --}}
        <form method="post" action="{{ route('eventos.programacao.store', $evento) }}" autocomplete="off" id="formAtividade">
            @csrf

            <div class="cardish">
                <h3 style="margin-top:0">Nova Atividade</h3>

                <div class="row" style="gap:10px; margin-bottom: 15px;">
                    <div class="col-sm-8">
                        <label for="titulo" class="form-label">Título da Atividade *</label>
                        <input type="text" name="titulo" id="titulo" class="form-control" value="{{ old('titulo') }}"
                            required>
                    </div>

                    <div class="col-sm-4">
                        <label for="modalidade" class="form-label">Tipo *</label>
                        <select name="modalidade" id="modalidade" class="form-control" required>
                            <option value="">Selecione...</option>
                            <option value="Palestra" {{ old('modalidade') == 'Palestra' ? 'selected' : '' }}>Palestra
                            </option>
                            <option value="Minicurso" {{ old('modalidade') == 'Minicurso' ? 'selected' : '' }}>Minicurso
                            </option>
                            <option value="Mesa-redonda" {{ old('modalidade') == 'Mesa-redonda' ? 'selected' : '' }}>
                                Mesa-redonda</option>
                            <option value="Conferência" {{ old('modalidade') == 'Conferência' ? 'selected' : '' }}>
                                Conferência</option>
                            <option value="Apresentação de Trabalho"
                                {{ old('modalidade') == 'Apresentação de Trabalho' ? 'selected' : '' }}>Apresentação de
                                Trabalho</option>
                            <option value="Oficina" {{ old('modalidade') == 'Oficina' ? 'selected' : '' }}>Oficina</option>
                            <option value="Outro" {{ old('modalidade') == 'Outro' ? 'selected' : '' }}>Outro</option>
                        </select>
                    </div>
                </div>

                <div class="row" style="gap:10px; margin-bottom: 15px;">
                    <div class="col-sm-6">
                        <label for="data_hora_inicio" class="form-label">Data e Hora de Início *</label>
                        <input type="datetime-local" name="data_hora_inicio" id="data_hora_inicio" class="form-control"
                            value="{{ old('data_hora_inicio') }}" required>
                    </div>

                    <div class="col-sm-6">
                        <label for="data_hora_fim" class="form-label">Data e Hora de Fim *</label>
                        <input type="datetime-local" name="data_hora_fim" id="data_hora_fim" class="form-control"
                            value="{{ old('data_hora_fim') }}" required>
                    </div>
                </div>

                <div class="row" style="gap:10px; margin-bottom: 15px;">
                    <div class="col-sm-8">
                        <label for="localidade" class="form-label">Local *</label>
                        <input type="text" name="localidade" id="localidade" class="form-control"
                            value="{{ old('localidade') }}" required>
                    </div>

                    <div class="col-sm-4">
                        <label for="capacidade" class="form-label">Vagas</label>
                        <input type="number" name="capacidade" id="capacidade" class="form-control"
                            value="{{ old('capacidade') }}" min="1" placeholder="Opcional">
                    </div>
                </div>

                <div class="row" style="gap:10px; margin-bottom: 15px;">
                    <div class="col-sm-12">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea name="descricao" id="descricao" class="form-control" rows="3"
                            placeholder="Descrição detalhada da atividade...">{{ old('descricao') }}</textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-check">
                            <input type="checkbox" name="requer_inscricao" id="requer_inscricao" value="1"
                                class="form-check-input" {{ old('requer_inscricao') ? 'checked' : '' }}>
                            <label for="requer_inscricao" class="form-check-label">
                                Requer inscrição específica para esta atividade
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right">
                <a href="{{ route('eventos.programacao.index', $evento) }}" class="btn btn-default">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar Atividade</button>
            </div>
        </form>
    </div>

    <script>
        // Validação de datas para o form de atividade
        document.getElementById('data_hora_fim')?.addEventListener('change', function() {
            const inicio = document.getElementById('data_hora_inicio').value;
            const fim = this.value;

            if (inicio && fim && new Date(fim) <= new Date(inicio)) {
                alert('A data de fim deve ser posterior à data de início');
                this.value = '';
            }
        });
    </script>
@endsection
