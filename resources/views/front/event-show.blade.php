{{-- resources/views/front/event-show.blade.php --}}
@extends('layouts.app')
@section('title', $evento->nome)

@section('content')
<div class="container py-5">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
    @endif

    <div class="row">
        {{-- Coluna Principal --}}
        <div class="col-lg-8">
            <h2 class="mb-2">{{ $evento->nome }}</h2>

            {{-- Badges de estado --}}
            <div class="mb-4 d-flex flex-wrap gap-2">
                <span class="badge bg-primary">{{ $evento->status ? ucfirst($evento->status) : '—' }}</span>

                @if(method_exists($evento,'isEncerrado') && $evento->isEncerrado())
                    <span class="badge bg-dark">Encerrado</span>
                @elseif($evento->inscricoesAbertas())
                    <span class="badge bg-success">Inscrições abertas</span>
                @else
                    <span class="badge bg-secondary">Inscrições fechadas</span>
                @endif

                @php $vagas = $evento->vagasDisponiveis(); @endphp
                @if(!is_null($vagas))
                    @if($vagas > 0)
                        <span class="badge bg-info">Vagas: {{ $vagas }}</span>
                    @else
                        <span class="badge bg-danger">Vagas encerradas</span>
                    @endif
                @endif
            </div>

            <div class="card mb-4 shadow-sm">
                <div class="card-header"><strong>Informações Gerais</strong></div>
                <div class="card-body">
                    <p class="mb-2"><strong>Período do evento:</strong> {{ $evento->periodo_evento }}</p>
                    <p class="mb-2"><strong>Período de inscrições:</strong> {{ $evento->periodo_inscricao }}</p>
                    <p class="mb-2"><strong>Tipo de realização:</strong> {{ $evento->tipo_evento ?? '—' }}</p>
                    @if($evento->tipo_classificacao)
                        <p class="mb-2"><strong>Classificação:</strong> {{ $evento->tipo_classificacao }}</p>
                    @endif
                    @if($evento->area_tematica)
                        <p class="mb-0"><strong>Área temática:</strong> {{ $evento->area_tematica }}</p>
                    @endif
                </div>
            </div>

            @if($evento->descricao)
                <div class="card mb-4 shadow-sm">
                    <div class="card-header"><strong>Descrição</strong></div>
                    <div class="card-body">{!! nl2br(e($evento->descricao)) !!}</div>
                </div>
            @endif

            {{-- Programação --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header"><strong>Programação</strong></div>
                <div class="card-body">
                    @php use Carbon\Carbon; @endphp

                    @forelse($evento->programacao as $item)
                        @php
                            // Normaliza INÍCIO
                            $iniOut = null;
                            if (!empty($item->data_hora_inicio)) {
                                $iniOut = Carbon::parse($item->data_hora_inicio);
                            } elseif (!empty($item->inicio_em)) {
                                $iniOut = Carbon::parse($item->inicio_em);
                            } elseif (!empty($item->data) || !empty($item->hora_inicio)) {
                                $iniOut = trim(
                                    (!empty($item->data) ? Carbon::parse($item->data)->format('d/m') : '') .
                                    (isset($item->hora_inicio) ? ' '.$item->hora_inicio : '')
                                );
                            }

                            // Normaliza FIM
                            $fimOut = null;
                            if (!empty($item->data_hora_fim)) {
                                $fimOut = Carbon::parse($item->data_hora_fim);
                            } elseif (!empty($item->termino_em)) {
                                $fimOut = Carbon::parse($item->termino_em);
                            } elseif (!empty($item->data) || !empty($item->hora_fim)) {
                                $fimOut = trim(
                                    (!empty($item->data) ? Carbon::parse($item->data)->format('d/m') : '') .
                                    (isset($item->hora_fim) ? ' '.$item->hora_fim : '')
                                );
                            }

                            $fmt = function ($v) {
                                return $v instanceof \Carbon\Carbon ? $v->format('d/m H:i') : ($v ?? '—');
                            };
                        @endphp

                        <div class="border-bottom pb-2 mb-2">
                            <strong>{{ $item->titulo }}</strong>
                            <p class="text-muted mb-1">{{ $item->descricao }}</p>
                            <small class="d-block text-muted">
                                <strong>Período:</strong>
                                {{ $fmt($iniOut) }} - {{ $fmt($fimOut) }}
                            </small>
                        </div>
                    @empty
                        <p class="text-muted mb-0">A programação deste evento ainda não foi divulgada.</p>
                    @endforelse
                </div>
            </div>

            {{-- Relacionados --}}
            @if(isset($relacionados) && $relacionados->count())
                <h4 class="mb-3">Eventos relacionados</h4>
                <div class="row">
                    @foreach ($relacionados as $e)
                        <div class="col-sm-6 col-md-4">
                            <div class="panel panel-default" style="height:100%">
                                @if($e->logomarca_url ?? false)
                                    <div class="panel-heading" style="padding:0;border-bottom:none">
                                        <img src="{{ $e->logomarca_url }}" alt="Logo" class="img-responsive"
                                             style="width:100%;max-height:160px;object-fit:cover">
                                    </div>
                                @endif
                                <div class="panel-body">
                                    <h5 style="margin:0 0 6px">
                                        <a href="{{ route('front.eventos.show', $e) }}">{{ $e->nome }}</a>
                                    </h5>
                                    <small class="text-muted">{{ $e->periodo_evento }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('front.eventos.index') }}" class="btn btn-secondary">Voltar à lista</a>
            </div>
        </div>

        {{-- Coluna lateral --}}
        <div class="col-lg-4">
            @if($evento->logomarca_path)
                <img src="{{ Storage::url($evento->logomarca_path) }}" alt="Logo do evento"
                     class="img-fluid rounded shadow-sm mb-4">
            @endif

            {{-- Participação / Inscrição --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header"><strong>Participação</strong></div>
                <div class="card-body">
                    @auth
                        @php
                            $jaInscrito   = $evento->inscricoes->contains('user_id', auth()->id());
                            $vagas        = $evento->vagasDisponiveis();
                            $semVagas     = !is_null($vagas) && $vagas <= 0;
                            $inscAbertas  = $evento->inscricoesAbertas();
                            $encerrado    = method_exists($evento,'isEncerrado') && $evento->isEncerrado();
                            $motivoFechamento =
                                $encerrado ? 'O evento já foi encerrado.' :
                                ($semVagas ? 'As vagas foram preenchidas.' :
                                'As inscrições não estão abertas no momento.');
                        @endphp

                        @if($jaInscrito)
                            <div class="alert alert-success mb-0">Você já está inscrito.</div>

                        @elseif($inscAbertas && !$semVagas)
                            <form method="post" action="{{ route('inscricoes.store') }}" class="mb-0">
                                @csrf
                                <input type="hidden" name="evento_id" value="{{ $evento->id }}">
                                <button class="btn btn-primary w-100">Inscrever-se Agora</button>
                            </form>

                        @else
                            {{-- Botão bloqueado que abre modal explicativa --}}
                            <button type="button" class="btn btn-outline-secondary w-100"
                                    data-bs-toggle="modal" data-bs-target="#inscricaoBloqueadaModal">
                                Inscrição indisponível
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary w-100">Entre para se inscrever</a>
                    @endauth
                </div>
            </div>

            @if($evento->coordenador)
                <div class="card shadow-sm">
                    <div class="card-header"><strong>Coordenador</strong></div>
                    <div class="card-body">
                        <div>{{ $evento->coordenador->name }}</div>
                        <div class="text-muted small">{{ $evento->coordenador->email }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal: motivo da inscrição indisponível / mensagens de erro --}}
@auth
    @php
        $vagas = $evento->vagasDisponiveis();
        $semVagas = !is_null($vagas) && $vagas <= 0;
        $encerrado = method_exists($evento,'isEncerrado') && $evento->isEncerrado();
        $motivoFechamento =
            $encerrado ? 'O evento já foi encerrado.' :
            ($semVagas ? 'As vagas foram preenchidas.' :
            'As inscrições não estão abertas no momento.');
    @endphp
    <div class="modal fade" id="inscricaoBloqueadaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Inscrição indisponível</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">{{ $motivoFechamento }}</p>
                    @if($errors->any())
                        <hr class="my-3">
                        <p class="text-danger mb-0">{{ $errors->first() }}</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Se houve erro de inscrição, abre o modal automaticamente
        var hasErrors = {!! $errors->any() ? 'true' : 'false' !!};
        if (hasErrors) {
            var modalEl = document.getElementById('inscricaoBloqueadaModal');
            if (modalEl && window.bootstrap) {
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }
    });
    </script>
    @endpush
@endauth
@endsection
