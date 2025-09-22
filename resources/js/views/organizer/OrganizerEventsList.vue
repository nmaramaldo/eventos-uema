<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-bold">Meus eventos</h2>
      <RouterLink
        :to="{ name: 'org.events.create' }"
        class="px-3 py-2 rounded-lg bg-uema-600 text-white hover:bg-uema-700"
      >
        Novo evento
      </RouterLink>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
      <p class="text-gray-600">

    </p>
    </div>
  </div>
</template>
