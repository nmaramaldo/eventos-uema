<script setup>
import { ref } from 'vue'
import { login } from '@/services/auth'
import Logo from '@/assets/logo-uema-site.png'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const remember = ref(false)
const loading = ref(false)
const error = ref(null)
const router = useRouter()

async function submit() {
  error.value = null
  loading.value = true
  try {
    await login({ email: email.value, password: password.value, remember: remember.value })
    // Evita flicker até o backend popular o usuário em um próximo load
    window.App.user = { name: email.value }
    router.push({ name: 'dashboard' })
  } catch (e) {
    error.value = e?.message || 'Não foi possível entrar'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-[calc(100vh-8rem)] flex items-center justify-center">
    <div class="w-full max-w-md bg-white shadow-card rounded-xl p-8">
      <!-- Logo + título -->
      <div class="flex flex-col items-center text-center mb-6 space-y-2">
        <img :src="Logo" alt="UEMA" class="h-12 sm:h-16 w-auto" />
        <h1 class="text-2xl font-semibold text-gray-900">Acesse sua conta</h1>
        <p class="text-sm text-gray-500">Use seu e-mail institucional para acessar</p>
      </div>

      <!-- Link de cadastro -->
      <p class="mt-2 text-center text-gray-500">
        Ainda não tem conta?
        <RouterLink
          :to="{ name: 'register' }"
          class="text-uema-600 hover:text-uema-700 font-medium"
        >
          Crie uma agora
        </RouterLink>
      </p>

      <!-- Formulário -->
      <form class="mt-6 space-y-5" @submit.prevent="submit">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-uema-500 focus:ring-uema-500"
            placeholder="seu@email.com"
            autocomplete="email"
          />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-uema-500 focus:ring-uema-500"
            placeholder="••••••••"
            autocomplete="current-password"
          />
        </div>

        <div class="flex items-center justify-between">
          <label class="inline-flex items-center gap-2 text-sm text-gray-600">
            <input
              type="checkbox"
              v-model="remember"
              class="rounded border-gray-300 text-uema-600 focus:ring-uema-500"
            />
            Lembrar-me
          </label>

          <a class="text-sm text-uema-600 hover:text-uema-700" href="/forgot-password">
            Esqueceu a senha?
          </a>
        </div>

        <button
          :disabled="loading"
          type="submit"
          class="w-full inline-flex justify-center items-center h-11 rounded-lg bg-uema-600 hover:bg-uema-700 text-white font-medium transition disabled:opacity-60"
        >
          {{ loading ? 'Entrando...' : 'Entrar' }}
        </button>

        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
      </form>
    </div>
  </div>
</template>
