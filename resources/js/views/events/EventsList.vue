<template>
  <div class="p-6 max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Lista de Eventos</h1>
      <button
        @click="goToCreate"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
      >
        + Novo Evento
      </button>
    </div>

    <table class="w-full border border-gray-300 rounded">
      <thead class="bg-gray-100">
        <tr>
          <th class="text-left p-3 border-b">Título</th>
          <th class="text-left p-3 border-b">Data</th>
          <th class="text-left p-3 border-b">Local</th>
          <th class="p-3 border-b text-center">Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="evento in eventos" :key="evento.id" class="border-b hover:bg-gray-50">
          <td class="p-3">{{ evento.title }}</td>
          <td class="p-3">{{ formatDate(evento.date) }}</td>
          <td class="p-3">{{ evento.location }}</td>
          <td class="p-3 text-center space-x-2">
            <button
              @click="editEvent(evento.id)"
              class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600"
            >
              Editar
            </button>
            <button
              @click="deleteEvent(evento.id)"
              class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
            >
              Excluir
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <p v-if="eventos.length === 0" class="text-gray-500 mt-4">Nenhum evento cadastrado.</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const eventos = ref([])
const router = useRouter()

const loadEvents = async () => {
  try {
    const { data } = await axios.get('/api/events')
    eventos.value = data
  } catch (error) {
    console.error('Erro ao carregar eventos:', error)
  }
}

const goToCreate = () => {
  router.push('/events/create')
}

const editEvent = (id) => {
  router.push(`/events/${id}/edit`)
}

const deleteEvent = async (id) => {
  if (confirm('Tem certeza que deseja excluir este evento?')) {
    try {
      await axios.delete(`/api/events/${id}`)
      eventos.value = eventos.value.filter(e => e.id !== id)
    } catch (error) {
      console.error('Erro ao excluir evento:', error)
    }
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

onMounted(loadEvents)
</script>

