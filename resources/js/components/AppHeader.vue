<script setup>
import Logo from '@/assets/logo-uema-site.png'
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { logout } from '@/services/auth'

const router = useRouter()
const user = computed(() => window.App?.user || null)

// Nome a exibir e inicial do avatar
const displayName = computed(() => user.value?.name || user.value?.email || 'Usuário')
const avatarInitial = computed(() => (displayName.value?.[0] || 'U').toUpperCase())

async function doLogout() {
  await logout()
  window.App.user = null
  router.push({ name: 'login' })
}

// Dropdown
const menuOpen = ref(false)
const menuRef = ref(null)
const menuBtnRef = ref(null)

function toggleMenu() {
  menuOpen.value = !menuOpen.value
}

function closeMenu() {
  menuOpen.value = false
}

function onClickOutside(e) {
  if (!menuOpen.value) return
  const el = menuRef.value
  const btn = menuBtnRef.value
  if (el && !el.contains(e.target) && btn && !btn.contains(e.target)) {
    closeMenu()
  }
}

function onKeydown(e) {
  if (!menuOpen.value) return
  if (e.key === 'Escape') {
    e.stopPropagation()
    closeMenu()
    menuBtnRef.value?.focus?.()
  }
}

onMounted(() => {
  document.addEventListener('click', onClickOutside)
  document.addEventListener('keydown', onKeydown)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', onClickOutside)
  document.removeEventListener('keydown', onKeydown)
})
</script>

<template>
  <header class="border-b bg-white/80 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <!-- Logo + link inicial -->
      <RouterLink :to="{ name: 'dashboard' }" class="flex items-center gap-3">
        <img :src="Logo" alt="UEMA" class="h-8 w-auto" />
        <span class="font-semibold text-gray-900">Sistema de Eventos</span>
      </RouterLink>

      <nav class="flex items-center gap-6">
        <!-- Dashboard -->
        <RouterLink
          :to="{ name: 'dashboard' }"
          class="text-sm font-medium text-gray-600 hover:text-uema-600"
          :class="{ 'text-uema-700': $route.name === 'dashboard' }"
        >
          Dashboard
        </RouterLink>

        <!-- Links visíveis só se o usuário estiver logado -->
        <template v-if="user">
          <RouterLink
            :to="{ name: 'events.list' }"
            class="text-sm font-medium text-gray-600 hover:text-uema-600"
            :class="{ 'text-uema-700': $route.name?.startsWith('events.') }"
          >
            Eventos
          </RouterLink>

          <RouterLink
            :to="{ name: 'enrollments.list' }"
            class="text-sm font-medium text-gray-600 hover:text-uema-600"
            :class="{ 'text-uema-700': $route.name === 'enrollments.list' }"
          >
            Minhas inscrições
          </RouterLink>

          <RouterLink
            :to="{ name: 'certificates.list' }"
            class="text-sm font-medium text-gray-600 hover:text-uema-600"
            :class="{ 'text-uema-700': $route.name === 'certificates.list' }"
          >
            Certificados
          </RouterLink>

          <!-- Link do Organizador (apenas para quem tem role === 'organizer') -->
          <RouterLink
            v-if="user?.role === 'organizer'"
            :to="{ name: 'org.dashboard' }"
            class="text-sm font-medium text-gray-600 hover:text-uema-600"
            :class="{ 'text-uema-700': $route.name?.toString().startsWith('org.') }"
          >
            Organizador
          </RouterLink>
        </template>

        <!-- Se não estiver logado, mostra Login -->
        <template v-else>
          <RouterLink
            :to="{ name: 'login' }"
            class="text-sm font-medium text-gray-600 hover:text-uema-600"
            :class="{ 'text-uema-700': $route.name === 'login' }"
          >
            Login
          </RouterLink>
        </template>

        <!-- Usuário + Dropdown -->
        <template v-if="user">
          <div class="relative" ref="menuRef">
            <button
              ref="menuBtnRef"
              @click="toggleMenu"
              class="flex items-center gap-3 rounded-full focus:outline-none focus:ring-2 focus:ring-uema-500"
              aria-haspopup="menu"
              :aria-expanded="menuOpen ? 'true' : 'false'"
            >
              <!-- Avatar com inicial -->
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-uema-100 text-uema-800 text-sm font-semibold">
                {{ avatarInitial }}
              </span>
              <span class="hidden sm:block text-sm text-gray-700">
                Olá, {{ displayName }}
              </span>
              <svg class="h-4 w-4 text-gray-500 hidden sm:block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
              </svg>
            </button>

            <!-- Menu -->
            <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
              <div
                v-if="menuOpen"
                class="absolute right-0 z-20 mt-2 w-56 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-black/5"
                role="menu"
                aria-label="Menu do usuário"
              >
                <div class="px-4 py-3 border-b">
                  <p class="text-sm font-medium text-gray-900 truncate">{{ displayName }}</p>
                  <p v-if="user?.email" class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                </div>

                <div class="py-1">
                  <!-- Exemplo de link de perfil (placeholder para futura rota) -->
                  <button
                    type="button"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                    @click="() => { closeMenu(); router.push({ name: 'dashboard' }) }"
                    role="menuitem"
                  >
                    Meu perfil
                  </button>

                  <!-- Atalho para área do Organizador (se for organizer) -->
                  <button
                    v-if="user?.role === 'organizer'"
                    type="button"
                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                    @click="() => { closeMenu(); router.push({ name: 'org.dashboard' }) }"
                    role="menuitem"
                  >
                    Área do Organizador
                  </button>

                  <div class="my-1 border-t"></div>

                  <button
                    type="button"
                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                    @click="() => { closeMenu(); doLogout() }"
                    role="menuitem"
                  >
                    Sair
                  </button>
                </div>
              </div>
            </transition>
          </div>
        </template>
      </nav>
    </div>
  </header>
</template>
