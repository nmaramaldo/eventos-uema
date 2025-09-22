import { createRouter, createWebHistory } from 'vue-router'

// Layouts
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue'
import OrganizerLayout from '@/layouts/OrganizerLayout.vue'

// Views
import DashboardView from '@/views/dashboard/DashboardView.vue'
import LoginView from '@/views/auth/LoginView.vue'
import RegisterView from '@/views/auth/RegisterView.vue'

// Helper para pegar o usuário atual (ajuste conforme sua app state)
function getUser() {
  return window.App?.user || null
}

const routes = [
  // Área do usuário comum (autenticada)
  {
    path: '/',
    component: DefaultLayout,
    children: [
      {
        path: '',
        name: 'dashboard',
        component: DashboardView,
        meta: { title: 'Dashboard', requiresAuth: true },
      },
      {
        path: 'eventos',
        name: 'events.list',
        component: () => import('@/views/events/EventsList.vue'),
        meta: { title: 'Eventos', requiresAuth: true },
      },
      {
        path: 'eventos/novo',
        name: 'events.create',
        component: () => import('@/views/events/EventFormPage.vue'),
        meta: { title: 'Novo Evento', requiresAuth: true },
      },
      {
        path: 'inscricoes',
        name: 'enrollments.list',
        component: () => import('@/views/enrollments/EnrollmentsList.vue'),
        meta: { title: 'Minhas Inscrições', requiresAuth: true },
      },
      {
        path: 'certificados',
        name: 'certificates.list',
        component: () => import('@/views/certificates/CertificatesList.vue'),
        meta: { title: 'Certificados', requiresAuth: true },
      },
    ],
  },

  // Área do ORGANIZADOR
  {
    path: '/organizador',
    component: OrganizerLayout,
    children: [
      {
        path: '',
        name: 'org.dashboard',
        component: () => import('@/views/organizer/OrganizerDashboard.vue'),
        meta: { title: 'Painel do Organizador', requiresAuth: true, requiresOrganizer: true },
      },
      {
        path: 'meus-eventos',
        name: 'org.events',
        component: () => import('@/views/organizer/OrganizerEventsList.vue'),
        meta: { title: 'Meus Eventos', requiresAuth: true, requiresOrganizer: true },
      },
      {
        path: 'meus-eventos/novo',
        name: 'org.events.create',
        component: () => import('@/views/events/EventFormPage.vue'),
        meta: { title: 'Criar Evento', requiresAuth: true, requiresOrganizer: true },
      },
      {
        path: 'inscritos/:eventId',
        name: 'org.attendees',
        component: () => import('@/views/organizer/OrganizerAttendees.vue'),
        meta: { title: 'Inscritos', requiresAuth: true, requiresOrganizer: true },
      },
    ],
  },

  // Auth
  {
    path: '/login',
    component: AuthLayout,
    children: [
      {
        path: '',
        name: 'login',
        component: LoginView,
        meta: { title: 'Login' },
      },
    ],
  },
  {
    path: '/register',
    component: AuthLayout,
    children: [
      {
        path: '',
        name: 'register',
        component: RegisterView,
        meta: { title: 'Cadastro' },
      },
    ],
  },

  // Fallback
  { path: '/:pathMatch(.*)*', redirect: { name: 'dashboard' } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior: () => ({ top: 0 }),
})

// Guard de navegação
router.beforeEach((to) => {
  const user = getUser()
  const isAuth = !!user
  const role = (user?.role || '').toLowerCase()

  // Precisa estar logado?
  if (to.meta?.requiresAuth && !isAuth) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  // Já logado não acessa login/cadastro
  if ((to.name === 'login' || to.name === 'register') && isAuth) {
    return { name: 'dashboard' }
  }

  // Precisa ser organizador?
  if (to.meta?.requiresOrganizer && role !== 'organizer') {
    return { name: 'dashboard' }
  }
})

// Título da página
router.afterEach((to) => {
  if (to.meta?.title) {
    document.title = `${to.meta.title} - Sistema de Eventos`
  }
})

export default router
