import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import router from '@/router'

// Configuração base do Axios
const http = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  }
})

// Request interceptor - adiciona token de autenticação
http.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore()
    const token = authStore.token || localStorage.getItem('auth_token')
    
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }

    // CSRF token para Laravel Sanctum
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
    if (csrfToken) {
      config.headers['X-CSRF-TOKEN'] = csrfToken
    }
    
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor - trata respostas e erros globalmente
http.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    const authStore = useAuthStore()
    
    // Se não autenticado (401), redireciona para login
    if (error.response?.status === 401) {
      authStore.logout()
      router.push('/login')
      return Promise.reject(error)
    }
    
    // Se sem permissão (403)
    if (error.response?.status === 403) {
      router.push('/unauthorized')
      return Promise.reject(error)
    }
    
    // Se não encontrado (404)
    if (error.response?.status === 404) {
      router.push('/not-found')
      return Promise.reject(error)
    }
    
    // Erro interno do servidor (500)
    if (error.response?.status >= 500) {
      router.push('/server-error')
      return Promise.reject(error)
    }
    
    return Promise.reject(error)
  }
)

// Helpers para diferentes tipos de requisição
export const httpService = {
  // GET request
  async get(url, config = {}) {
    try {
      const response = await http.get(url, config)
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  // POST request
  async post(url, data = {}, config = {}) {
    try {
      const response = await http.post(url, data, config)
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  // PUT request
  async put(url, data = {}, config = {}) {
    try {
      const response = await http.put(url, data, config)
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  // PATCH request
  async patch(url, data = {}, config = {}) {
    try {
      const response = await http.patch(url, data, config)
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  // DELETE request
  async delete(url, config = {}) {
    try {
      const response = await http.delete(url, config)
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  // Upload de arquivo
  async upload(url, formData, config = {}) {
    try {
      const response = await http.post(url, formData, {
        ...config,
        headers: {
          ...config.headers,
          'Content-Type': 'multipart/form-data'
        }
      })
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  // Download de arquivo
  async download(url, filename, config = {}) {
    try {
      const response = await http.get(url, {
        ...config,
        responseType: 'blob'
      })
      
      // Criar link de download
      const downloadUrl = window.URL.createObjectURL(new Blob([response.data]))
      const link = document.createElement('a')
      link.href = downloadUrl
      link.setAttribute('download', filename)
      document.body.appendChild(link)
      link.click()
      link.remove()
      window.URL.revokeObjectURL(downloadUrl)
      
      return response.data
    } catch (error) {
      throw this.handleError(error)
    }
  },

  // Tratamento de erros
  handleError(error) {
    const errorData = {
      message: 'Erro desconhecido',
      status: null,
      data: null
    }

    if (error.response) {
      // Erro com resposta do servidor
      errorData.status = error.response.status
      errorData.data = error.response.data
      errorData.message = error.response.data?.message || 
                         error.response.statusText || 
                         'Erro no servidor'
    } else if (error.request) {
      // Erro de rede
      errorData.message = 'Erro de conexão. Verifique sua internet.'
    } else {
      // Erro de configuração
      errorData.message = error.message
    }

    return errorData
  }
}

export default http