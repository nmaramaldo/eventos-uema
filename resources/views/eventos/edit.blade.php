@extends('layouts.app')

@section('title', 'Editar Evento - Eventos UEMA')

@section('content')
    @php
        // helpers para datetime-local
        $di  = old('data_inicio_evento', isset($evento) ? optional($evento->data_inicio_evento)->format('Y-m-d\TH:i') : '');
        $df  = old('data_fim_evento',    isset($evento) ? optional($evento->data_fim_evento)->format('Y-m-d\TH:i')    : '');
        $ii  = old('data_inicio_inscricao', isset($evento) ? optional($evento->data_inicio_inscricao)->format('Y-m-d\TH:i') : '');
        $if  = old('data_fim_inscricao',    isset($evento) ? optional($evento->data_fim_inscricao)->format('Y-m-d\TH:i')    : '');
        $st  = old('status', $evento->status ?? 'rascunho');
        $tipoAtual = old('tipo_evento', $evento->tipo_evento ?? 'presencial');
    @endphp

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-purple-600 to-blue-600 text-black py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl font-bold mb-4">Editar Evento</h1>
                <p class="text-purple-100 text-lg">Atualize as informações do seu evento</p>
            </div>
        </div>
    </section>

    <!-- Event Form -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <form action="{{ route('eventos.update', $evento) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')

                    {{-- Informações Básicas --}}
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-info-circle mr-3 text-purple-600"></i>
                            Informações Básicas
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6">
                            {{-- Título --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Título do Evento *</label>
                                <input type="text" name="nome" value="{{ old('nome', $evento->nome) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('nome') border-red-500 @enderror"
                                       required>
                                @error('nome') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Categoria --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Categoria *</label>
                                <select name="tipo_classificacao"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('tipo_classificacao') border-red-500 @enderror"
                                        required>
                                    @php $cats = ['Tecnologia','Acadêmico','Cultural','Esportivo','Científico','Social','Competição','Workshop']; @endphp
                                    <option value="">Selecione uma categoria</option>
                                    @foreach($cats as $cat)
                                        <option value="{{ $cat }}" @selected(old('tipo_classificacao', $evento->tipo_classificacao)===$cat)>{{ $cat }}</option>
                                    @endforeach
                                </select>
                                @error('tipo_classificacao') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Capa (arquivo) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Imagem do Evento</label>
                                <input type="file" name="capa" accept="image/*"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('capa') border-red-500 @enderror">
                                <p class="text-gray-500 text-sm mt-1">Formatos: JPG, PNG, GIF (máx. 3MB)</p>
                                @if(!empty($evento->logomarca_url))
                                    <div class="mt-2">
                                        <a href="{{ $evento->logomarca_url }}" target="_blank" class="text-purple-600 underline">ver imagem atual</a>
                                    </div>
                                @endif
                                @error('capa') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Descrição --}}
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Descrição do Evento *</label>
                            <textarea name="descricao" rows="5"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('descricao') border-red-500 @enderror"
                                      required>{{ old('descricao', $evento->descricao) }}</textarea>
                            @error('descricao') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Tipo de Evento --}}
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-laptop-house mr-3 text-purple-600"></i>
                            Tipo de Evento
                        </h2>
                        <div class="flex gap-4">
                            @foreach(['online'=>'Online','presencial'=>'Presencial','hibrido'=>'Híbrido'] as $val=>$rot)
                                <label class="flex items-center">
                                    <input type="radio" name="tipo_evento" value="{{ $val }}"
                                           class="text-purple-600 focus:ring-purple-500"
                                           {{ $tipoAtual === $val ? 'checked' : '' }} required>
                                    <span class="ml-2 text-gray-700">{{ $rot }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('tipo_evento') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Datas --}}
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-calendar-alt mr-3 text-purple-600"></i>
                            Datas
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Abertura do Evento *</label>
                                <input type="datetime-local" name="data_inicio_evento" value="{{ $di }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('data_inicio_evento') border-red-500 @enderror"
                                       required>
                                @error('data_inicio_evento') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Término do Evento *</label>
                                <input type="datetime-local" name="data_fim_evento" value="{{ $df }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('data_fim_evento') border-red-500 @enderror"
                                       required>
                                @error('data_fim_evento') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Local/Área temática + Inscrições --}}
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-users mr-3 text-purple-600"></i>
                            Inscrições e Outras Informações
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Área temática</label>
                                <input type="text" name="area_tematica" value="{{ old('area_tematica', $evento->area_tematica) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('area_tematica') border-red-500 @enderror"
                                       placeholder="Ex.: Educação / Saúde / TI">
                                @error('area_tematica') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Número de Vagas (opcional)</label>
                                <input type="number" name="vagas" min="1" value="{{ old('vagas', $evento->vagas) }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('vagas') border-red-500 @enderror">
                                @error('vagas') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Início das inscrições *</label>
                                <input type="datetime-local" name="data_inicio_inscricao" value="{{ $ii }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('data_inicio_inscricao') border-red-500 @enderror"
                                       required>
                                @error('data_inicio_inscricao') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fim das inscrições *</label>
                                <input type="datetime-local" name="data_fim_inscricao" value="{{ $if }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('data_fim_inscricao') border-red-500 @enderror"
                                       required>
                                @error('data_fim_inscricao') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status do Evento</label>
                                <div class="flex items-center space-x-6">
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="ativo" class="text-purple-600 focus:ring-purple-500" {{ $st==='ativo' ? 'checked' : '' }}>
                                        <span class="ml-2 text-gray-700">Ativo (inscrições liberadas)</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="rascunho" class="text-purple-600 focus:ring-purple-500" {{ $st==='rascunho' ? 'checked' : '' }}>
                                        <span class="ml-2 text-gray-700">Rascunho</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="publicado" class="text-purple-600 focus:ring-purple-500" {{ $st==='publicado' ? 'checked' : '' }}>
                                        <span class="ml-2 text-gray-700">Publicado</span>
                                    </label>
                                </div>
                                @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Ações --}}
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <div class="flex flex-col md:flex-row gap-4 justify-between">
                            <a href="{{ route('eventos.index') }}"
                               class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-4 rounded-lg font-semibold text-center transition-colors">
                                <i class="fas fa-times mr-2"></i>Cancelar
                            </a>
                            <button class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-4 rounded-lg font-semibold transition-colors">
                                <i class="fas fa-save mr-2"></i>Salvar alterações
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
