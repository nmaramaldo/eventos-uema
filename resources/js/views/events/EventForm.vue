<template>
  <form @submit.prevent="handleSubmit" class="space-y-8">
    <!-- Informações Básicas -->
    <div class="bg-white p-6 rounded-lg shadow-card">
      <h3 class="text-lg font-semibold text-gray-900 mb-6">Informações Básicas</h3>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Título do Evento -->
        <div class="lg:col-span-2">
          <BaseInput
            v-model="form.title"
            label="Título do Evento"
            placeholder="Ex: Workshop de Desenvolvimento Web"
            required
            :error-message="errors.title"
            maxlength="255"
            show-counter
          />
        </div>

        <!-- Descrição Curta -->
        <div class="lg:col-span-2">
          <BaseInput
            v-model="form.shortDescription"
            label="Descrição Curta"
            type="textarea"
            :rows="3"
            placeholder="Descrição resumida do evento (aparece nas listagens)"
            :error-message="errors.shortDescription"
            maxlength="500"
            show-counter
          />
        </div>

        <!-- Categoria -->
        <BaseInput
          v-model="form.category"
          label="Categoria"
          type="select"
          required
          placeholder="Selecione uma categoria"
          :options="categoryOptions"
          :error-message="errors.category"
        />

        <!-- Tipo do Evento -->
        <BaseInput
          v-model="form.type"
          label="Modalidade"
          type="select"
          required
          placeholder="Selecione a modalidade"
          :options="typeOptions"
          :error-message="errors.type"
          @change="handleTypeChange"
        />

        <!-- Status -->
        <BaseInput
          v-model="form.status"
          label="Status"
          type="select"
          required
          :options="statusOptions"
          :error-message="errors.status"
        />

        <!-- Máximo de Participantes -->
        <BaseInput
          v-model.number="form.maxParticipants"
          label="Máximo de Participantes"
          type="number"
          min="1"
          placeholder="0 = Ilimitado"
          help-text="Deixe em branco ou 0 para ilimitado"
          :error-message="errors.maxParticipants"
        />
      </div>
    </div>

    <!-- Data e Horário -->
    <div class="bg-white p-6 rounded-lg shadow-card">
      <h3 class="text-lg font-semibold text-gray-900 mb-6">Data e Horário</h3>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Data de Início -->
        <BaseInput
          v-model="form.startDate"
          label="Data de Início"
          type="date"
          required
          :error-message="errors.startDate"
        />

        <!-- Data de Término -->
        <BaseInput
          v-model="form.endDate"
          label="Data de Término"
          type="date"
          required
          :error-message="errors.endDate"
        />

        <!-- Horário de Início -->
        <BaseInput
          v-model="form.startTime"
          label="Horário de Início"
          type="time"
          required
          :error-message="errors.startTime"
        />

        <!-- Horário de Término -->
        <BaseInput
          v-model="form.endTime"
          label="Horário de Término"
          type="time"
          required
          :error-message="errors.endTime"
        />

        <!-- Período de Inscrições - Início -->
        <BaseInput
          v-model="form.registrationStart"
          label="Início das Inscrições"
          type="datetime-local"
          :error-message="errors.registrationStart"
        />

        <!-- Período de Inscrições - Fim -->
        <BaseInput
          v-model="form.registrationEnd"
          label="Fim das Inscrições"
          type="datetime-local"
          :error-message="errors.registrationEnd"
        />
      </div>
    </div>

    <!-- Local/Online -->
    <div class="bg-white p-6 rounded-lg shadow-card">
      <h3 class="text-lg font-semibold text-gray-900 mb-6">
        {{ form.type === 'online' ? 'Configurações Online' : 'Local do Evento' }}
      </h3>

      <!-- Campos para Evento Presencial -->
      <div v-if="form.type === 'presencial'" class="space-y-6">
        <BaseInput
          v-model="form.location"
          label="Nome do Local"
          placeholder="Ex: Auditório Central da UEMA"
          required
          :error-message="errors.location"
        />

        <BaseInput
          v-model="form.address"
          label="Endereço Completo"
          type="textarea"
          :rows="3"
          placeholder="Rua, número, bairro, cidade, estado, CEP"
          required
          :error-message="errors.address"
        />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <BaseInput
            v-model="form.latitude"
            label="Latitude (opcional)"
            type="number"
            step="any"
            placeholder="-2.5297"
            help-text="Para exibir no mapa"
          />

          <BaseInput
            v-model="form.longitude"
            label="Longitude (opcional)"
            type="number"
            step="any"
            placeholder="-44.3028"
            help-text="Para exibir no mapa"
          />
        </div>
      </div>

      <!-- Campos para Evento Online -->
      <div v-if="form.type === 'online'" class="space-y-6">
        <BaseInput
          v-model="form.onlineLink"
          label="Link da Transmissão"
          type="url"
          placeholder="https://meet.google.com/abc-defg-hij"
          required
          :error-message="errors.onlineLink"
          help-text="Link do Zoom, Google Meet, YouTube, etc."
        />

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <BaseInput
            v-model="form.meetingId"
            label="ID da Reunião (opcional)"
            placeholder="123-456-789"
            help-text="ID para Zoom, Teams, etc."
          />

          <BaseInput
            v-model="form.meetingPassword"
            label="Senha da Reunião (opcional)"
            placeholder="senha123"
            help-text="Senha de acesso à reunião"
          />
        </div>
      </div>

      <!-- Campos para Evento Híbrido -->
      <div v-if="form.type === 'hibrido'" class="space-y-6">
        <!-- Local Presencial -->
        <div class="border-l-4 border-uema-500 pl-4">
          <h4 class="font-medium text-gray-900 mb-4">Presencial</h4>

          <BaseInput
            v-model="form.location"
            label="Nome do Local"
            placeholder="Ex: Auditório Central da UEMA"
            required
            :error-message="errors.location"
          />

          <BaseInput
            v-model="form.address"
            label="Endereço Completo"
            type="textarea"
            :rows="2"
            placeholder="Rua, número, bairro, cidade, estado, CEP"
            required
            :error-message="errors.address"
            class="mt-4"
          />
        </div>

        <!-- Transmissão Online -->
        <div class="border-l-4 border-success-500 pl-4">
          <h4 class="font-medium text-gray-900 mb-4">Online</h4>

          <BaseInput
            v-model="form.onlineLink"
            label="Link da Transmissão"
            type="url"
            placeholder="https://meet.google.com/abc-defg-hij"
            required
            :error-message="errors.onlineLink"
          />
        </div>
      </div>
    </div>

    <!-- Descrição Detalhada -->
    <div class="bg-white p-6 rounded-lg shadow-card">
      <h3 class="text-lg font-semibold text-gray-900 mb-6">Descrição Detalhada</h3>

      <BaseInput
        v-model="form.description"
        type="textarea"
        :rows="8"
        placeholder="Descreva detalhadamente o evento, objetivos, público-alvo, metodologia, etc."
        :error-message="errors.description"
      />
    </div>

    <!-- Configurações de Inscrição -->
    <div class="bg-white p-6 rounded-lg shadow-card">
      <h3 class="text-lg font-semibold text-gray-900 mb-6">Configurações de Inscrição</h3>

      <div class="space-y-6">
        <!-- Evento Gratuito/Pago -->
        <div class="flex items-center space-x-3">
          <input
            id="is-free"
            v-model="form.isFree"
            type="checkbox"
            class="h-4 w-4 text-uema-600 focus:ring-uema-500 border-gray-300 rounded"
          />
          <label for="is-free" class="text-sm font-medium text-gray-700">
            Evento gratuito
          </label>
        </div>

        <!-- Preço (se não for gratuito) -->
        <div v-if="!form.isFree" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <BaseInput
            v-model.number="form.price"
            label="Preço (R$)"
            type="number"
            step="0.01"
            min="0"
            placeholder="0.00"
            :error-message="errors.price"
          />
        </div>

        <!-- Requer Aprovação -->
        <div class="flex items-center space-x-3">
          <input
            id="requires-approval"
            v-model="form.requiresApproval"
            type="checkbox"
            class="h-4 w-4 text-uema-600 focus:ring-uema-500 border-gray-300 rounded"
          />
          <label for="requires-approval" class="text-sm font-medium text-gray-700">
            Inscrições requerem aprovação
          </label>
        </div>
      </div>
    </div>

    <!-- Banner do Evento -->
    <div class="bg-white p-6 rounded-lg shadow-card">
      <h3 class="text-lg font-semibold text-gray-900 mb-6">Banner do Evento</h3>

      <div class="space-y-4">
        <!-- Upload de banner -->
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
          <input
            ref="bannerInput"
            type="file"
            accept="image/*"
            class="hidden"
            @change="handleBannerUpload"
          />

          <!-- Preview do banner -->
          <div v-if="bannerPreview" class="mb-4">
            <img
              :src="bannerPreview"
              alt="Preview do banner"
              class="mx-auto max-h-48 rounded-lg shadow-sm"
            />
          </div>

          <div class="space-y-2">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
              <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>

            <div class="text-sm text-gray-600">
              <BaseButton
                variant="outline"
                size="sm"
                @click="triggerBannerFile"
              >
                Selecionar Banner
              </BaseButton>
              <p class="mt-2">PNG, JPG, JPEG até 5MB</p>
              <p class="text-xs text-gray-500">Tamanho recomendado: 1200x600px</p>
            </div>
          </div>
        </div>

        <!-- Remover banner -->
        <div v-if="bannerPreview" class="text-center">
          <BaseButton
            variant="danger"
            size="sm"
            @click="removeBanner"
          >
            Remover Banner
          </BaseButton>
        </div>
      </div>
    </div>

    <!-- Tags -->
    <div class="bg-white p-6 rounded-lg shadow-card">
      <h3 class="text-lg font-semibold text-gray-900 mb-6">Tags</h3>

      <div class="space-y-4">
        <!-- Input para adicionar tags -->
        <div class="flex space-x-2">
          <BaseInput
            v-model="newTag"
            placeholder="Digite uma tag e pressione Enter"
            @keydown.enter.prevent="addTag"
          />
          <BaseButton
            type="button"
            variant="outline"
            @click="addTag"
          >
            Adicionar
          </BaseButton>
        </div>

        <!-- Lista de tags -->
        <div v-if="form.tags.length > 0" class="flex flex-wrap gap-2">
          <span
            v-for="(tag, index) in form.tags"
            :key="index"
            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-uema-100 text-uema-800"
          >
            {{ tag }}
            <button
              type="button"
              class="ml-2 text-uema-600 hover:text-uema-800"
              @click="removeTag(index)"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </span>
        </div>
      </div>
    </div>

    <!-- Botões de Ação -->
    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
      <BaseButton
        variant="outline"
        @click="$emit('cancel')"
      >
        Cancelar
      </BaseButton>

      <BaseButton
        type="submit"
        variant="primary"
        :loading="loading"
      >
        {{ isEditing ? 'Atualizar Evento' : 'Criar Evento' }}
      </BaseButton>
    </div>
  </form>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import BaseInput from '@/components/common/BaseInput.vue'
