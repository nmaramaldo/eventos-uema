<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Topbar -->
    <header class="h-14 bg-white border-b flex items-center px-4 justify-between">
      <h1 class="font-semibold">Área do Organizador</h1>
      <RouterLink :to="{ name: 'dashboard' }" class="text-sm text-uema-600 hover:text-uema-700">
        Voltar ao sistema
      </RouterLink>
    </header>

    <div class="flex">
      <!-- Sidebar -->
      <aside class="w-64 bg-white border-r min-h-[calc(100vh-3.5rem)] p-4 space-y-2">
        <RouterLink
          :to="{ name: 'org.dashboard' }"
          class="block px-3 py-2 rounded hover:bg-gray-100"
          :class="{ 'bg-gray-100 font-medium': $route.name === 'org.dashboard' }"
        >
          Painel
        </RouterLink>

        <RouterLink
          :to="{ name: 'org.events' }"
          class="block px-3 py-2 rounded hover:bg-gray-100"
          :class="{ 'bg-gray-100 font-medium': $route.name?.toString().startsWith('org.events') }"
        >
          Meus eventos
        </RouterLink>
      </aside>

      <!-- Conteúdo -->
      <main class="flex-1 p-6">
        <RouterView />
      </main>
    </div>
  </div>
</template>
