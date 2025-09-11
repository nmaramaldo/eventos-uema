<script setup>
import { ref } from 'vue'
import { register } from '@/services/auth'
import Logo from '@/assets/logo-uema-site.png'
import { useRouter } from 'vue-router'

const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const loading = ref(false)
const error = ref(null)

async function submit() {
  error.value = null
  loading.value = true
  try {
    await register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
    })
    // depois de cadastrar, manda para o login
    router.push({ name: 'login' })
  } catch (e) {
    error.value = e?.message || 'Não foi possível concluir o cadastro'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-[calc(100vh-8rem)] flex items-center justify-center">
    <div class="w-full max-w-md bg-white shadow-card rounded-xl p-8">
      <div class="flex items-center gap-3 justify-center mb-6">
        <img :src="Logo" alt="UEMA" class="h-10 w-auto" />
        <h1 class="text-2xl font-semibold text-gray-900">Crie sua conta</h1>
      </div>

      <p class="mt-2 text-center text-gray-500">
        Já tem uma conta?
        <RouterLink :to="{ name: 'login' }" class="text-uema-600 hover:text-uema-700 font-medium">
          Fazer login
        </RouterLink>
      </p>

      <form class="mt-6 space-y-5" @submit.prevent="submit">
        <div>
          <label class="block text-sm font-medium text-gray-700">Nome completo</label>
        <input v-model="name" type="text" required
               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-uema-500 focus:ring-uema-500"
               placeholder="Seu nome" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">E-mail</label>
          <input v-model="email" type="email" required
                 class="mt-1 block w-full rounded-lg border-gray-300 focus:border-uema-500 focus:ring-uema-500"
                 placeholder="seu@email.com" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Senha</label>
          <input v-model="password" type="password" required
                 class="mt-1 block w-full rounded-lg border-gray-300 focus:border-uema-500 focus:ring-uema-500"
                 placeholder="••••••••" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Confirmar senha</label>
          <input v-model="password_confirmation" type="password" required
                 class="mt-1 block w-full rounded-lg border-gray-300 focus:border-uema-500 focus:ring-uema-500"
                 placeholder="Repita a senha" />
        </div>

        <button :disabled="loading" type="submit"
                class="w-full inline-flex justify-center items-center h-11 rounded-lg bg-uema-600 hover:bg-uema-700 text-white font-medium transition">
          {{ loading ? 'Cadastrando...' : 'Criar conta' }}
        </button>

        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
      </form>
    </div>
  </div>
</template>
