<template>
  <div class="space-y-4">
    <h2 class="text-2xl font-bold">Inscritos do evento {{ $route.params.eventId }}</h2>

    <div class="bg-white rounded-xl shadow p-6">
      <p class="text-gray-600">
        Tabela de inscritos com nome, e-mail, status (aprovado/pendente), ações (aprovar, recusar, marcar presença).
      </p>
    </div>
  </div>
</template>
