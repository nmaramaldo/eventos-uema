<script setup>
import AppHeader from '@components/AppHeader.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <AppHeader />

    <main class="flex-1">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
        <router-view />
      </div>
    </main>
  </div>
</template>
