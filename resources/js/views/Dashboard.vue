<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">
              Visão geral do Sistema de Eventos UEMA
            </p>
          </div>
          
          <div class="flex items-center space-x-4">
            <!-- Período de filtro -->
            <BaseInput
              v-model="selectedPeriod"
              type="select"
              :options="periodOptions"
              size="sm"
              @change="loadDashboardData"
            />
            
            <!-- Botão de exportar -->
            <BaseButton
              variant="outline"
              size="sm"
              :loading="exporting"
              @click="exportReport"
            >
              <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
              </svg>
              Exportar
            </BaseButton>

            <!-- Botão de atualizar -->
            <BaseButton
              variant="ghost"
              size="sm"
              :loading="loading"
              @click="loadDashboardData"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </BaseButton>
          </div>
        </div>
      </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Loading State -->
      <div v-if="loading && !dashboardData" class="flex justify-center items-center h-64">
        <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-uema-600"></div>
      </div>

      <div v-else class="space-y-8">
        <!-- Cards de Estatísticas Principais -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <StatsCard
            title="Total de Eventos"
            :value="dashboardData?.stats?.totalEvents || 0"
            :change="dashboardData?.stats?.eventsChange || 0"
            icon="calendar"
            color="blue"
          />
          
          <StatsCard
            title="Usuários Ativos"
            :value="dashboardData?.stats?.activeUsers || 0"
            :change="dashboardData?.stats?.usersChange || 0"
            icon="users"
            color="green"
          />
          
          <StatsCard
            title="Inscrições"
            :value="dashboardData?.stats?.totalRegistrations || 0"
            :change="dashboardData?.stats?.registrationsChange || 0"
            icon="user-plus"
            color="purple"
          />
          
          <StatsCard
            title="Certificados"
            :value="dashboardData?.stats?.totalCertificates || 0"
            :change="dashboardData?.stats?.certificatesChange || 0"
            icon="award"
            color="yellow"
          />
        </div>

        <!-- Gráficos Principais -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Gráfico de Eventos -->
          <div class="bg-white p-6 rounded-lg shadow-card">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-lg font-semibold text-gray-900">Eventos por Mês</h3>
              <div class="flex space-x-2">
                <button
                  v-for="period in ['3m', '6m', '1y']"
                  :key="period"
                  class="px-3 py-1 text-xs font-medium rounded-md"
                  :class="selectedChartPeriod === period 
                    ? 'bg-uema-100 text-uema-700' 
                    : 'text-gray-500 hover:text-gray-700'"
                  @click="updateChartPeriod(period)"
                >
                  {{ period.toUpperCase() }}
                </button>
              </div>
            </div>
            <EventsChart 
              :data="chartsData?.eventsChart"
              :loading="loadingCharts"
            />
          </div>

          <!-- Gráfico de Inscrições -->
          <div class="bg-white p-6 rounded-lg shadow-card">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Inscrições por Período</h3>
            <RegistrationsChart 
              :data="chartsData?.registrationsChart"
              :loading="loadingCharts"
            />
          </div>
        </div>

        <!-- Distribuição por Categorias e Tipos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Eventos por Categoria -->
          <div class="bg-white p-6 rounded-lg shadow-card">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Eventos por Categoria</h3>
            <div class="space-y-4">
              <div
                v-for="category in dashboardData?.eventsByCategory || []"
                :key="category.name"
                class="flex items-center justify-between"
              >
                <div class="flex items-center">
                  <div 
                    class="w-3 h-3 rounded-full mr-3"
                    :style="{ backgroundColor: category.color }"
                  ></div>
                  <span class="text-sm font-medium text-gray-900">
                    {{ category.name }}
                  </span>
                </div>
                <div class="flex items-center space-x-2">
                  <span class="text-sm text-gray-500">
                    {{ category.count }}
                  </span>
                  <div class="w-20 h-2 bg-gray-200 rounded-full">
                    <div 
                      class="h-2 rounded-full"
                      :style="{ 
                        backgroundColor: category.color,
                        width: `${category.percentage}%` 
                      }"
                    ></div>
                  </div>
                  <span class="text-xs text-gray-500 w-8 text-right">
                    {{ category.percentage }}%
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Eventos por Tipo -->
          <div class="bg-white p-6 rounded-lg shadow-card">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Modalidades de Eventos</h3>
            <div class="grid grid-cols-3 gap-4">
              <div 
                v-for="type in dashboardData?.eventsByType || []"
                :key="type.name"
                class="text-center p-4 border border-gray-200 rounded-lg"
              >
                <div class="text-2xl font-bold" :class="getTypeColor(type.name)">
                  {{ type.count }}
                </div>
                <div class="text-sm text-gray-600 capitalize">
                  {{ type.name }}
                </div>
                <div class="text-xs text-gray-500">
                  {{ type.percentage }}%
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Eventos e Atividade Recente -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Top Eventos -->
          <div class="bg-white p-6 rounded-lg shadow-card">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Eventos Mais Populares</h3>
            <div class="space-y-4">
              <div
                v-for="(event, index) in dashboardData?.topEvents || []"
                :key="event.id"
                class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50"
              >
                <div class="flex-shrink-0 w-8 h-8 bg-uema-100 rounded-full flex items-center justify-center mr-3">
                  <span class="text-sm font-semibold text-uema-700">
                    {{ index + 1 }}
                  </span>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 truncate">
                    {{ event.title }}
                  </p>
                  <p class="text-xs text-gray-500">
                    {{ event.registrations }} inscrições
                  </p>
                </div>
                <div class="flex-shrink-0">
                  <span 
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getEventStatusClass(event.status)"
                  >
                    {{ getEventStatusLabel(event.status) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Atividade Recente -->
          <div class="bg-white p-6 rounded-lg shadow-card">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Atividade Recente</h3>
            <div class="flow-root">
              <ul class="-mb-8">
                <li
                  v-for="(activity, index) in dashboardData?.recentActivity || []"
                  :key="activity.id"
                  class="relative pb-8"
                >
                  <div v-if="index !== (dashboardData?.recentActivity?.length - 1)" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></div>
                  <div class="relative flex space-x-3">
                    <div>
                      <span 
                        class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white"
                        :class="getActivityColor(activity.type)"
                      >
                        <component :is="getActivityIcon(activity.type)" class="h-4 w-4 text-white" />
                      </span>
                    </div>
                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                      <div>
                        <p class="text-sm text-gray-500">
                          {{ activity.description }}
                        </p>
                      </div>
                      <div class="text-right text-sm whitespace-nowrap text-gray-500">
                        {{ formatTimeAgo(activity.created_at) }}
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Alertas e Notificações -->
        <div v-if="dashboardData?.alerts?.length > 0" class="bg-white p-6 rounded-lg shadow-card">
          <h3 class="text-lg font-semibold text-gray-900 mb-6">Alertas do Sistema</h3>
          <div class="space-y-3">
            <div
              v-for="alert in dashboardData.alerts"
              :key="alert.id"
              class="flex items-start p-3 rounded-lg"
              :class="getAlertClass(alert.type)"
            >
              <div class="flex-shrink-0">
                <component 
                  :is="getAlertIcon(alert.type)" 
                  class="h-5 w-5"
                  :class="getAlertIconClass(alert.type)"
                />
              </div>
              <div class="ml-3 flex-1">
                <p class="text-sm font-medium" :class="getAlertTextClass(alert.type)">
                  {{ alert.title }}
                </p>
                <p class="text-sm" :class="getAlertDescriptionClass(alert.type)">
                  {{ alert.message }}
                </p>
              </div>
              <div class="flex-shrink-0 ml-4">
                <BaseButton
                  variant="ghost"
                  size="sm"
                  @click="dismissAlert(alert.id)"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </BaseButton>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { dashboardService } from '@/services/api/dashboard'
import BaseInput from '@/components/common/BaseInput.vue'
import BaseButton from '@/components/common/BaseButton.vue'
import StatsCard from '@/components/dashboard/StatsCard.vue'
import EventsChart from '@/components/dashboard/EventsChart.vue'
import RegistrationsChart from '@/components/dashboard/RegistrationsChart.vue'
import { useToast } from '@/composables/useToast'
import { formatTimeAgo } from '@/utils/dateUtils'

// Composables
const toast = useToast()

// State
const loading = ref(false)
const loadingCharts = ref(false)
const exporting = ref(false)
const dashboardData = ref(null)
const chartsData = ref(null)
const selectedPeriod = ref('month')
const selectedChartPeriod = ref('6m')

// Opções de período
const periodOptions = [
  { value: 'today', label: 'Hoje' },
  { value: 'week', label: 'Esta Semana' },
  { value: 'month', label: 'Este Mês' },
  { value: 'quarter', label: 'Este Trimestre' },
  { value: 'year', label: 'Este Ano' }
]

// Métodos
const loadDashboardData = async () => {
  try {
    loading.value = true
    
    // Carregar dados principais
    const [mainData, statsData] = await Promise.all([
      dashboardService.getDashboardData(),
      dashboardService.getMainStats({ period: selectedPeriod.value })
    ])
    
    dashboardData.value = {
      ...mainData,
      stats: statsData
    }
    
    // Carregar dados dos gráficos
    await loadChartsData()
    
  } catch (error) {
    console.error('Erro ao carregar dashboard:', error)
    toast.error('Erro ao carregar dados do dashboard')
  } finally {
    loading.value = false
  }
}

const loadChartsData = async () => {
  try {
    loadingCharts.value = true
    
    const [eventsChart, registrationsChart] = await Promise.all([
      dashboardService.getEventsStats({ 
        period: selectedChartPeriod.value 
      }),
      dashboardService.getRegistrationsChart({ 
        period: selectedChartPeriod.value 
      })
    ])
    
    chartsData.value = {
      eventsChart,
      registrationsChart
    }
    
  } catch (error) {
    console.error('Erro ao carregar gráficos:', error)
  } finally {
    loadingCharts.value = false
  }
}

const updateChartPeriod = (period) => {
  selectedChartPeriod.value = period
  loadChartsData()
}

const exportReport = async () => {
  try {
    exporting.value = true
    
    await dashboardService.exportDashboardData({
      format: 'excel',
      type: 'summary',
      period: selectedPeriod.value
    })
    
    toast.success('Relatório exportado com sucesso!')
    
  } catch (error) {
    console.error('Erro ao exportar:', error)
    toast.error('Erro ao exportar relatório')
  } finally {
    exporting.value = false
  }
}

const dismissAlert = async (alertId) => {
  try {
    await dashboardService.markAlertAsRead(alertId)
    
    // Remover alerta da lista
    if (dashboardData.value?.alerts) {
      dashboardData.value.alerts = dashboardData.value.alerts.filter(
        alert => alert.id !== alertId
      )
    }
    
  } catch (error) {
    console.error('Erro ao dispensar alerta:', error)
  }
}

// Helpers para styling
const getTypeColor = (type) => {
  const colors = {
    presencial: 'text-blue-600',
    online: 'text-green-600',
    hibrido: 'text-purple-600'
  }
  return colors[type] || 'text-gray-600'
}

const getEventStatusClass = (status) => {
  const classes = {
    published: 'bg-green-100 text-green-800',
    draft: 'bg-yellow-100 text-yellow-800',
    cancelled: 'bg-red-100 text-red-800',
    completed: 'bg-gray-100 text-gray-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getEventStatusLabel = (status) => {
  const labels = {
    published: 'Publicado',
    draft: 'Rascunho',
    cancelled: 'Cancelado',
    completed: 'Concluído'
  }
  return labels[status] || status
}

const getActivityColor = (type) => {
  const colors = {
    event_created: 'bg-green-500',
    user_registered: 'bg-blue-500',
    event_updated: 'bg-yellow-500',
    certificate_issued: 'bg-purple-500'
  }
  return colors[type] || 'bg-gray-500'
}

const getActivityIcon = (type) => {
  const icons = {
    event_created: 'PlusIcon',
    user_registered: 'UserPlusIcon',
    event_updated: 'PencilIcon',
    certificate_issued: 'AwardIcon'
  }
  return icons[type] || 'InformationCircleIcon'
}

const getAlertClass = (type) => {
  const classes = {
    info: 'bg-blue-50 border-blue-200',
    success: 'bg-green-50 border-green-200',
    warning: 'bg-yellow-50 border-yellow-200',
    error: 'bg-red-50 border-red-200'
  }
  return `border ${classes[type] || 'bg-gray-50 border-gray-200'}`
}

const getAlertIcon = (type) => {
  const icons = {
    info: 'InformationCircleIcon',
    success: 'CheckCircleIcon',
    warning: 'ExclamationTriangleIcon',
    error: 'XCircleIcon'
  }
  return icons[type] || 'InformationCircleIcon'
}

const getAlertIconClass = (type) => {
  const classes = {
    info: 'text-blue-500',
    success: 'text-green-500',
    warning: 'text-yellow-500',
    error: 'text-red-500'
  }
  return classes[type] || 'text-gray-500'
}

const getAlertTextClass = (type) => {
  const classes = {
    info: 'text-blue-800',
    success: 'text-green-800',
    warning: 'text-yellow-800',
    error: 'text-red-800'
  }
  return classes[type] || 'text-gray-800'
}

const getAlertDescriptionClass = (type) => {
  const classes = {
    info: 'text-blue-700',
    success: 'text-green-700',
    warning: 'text-yellow-700',
    error: 'text-red-700'
  }
  return classes[type] || 'text-gray-700'
}

// Lifecycle
onMounted(() => {
  loadDashboardData()
})
</script>