import BaseButton from '@/components/common/BaseButton.vue'

const props = defineProps({
  initialData: {
    type: Object,
    default: () => ({})
  },
  loading: {
    type: Boolean,
    default: false
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const emit = defineEmits(['submit', 'cancel'])

// Verificar se está editando
const isEditing = computed(() => !!props.initialData.id)

// Dados do formulário
const form = reactive({
  title: '',
  shortDescription: '',
  description: '',
  category: '',
  type: 'presencial',
  status: 'draft',
  startDate: '',
  endDate: '',
  startTime: '',
  endTime: '',
  registrationStart: '',
  registrationEnd: '',
  maxParticipants: null,
  location: '',
  address: '',
  latitude: null,
  longitude: null,
  onlineLink: '',
  meetingId: '',
  meetingPassword: '',
  price: 0,
  isFree: true,
  requiresApproval: false,
  bannerImage: null,
  tags: [],
  ...props.initialData
})

// Preview do banner
const bannerPreview = ref(props.initialData.bannerUrl || null)
const bannerInput = ref(null)
const newTag = ref('')

// Opções dos selects
const categoryOptions = [
  { value: 'academico', label: 'Acadêmico' },
  { value: 'cientifico', label: 'Científico' },
  { value: 'cultural', label: 'Cultural' },
  { value: 'institucional', label: 'Institucional' },
  { value: 'extensao', label: 'Extensão' },
  { value: 'pesquisa', label: 'Pesquisa' },
  { value: 'workshop', label: 'Workshop' },
  { value: 'palestra', label: 'Palestra' },
  { value: 'congresso', label: 'Congresso' },
  { value: 'seminario', label: 'Seminário' }
]

const typeOptions = [
  { value: 'presencial', label: 'Presencial' },
  { value: 'online', label: 'Online' },
  { value: 'hibrido', label: 'Híbrido' }
]

const statusOptions = [
  { value: 'draft', label: 'Rascunho' },
  { value: 'published', label: 'Publicado' },
  { value: 'cancelled', label: 'Cancelado' },
  { value: 'completed', label: 'Concluído' }
]

// Handlers
const handleTypeChange = () => {
  // Limpar campos quando mudar o tipo
  if (form.type === 'online') {
    form.location = ''
    form.address = ''
    form.latitude = null
    form.longitude = null
  } else if (form.type === 'presencial') {
    form.onlineLink = ''
    form.meetingId = ''
    form.meetingPassword = ''
  }
}

function triggerBannerFile() {
  bannerInput.value?.click()
}

const handleBannerUpload = (event) => {
  const file = event.target.files?.[0]
  if (file) {
    // Validar tipo e tamanho
    if (!file.type.startsWith('image/')) {
      alert('Por favor, selecione apenas imagens.')
      return
    }

    if (file.size > 5 * 1024 * 1024) { // 5MB
      alert('A imagem deve ter no máximo 5MB.')
      return
    }

    form.bannerImage = file

    // Criar preview
    const reader = new FileReader()
    reader.onload = (e) => {
      bannerPreview.value = e.target?.result
    }
    reader.readAsDataURL(file)
  }
}

const removeBanner = () => {
  form.bannerImage = null
  bannerPreview.value = null
  if (bannerInput.value) {
    bannerInput.value.value = ''
  }
}

const addTag = () => {
  const tag = newTag.value.trim()
  if (tag && !form.tags.includes(tag)) {
    form.tags.push(tag)
    newTag.value = ''
  }
}

const removeTag = (index) => {
  form.tags.splice(index, 1)
}

const handleSubmit = () => {
  emit('submit', { ...form })
}

// Watch para evento gratuito
watch(() => form.isFree, (isFree) => {
  if (isFree) form.price = 0
})
</script>
