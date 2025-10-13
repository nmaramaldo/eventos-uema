@extends('layouts.app')
@section('title', $evento->nome)

@section('content')
<div class="container py-5">
    <div class="row">
        {{-- Coluna Principal (Esquerda) --}}
        <div class="col-lg-8">
            {{-- Título e Badges --}}
            <h2 class="mb-2">{{ $evento->nome }}</h2>
            <div class="mb-4">
                <span class="badge bg-primary">{{ $evento->status ? ucfirst($evento->status) : '—' }}</span>
                @if($evento->inscricoesAbertas())
                    <span class="badge bg-success">Inscrições abertas</span>
                @else
                    <span class="badge bg-secondary">Inscrições fechadas</span>
                @endif
                @if(!is_null($evento->vagasDisponiveis()))
                    <span class="badge bg-info">Vagas: {{ $evento->vagasDisponiveis() }}</span>
                @endif
            </div>

            {{-- Card de Informações Gerais --}}
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

            {{-- Card de Descrição --}}
            @if($evento->descricao)
                <div class="card mb-4 shadow-sm">
                    <div class="card-header"><strong>Descrição</strong></div>
                    <div class="card-body">{!! nl2br(e($evento->descricao)) !!}</div>
                </div>
            @endif
            
            {{-- ✅ CORREÇÃO: Card de Programação --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header"><strong>Programação</strong></div>
                <div class="card-body">
                    {{-- Usando o nome correto do relacionamento: 'programacao' --}}
                    @forelse($evento->programacao as $item)
                        <div class="border-bottom pb-2 mb-2">
                            <strong>{{ $item->titulo }}</strong>
                            <p class="text-muted mb-1">{{ $item->descricao }}</p>
                            <small class="d-block text-muted">
                                <strong>Período:</strong> {{ $item->data_hora_inicio?->format('d/m H:i') }} - {{ $item->data_hora_fim?->format('d/m H:i') }}
                            </small>
                        </div>
                    @empty
                        <p class="text-muted">A programação deste evento ainda não foi divulgada.</p>
                    @endforelse
                </div>
            </div>

            {{-- Botões de Ação --}}
            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('eventos.index') }}" class="btn btn-secondary">Voltar à Lista</a>
                @can('update', $evento)
                    <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-primary">Editar Evento</a>
                @endcan
            </div>
        </div>

        {{-- Coluna Lateral (Direita) --}}
        <div class="col-lg-4">
            @if($evento->logomarca_path)
                <img src="{{ Storage::url($evento->logomarca_path) }}" alt="Logo do evento" class="img-fluid rounded shadow-sm mb-4">
            @endif

            {{-- Card de Inscrição --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header"><strong>Participação</strong></div>
                <div class="card-body">
                    @auth
                        @php
                            $jaInscrito = $evento->inscricoes->contains('user_id', auth()->id());
                        @endphp
                        @if($jaInscrito)
                            <div class="alert alert-success mb-0">Você já está inscrito.</div>
                        @elseif($evento->inscricoesAbertas())
                            <form method="post" action="{{ route('inscricoes.store') }}" class="mb-0">
                                @csrf
                                <input type="hidden" name="evento_id" value="{{ $evento->id }}">
                                <button class="btn btn-primary w-100">Inscrever-se Agora</button>
                            </form>
                        @else
                            <div class="alert alert-info mb-0">As inscrições não estão abertas no momento.</div>
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
@endsection