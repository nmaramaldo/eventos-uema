@extends('layouts.app')
@section('title', 'Eventos')

@section('content')
<div class="container py-5">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            {{-- Cabeçalho com botão alinhado totalmente à direita --}}
            <div class="d-flex align-items-center flex-wrap gap-2">
                <div>
                    <h2 class="mb-0">Eventos</h2>
                    <p class="text-muted mb-0">Lista de eventos cadastrados.</p>
                </div>

                @can('create', App\Models\Event::class)
                    <a href="{{ route('eventos.create') }}" class="btn btn-primary ms-auto">
                        Novo Evento
                    </a>
                @endcan
            </div>
        </div>

        {{-- Filtros / Busca --}}
        <div class="card-body border-bottom">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nome ou descrição">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    @php
                        $st = request('status');
                        $statusOptions = ['', 'rascunho', 'ativo', 'publicado'];
                    @endphp
                    <select name="status" class="form-select">
                        <option value="">-- Todos --</option>
                        @foreach ($statusOptions as $opt)
                            @if($opt!=='')
                                <option value="{{ $opt }}" @selected($st===$opt)>{{ ucfirst($opt) }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Inscrições</label>
                    @php $jan = request('janela'); @endphp
                    <select name="janela" class="form-select">
                        <option value="">-- Todas --</option>
                        <option value="abertas"   @selected($jan==='abertas')>Abertas</option>
                        <option value="fechadas"  @selected($jan==='fechadas')>Fechadas</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1">Aplicar</button>
                    <a class="btn btn-outline-secondary" href="{{ route('eventos.index') }}">Limpar</a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3" style="width: 120px">ID</th>
                            <th class="px-3">Nome</th>
                            <th class="px-3">Período do Evento</th>
                            <th class="px-3">Período de Inscrições</th>
                            <th class="px-3">Tipo</th>
                            <th class="px-3">Status</th>
                            <th class="px-3 text-end" style="width: 360px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($eventos as $e)
                            <tr>
                                <td class="px-3">
                                    <code class="small">{{ $e->id }}</code>
                                </td>
                                <td class="px-3">
                                    <a href="{{ route('eventos.show', $e) }}"><strong>{{ $e->nome }}</strong></a>
                                </td>
                                <td class="px-3">{{ $e->periodo_evento }}</td>
                                <td class="px-3">{{ $e->periodo_inscricao }}</td>
                                <td class="px-3">{{ $e->tipo_evento ?? '—' }}</td>
                                <td class="px-3">
                                    @php
                                        $map = [
                                            'Aberto'                    => 'success',
                                            'Inscrições encerradas'     => 'secondary',
                                            'Encerrado'                 => 'dark',
                                            'Não iniciado'              => 'warning',
                                            'Publicado'                 => 'primary',
                                            'Rascunho'                  => 'secondary',
                                        ];
                                        $rotulo = $e->status_exibicao ?? ($e->status ? ucfirst($e->status) : '—');
                                        $cls = $map[$rotulo] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $cls }}">{{ $rotulo }}</span>
                                </td>
                                <td class="px-3 text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('eventos.show', $e) }}">Ver</a>

                                    @can('update', $e)
                                        {{-- Programação das atividades --}}
                                        <a class="btn btn-sm btn-outline-primary"
                                           href="{{ route('eventos.programacao.index', $e) }}">
                                            Programação
                                        </a>

                                        {{-- ✅ botão separado de Check-in / Credenciamento --}}
                                        <a class="btn btn-sm btn-outline-success"
                                           href="{{ route('eventos.checkin', $e) }}">
                                            Check-in
                                        </a>

                                        {{-- Modelos / emissão de certificados desse evento --}}
                                        <a class="btn btn-sm btn-outline-info"
                                           href="{{ route('certificados.create', ['evento_id' => $e->id]) }}">
                                            Certificados
                                        </a>

                                        <a class="btn btn-sm btn-primary" href="{{ route('eventos.edit', $e) }}">Editar</a>

                                        @if($e->status == 'rascunho')
                                            <form action="{{ route('eventos.publish', $e) }}" method="post" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-sm btn-success"
                                                        onclick="return confirm('Publicar este evento?')">
                                                    Publicar
                                                </button>
                                            </form>
                                        @endif
                                    @endcan

                                    @can('delete', $e)
                                        <form action="{{ route('eventos.destroy', $e) }}" method="post" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Tem certeza que deseja remover este evento?')">
                                                Excluir
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted p-5">Nenhum evento encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($eventos->hasPages())
            <div class="card-footer">
                {{ $eventos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
