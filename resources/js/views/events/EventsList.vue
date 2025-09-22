<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Eventos</h1>
      <RouterLink
        :to="{ name: 'events.create' }"
        class="px-3 py-2 rounded-lg bg-uema-600 text-white hover:bg-uema-700"
      >
        Novo Evento
      </RouterLink>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
      <p class="text-gray-600">Aqui será exibida a listagem de eventos.</p>
    </div>
  </div>
</template>
