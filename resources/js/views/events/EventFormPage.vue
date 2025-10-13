<template>
  <form @submit.prevent="submitForm">
    <div class="tabs mb-4">
      <button
        v-for="(tab, index) in tabs"
        :key="tab"
        @click="activeTab = index"
        :class="['px-4 py-2', activeTab === index ? 'bg-blue-600 text-white' : 'bg-gray-100']"
      >
        {{ tab }}
      </button>
    </div>

    <component
      :is="tabComponents[activeTab]"
      v-model="localEvent"
    />

    <div class="flex justify-end mt-6">
      <button
        type="submit"
        class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700"
      >
        {{ isEditing ? 'Salvar Alterações' : 'Criar Evento' }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import EventBasicInfo from './EventBasicInfo.vue'
import EventProgram from './EventProgram.vue'
import EventSpeakers from './EventSpeakers.vue'

const props = defineProps({
  initialEvent: Object,
  isEditing: Boolean
})
const emit = defineEmits(['submit'])

const localEvent = ref({ ...props.initialEvent })
const activeTab = ref(0)

const tabs = ['Informações Básicas', 'Programação', 'Palestrantes']
const tabComponents = [EventBasicInfo, EventProgram, EventSpeakers]

watch(() => props.initialEvent, (newVal) => {
  localEvent.value = { ...newVal }
})

const submitForm = () => {
  emit('submit', localEvent.value)
}
</script>

