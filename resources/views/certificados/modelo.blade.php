@extends('layouts.app')
@section('title', $modelo->exists ? 'Editar modelo de certificado' : 'Novo modelo de certificado')

@section('content')
<div class="container py-4">

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Ops!</strong> Verifique os erros abaixo.<br>
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header d-flex align-items-center">
            <div>
                <h3 class="mb-0">
                    {{ $modelo->exists ? 'Editar modelo de certificado' : 'Novo modelo de certificado' }}
                </h3>
                <small class="text-muted">
                    Vincule o modelo a um evento e personalize o texto e o fundo.
                </small>
            </div>
            <a href="{{ route('certificado-modelos.index') }}" class="btn btn-outline-secondary ms-auto">
                Voltar
            </a>
        </div>

        <div class="card-body">

            <form method="post"
                  action="{{ $modelo->exists ? route('certificado-modelos.update', $modelo) : route('certificado-modelos.store') }}"
                  enctype="multipart/form-data">
                @csrf
                @if($modelo->exists)
                    @method('PUT')
                @endif

                {{-- LAYOUT 2 COLUNAS: CONFIG À ESQUERDA, PRÉVIA DO CERTIFICADO À DIREITA --}}
                <div class="row">
                    <div class="col-lg-4 border-end">

                        {{-- EVENTO --}}
                        <div class="mb-3">
                            <label class="form-label">Evento *</label>
                            <select name="evento_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                @foreach($eventos as $ev)
                                    <option value="{{ $ev->id }}"
                                        @selected(old('evento_id', $modelo->evento_id) == $ev->id)>
                                        {{ $ev->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- TÍTULO --}}
                        <div class="mb-3">
                            <label class="form-label">Título do modelo *</label>
                            <input type="text" name="titulo" class="form-control"
                                   value="{{ old('titulo', $modelo->titulo) }}" required>
                        </div>

                        {{-- CATEGORIA --}}
                        <div class="mb-3">
                            <label class="form-label">Categoria *</label>
                            @php $tipo = old('slug_tipo', $modelo->slug_tipo); @endphp
                            <select name="slug_tipo" class="form-select" required>
                                <option value="participante" @selected($tipo === 'participante')>Participantes</option>
                                <option value="palestrante" @selected($tipo === 'palestrante')>Palestrantes</option>
                                <option value="organizador" @selected($tipo === 'organizador')>Organizadores</option>
                            </select>
                            <small class="text-muted">
                                Ajuda a identificar se o modelo é para participante, palestrante ou organização.
                            </small>
                        </div>

                        {{-- ATRIBUIÇÃO --}}
                        <div class="mb-3">
                            <label class="form-label">Atribuição (quem recebe)</label>
                            <input type="text" name="atribuicao" class="form-control"
                                   placeholder="Ex: Todos os inscritos presentes no evento"
                                   value="{{ old('atribuicao', $modelo->atribuicao) }}">
                        </div>

                        {{-- PUBLICADO --}}
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="publicado" id="publicado"
                                   class="form-check-input"
                                   value="1" @checked(old('publicado', $modelo->publicado))>
                            <label for="publicado" class="form-check-label">
                                Modelo publicado (disponível para seleção na emissão)
                            </label>
                        </div>

                        {{-- IMAGEM DE FUNDO --}}
                        <div class="mb-3">
                            <label class="form-label">Imagem de fundo (opcional)</label>
                            <input type="file" name="background" class="form-control">
                            <small class="text-muted">
                                Tamanho sugerido: 1170 x 830px, até 2MB.
                            </small>
                            @if($modelo->background_path)
                                <div class="mt-2">
                                    <strong>Atual:</strong><br>
                                    <img src="{{ Storage::url($modelo->background_path) }}"
                                         alt="Fundo atual" style="max-width: 100%; border:1px solid #ddd;">
                                </div>
                            @endif
                        </div>

                    </div> {{-- /col esquerda --}}

                    <div class="col-lg-8">

                        {{-- TABS / TAGS DISPONÍVEIS --}}
                        <ul class="nav nav-tabs mb-3">
                            <li class="nav-item">
                                <span class="nav-link active">Editor do certificado</span>
                            </li>
                            <li class="nav-item ms-auto">
                                <span class="nav-link disabled text-muted small">
                                    Use as tags para montar o texto: veja abaixo
                                </span>
                            </li>
                        </ul>

                        <div class="alert alert-info small">
                            <strong>Tags disponíveis:</strong><br>
                            <code>{nome_participante}</code>,
                            <code>{nome_evento}</code>,
                            <code>{data_inicio_evento}</code>,
                            <code>{data_fim_evento}</code>,
                            <code>{carga_horaria}</code>,
                            <code>{nome_organizador}</code>,
                            <code>{nome_palestrante}</code>,
                            <code>{local_evento}</code>
                            etc. &mdash; serão substituídas automaticamente ao emitir o certificado.
                        </div>

                        @php
                            $textoInicial = old(
                                'corpo_html',
                                $modelo->corpo_html ?: 'Certificamos que {nome_participante} participou do evento {nome_evento}, realizado no período de {data_inicio_evento} a {data_fim_evento}, com carga horária total de {carga_horaria} horas.'
                            );
                        @endphp

                        {{-- “LONA” DO CERTIFICADO (PREVIEW + EDITOR) --}}
                        <div class="mb-3">
                            <label class="form-label">Pré-visualização do certificado</label>

                            <div class="border rounded bg-white position-relative overflow-hidden"
                                 style="
                                     width: 100%;
                                     max-width: 100%;
                                     aspect-ratio: 14 / 10;
                                     background-color: #fdfdfd;
                                     @if($modelo->background_path)
                                        background-image: url('{{ Storage::url($modelo->background_path) }}');
                                        background-size: cover;
                                        background-position: center;
                                     @endif
                                 ">

                                {{-- Miolo editável --}}
                                <div class="position-absolute top-50 start-50 translate-middle text-center px-4"
                                     style="width: 80%;">
                                    <h4 class="fw-bold mb-4" style="letter-spacing: 0.15em;">
                                        CERTIFICADO
                                    </h4>

                                    {{-- Editor de fato (contenteditable) --}}
                                    <div id="certificate-editor"
                                         contenteditable="true"
                                         class="text-start small"
                                         style="max-height: 220px; overflow-y: auto; background-color: rgba(255,255,255,0.9); padding: 0.75rem; border-radius: 0.5rem; border: 1px dashed #ccc;">
                                        {!! $textoInicial !!}
                                    </div>
                                </div>
                            </div>

                            <small class="text-muted d-block mt-1">
                                Edite o texto diretamente no quadro acima. As tags entre <code>{ }</code> serão
                                substituídas pelos dados reais do participante/evento.
                            </small>
                        </div>

                        {{-- CAMPO REAL ENVIADO PARA O BACK-END (OBRIGATÓRIO) --}}
                        <textarea name="corpo_html"
                                  id="corpo_html"
                                  class="d-none"
                                  required>{!! $textoInicial !!}</textarea>

                    </div> {{-- /col direita --}}
                </div> {{-- /row --}}

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('certificado-modelos.index') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                    <button class="btn btn-primary">
                        {{ $modelo->exists ? 'Salvar alterações' : 'Criar modelo' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editor   = document.getElementById('certificate-editor');
    const textarea = document.getElementById('corpo_html');
    if (!editor || !textarea) return;

    const sync = () => {
        textarea.value = editor.innerHTML.trim();
    };

    // sincroniza ao digitar
    editor.addEventListener('input', sync);

    // garante sincronização antes de enviar o formulário
    const form = editor.closest('form');
    if (form) {
        form.addEventListener('submit', function () {
            sync();
        });
    }

    // sincroniza uma vez na carga inicial
    sync();
});
</script>
@endpush
