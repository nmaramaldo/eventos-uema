<script setup>
import Logo from '@/assets/logo-uema-site.png'
</script>

<template>
  <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
    <!-- Lado esquerdo: branding -->
    <div class="hidden lg:flex flex-col justify-between bg-gradient-to-br 
    from-uema-600 to-uema-800 p-10 text-white">
    
      <div class="flex items-center gap-3">
        <img :src="Logo" alt="UEMA" class="h-10 w-auto" />
        <span class="text-lg font-semibold">Sistema de Eventos</span>
      </div>

      <div>
        <h1 class="text-3xl font-bold leading-tight">Gerencie eventos, inscrições e certificados</h1>
        <p class="mt-3 text-white/80">Feito para a comunidade acadêmica da UEMA</p>
      </div>

      <p class="text-white/60 text-sm">© UEMA — Universidade Estadual do Maranhão</p>
    </div>

    <!-- Lado direito: formulário -->
    <div class="flex items-center justify-center p-8">
      <div class="w-full max-w-md">
        <div class="lg:hidden flex items-center gap-3 mb-8">
          <img :src="Logo" alt="UEMA" class="h-8 w-auto" />
          <span class="text-lg font-semibold text-gray-900">Sistema de Eventos</span>
        </div>

        <router-view />
      </div>
    </div>
  </div>
</template>
