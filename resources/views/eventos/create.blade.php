@extends('layouts.app')

@section('title', 'Criar Evento - Eventos UEMA')

@section('content')
    <!-- Page Header -->
    <section class="bg-gradient-to-r from-purple-600 to-blue-600 text-black py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl font-bold mb-4">Criar Novo Evento</h1>
                <p class="text-purple-100 text-lg">Compartilhe conhecimento e conecte-se com a comunidade universitária</p>
            </div>
        </div>
    </section>

    <!-- Event Form -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <form action="{{ route('eventos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    {{-- Informações Básicas --}}
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-info-circle mr-3 text-purple-600"></i>
                            Informações Básicas
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6">
                            {{-- Título --}}
                            <div class="md:col-span-2">
                                <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">Título do Evento
                                    *</label>
                                <input type="text" id="nome" name="nome" value="{{ old('nome') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('nome') border-red-500 @enderror"
                                    placeholder="Ex: Workshop de Desenvolvimento Web" required>
                                @error('nome')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Categoria --}}
                            <div>
                                <label for="tipo_classificacao"
                                    class="block text-sm font-medium text-gray-700 mb-2">Categoria
                                    *</label>
                                <select id="tipo_classificacao" name="tipo_classificacao"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('tipo_classificacao') border-red-500 @enderror"
                                    required>
                                    <option value="">Selecione uma categoria</option>
                                    @foreach (['Tecnologia', 'Acadêmico', 'Cultural', 'Esportivo', 'Científico', 'Social', 'Competição', 'Workshop'] as $cat)
                                        <option value="{{ $cat }}"
                                            {{ old('tipo_classificacao') == $cat ? 'selected' : '' }}>{{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_classificacao')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Imagem --}}
                            <div>
                                <label for="logomarca_url" class="block text-sm font-medium text-gray-700 mb-2">Imagem do
                                    Evento</label>
                                <div class="relative">
                                    <input type="file" id="logomarca_url" name="logomarca_url" accept="image/*"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('logomarca_url') border-red-500 @enderror">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                </div>
                                <p class="text-gray-500 text-sm mt-1">Formatos aceitos: JPG, PNG, GIF (máx. 2MB)</p>
                                @error('logomarca_url')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Descrição --}}
                        <div class="mt-6">
                            <label for="descricao" class="block text-sm font-medium text-gray-700 mb-2">Descrição do Evento
                                *</label>
                            <textarea id="descricao" name="descricao" rows="5"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('descricao') border-red-500 @enderror"
                                placeholder="Descreva seu evento, objetivos, público-alvo e o que os participantes podem esperar..." required>{{ old('descricao') }}</textarea>
                            @error('descricao')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>


                    {{-- Tipo de Evento --}}
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-laptop-house mr-3 text-purple-600"></i>
                            Tipo de Evento
                        </h2>

                        <div class="flex gap-4">
                            {{-- Online --}}
                            <label class="flex items-center">
                                <input type="radio" name="tipo_evento" value="online"
                                    {{ old('tipo_evento') == 'online' ? 'checked' : '' }}
                                    class="text-purple-600 focus:ring-purple-500" required>
                                <span class="ml-2 text-gray-700">Online</span>
                            </label>

                            {{-- Presencial --}}
                            <label class="flex items-center">
                                <input type="radio" name="tipo_evento" value="presencial"
                                    {{ old('tipo_evento') == 'presencial' ? 'checked' : '' }}
                                    class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Presencial</span>
                            </label>

                            {{-- Híbrido --}}
                            <label class="flex items-center">
                                <input type="radio" name="tipo_evento" value="hibrido"
                                    {{ old('tipo_evento') == 'hibrido' ? 'checked' : '' }}
                                    class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Híbrido</span>
                            </label>
                        </div>

                        @error('tipo_evento')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Data e Horário --}}
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-calendar-alt mr-3 text-purple-600"></i>
                            Data
                        </h2>

                        <div class="grid md:grid-cols-3 gap-6">
                            <div>
                                <label for="data_inicio_evento" class="block text-sm font-medium text-gray-700 mb-2">Data
                                    inicio Evento *</label>
                                <input type="date" id="data_inicio_evento" name="data_inicio_evento"
                                    value="{{ old('data_inicio_evento') }}" min="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('data_inicio_evento') border-red-500 @enderror"
                                    required>
                                @error('data_inicio_evento')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="data_fim_evento" class="block text-sm font-medium text-gray-700 mb-2">Data fim
                                    Evento *</label>
                                <input type="date" id="data_fim_evento" name="data_fim_evento"
                                    value="{{ old('data_fim_evento') }}" min="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('data_fim_evento') border-red-500 @enderror"
                                    required>
                                @error('data_fim_evento')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Localização --}}
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-map-marker-alt mr-3 text-purple-600"></i>
                            Localização
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="area_tematica" class="block text-sm font-medium text-gray-700 mb-2">Local do
                                    Evento *</label>
                                <input type="text" id="local" name="area_tematica"
                                    value="{{ old('area_tematica') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('area_tematica') border-red-500 @enderror"
                                    placeholder="Ex: Auditório Central - Campus São Luís" required>
                                @error('area_tematica')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Inscrições --}}
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-users mr-3 text-purple-600"></i>
                            Inscrições
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="data_inicio_inscricao"
                                    class="block text-sm font-medium text-gray-700 mb-2">Data inicio inscrições</label>
                                <input type="date" id="data_inicio_inscricao" name="data_inicio_inscricao"
                                    value="{{ old('data_inicio_inscricao') }}" min="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('data_inicio_inscricao') border-red-500 @enderror">
                                @error('data_inicio_inscricao')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="data_fim_inscricao" class="block text-sm font-medium text-gray-700 mb-2">Data
                                    fim inscrições</label>
                                <input type="date" id="data_fim_inscricao" name="data_fim_inscricao"
                                    value="{{ old('data_fim_inscricao') }}" min="{{ date('Y-m-d') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('data_fim_inscricao') border-red-500 @enderror">
                                @error('data_fim_inscricao')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid md:grid-cols-3 gap-6">
                                <!-- Max Participants -->
                                <div>
                                    <label for="vagas_total" class="block text-sm font-medium text-gray-700 mb-2">
                                        Número de Vagas *
                                    </label>
                                    <input type="number" id="vagas_total" name="vagas_total"
                                        value="{{ old('vagas_total') }}" min="1" max="1000"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 @error('vagas_total') border-red-500 @enderror"
                                        required>
                                    @error('vagas_total')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>

                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status do Evento</label>
                                <div class="flex items-center space-x-6">
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="ativo"
                                            {{ old('status', 'ativo') == 'ativo' ? 'checked' : '' }}
                                            class="text-purple-600 focus:ring-purple-500">
                                        <span class="ml-2 text-gray-700">Ativo (visível para todos)</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="rascunho"
                                            {{ old('status') == 'rascunho' ? 'checked' : '' }}
                                            class="text-purple-600 focus:ring-purple-500">
                                        <span class="ml-2 text-gray-700">Rascunho (apenas você pode ver)</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Ações do Form --}}
                        <div class="bg-white rounded-xl shadow-lg p-8">
                            <div class="flex flex-col md:flex-row gap-4 justify-between">
                                <a href="{{ route('eventos.index') }}"
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-4 rounded-lg font-semibold text-center transition-colors">
                                    <i class="fas fa-times mr-2"></i>Cancelar
                                </a>

                                <div class="flex gap-4">
                                    <button type="submit" name="action" value="draft"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-8 py-4 rounded-lg font-semibold transition-colors">
                                        <i class="fas fa-save mr-2"></i>Salvar Rascunho
                                    </button>
                                    <button type="submit" name="action" value="publish"
                                        class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-4 rounded-lg font-semibold transition-colors">
                                        <i class="fas fa-rocket mr-2"></i>Publicar Evento
                                    </button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startTime = document.getElementById('horario_inicio');
            const endTime = document.getElementById('horario_fim');
            const imageInput = document.getElementById('imagem');
            const form = document.querySelector('form');

            // Auto-fill end time (+2h)
            startTime?.addEventListener('change', function() {
                if (this.value && !endTime.value) {
                    const start = new Date('2000-01-01 ' + this.value);
                    start.setHours(start.getHours() + 2);
                    endTime.value = start.toTimeString().slice(0, 5);
                }
            });

            // Image preview
            imageInput?.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        let preview = document.getElementById('image-preview');
                        if (!preview) {
                            preview = document.createElement('div');
                            preview.id = 'image-preview';
                            preview.className = 'mt-4';
                            imageInput.parentNode.appendChild(preview);
                        }
                        preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-32 h-32 object-cover rounded-lg border">
                <p class="text-sm text-gray-500 mt-2">Preview da imagem</p>`;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Confirmation for publishing
            form?.addEventListener('submit', function(e) {
                const action = e.submitter?.value;
                if (action === 'publish' && !confirm(
                        'Tem certeza que deseja publicar este evento? Ele ficará visível para todos os usuários.'
                    )) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endpush
