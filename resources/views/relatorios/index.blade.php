@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Relatórios do Sistema</h2>

    <div class="list-group">
        <a href="{{ route('relatorios.eventos') }}" class="list-group-item list-group-item-action">
            Relatórios de Eventos
        </a>
    </div>
</div>
    
@endsection
