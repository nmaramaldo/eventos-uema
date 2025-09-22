<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">
        {{ isEditing ? 'Editar Evento' : 'Novo Evento' }}
      </h1>
    </div>

    <div class="bg-white rounded-xl shadow-card p-6">
      <EventForm
        :initial-data="initialData"
        :loading="loading"
        :errors="errors"
        @submit="onSubmit"
        @cancel="onCancel"
      />
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import EventForm from '@/views/events/EventForm.vue'

const router = useRouter()
const route = useRoute()

// Edição se tiver ID na rota (ex: /eventos/:id/editar)
const isEditing = computed(() => Boolean(route.params?.id))

// Mock inicial (se edição, poderia vir de API)
const initialData = reactive({})

// Estado
const loading = ref(false)
const errors = reactive({})

// Handlers
async function onSubmit(payload) {
  try {
    loading.value = true
    resetErrors()

    // Aqui seria a chamada da API (create/update)
    await fakeRequest(1000)

    router.push({ name: 'events.list' })
  } catch (e) {
    console.error(e)
    // se API retornar erros: errors.title = 'mensagem'
  } finally {
    loading.value = false
  }
}

function onCancel() {
  router.push({ name: 'events.list' })
}

function resetErrors() {
  Object.keys(errors).forEach(k => delete errors[k])
}

// Mock request
function fakeRequest(ms) {
  return new Promise(resolve => setTimeout(resolve, ms))
}
</script>
