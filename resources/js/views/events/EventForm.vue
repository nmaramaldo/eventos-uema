<template>
  <div class="space-y-8">
    <!-- Navegação entre abas -->
    <div class="flex space-x-4 border-b border-gray-200 pb-2">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        class="px-4 py-2 rounded-t-md text-sm font-medium"
        :class="tab.key === activeTab
          ? 'bg-white border-t border-l border-r border-gray-200 text-uema-600'
          : 'text-gray-500 hover:text-uema-600'"
        @click="activeTab = tab.key"
      >
        {{ tab.label }}
      </button>
    </div>

    <!-- Conteúdo da aba -->
    <component
      :is="currentComponent"
      v-model="form"
      :errors="errors"
      @submit="handleSubmit"
    />

    <!-- Botões finais -->
    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
      <BaseButton variant="outline" @click="$emit('cancel')">Cancelar</BaseButton>
      <BaseButton type="submit" variant="primary" :loading="loading" @click="handleSubmit">
        {{ isEditing ? 'Atualizar Evento' : 'Criar Evento' }}
      </BaseButton>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import BaseButton from '@/components/common/BaseButton.vue'
import EventBasicInfo from './EventBasicInfo.vue'
import EventProgram from './EventProgram.vue'
import EventSpeakers from './EventSpeakers.vue'

const props = defineProps({
  initialData: { type: Object, default: () => ({}) },
  loading: Boolean,
  errors: { type: Object, default: () => ({}) },
})

const emit = defineEmits(['submit', 'cancel'])
const isEditing = computed(() => !!props.initialData.id)

// tabs
const tabs = [
  { key: 'basic', label: 'Eventos e Inscrições', component: EventBasicInfo },
  { key: 'program', label: 'Programação', component: EventProgram },
  { key: 'speakers', label: 'Palestrantes', component: EventSpeakers },
]

const activeTab = ref('basic')
const currentComponent = computed(() => tabs.find(t => t.key === activeTab.value).component)

const form = reactive({
  title: '',
  shortDescription: '',
  description: '',
  startDate: '',
  endDate: '',
  registrationStart: '',
  registrationEnd: '',
  type: 'presencial',
  isFree: true,
  price: 0,
  bannerImage: null,
  bannerUrl: props.initialData.bannerUrl || null,
  tags: [],
  ...props.initialData
})

function handleSubmit() {
  emit('submit', { ...form })
}
</script>
