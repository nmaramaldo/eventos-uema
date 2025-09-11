import { httpService } from './utils/http.js'

export const usersService = {
  // LISTAGEM E BUSCA
  // Listar usuários com filtros e paginação
  async getUsers(params = {}) {
    const queryParams = new URLSearchParams()
    
    if (params.search) queryParams.append('search', params.search)
    if (params.role) queryParams.append('role', params.role)
    if (params.status) queryParams.append('status', params.status)
    if (params.institution) queryParams.append('institution', params.institution)
    if (params.verified) queryParams.append('verified', params.verified)
    if (params.page) queryParams.append('page', params.page)
    if (params.per_page) queryParams.append('per_page', params.per_page)
    if (params.sort_by) queryParams.append('sort_by', params.sort_by)
    if (params.sort_order) queryParams.append('sort_order', params.sort_order)
    
    const query = queryParams.toString() ? `?${queryParams.toString()}` : ''
    return await httpService.get(`/users${query}`)
  },

  // Obter usuário por ID
  async getUser(id) {
    return await httpService.get(`/users/${id}`)
  },

  // Buscar usuários por termo
  async searchUsers(term) {
    return await httpService.get(`/users/search?q=${encodeURIComponent(term)}`)
  },

  // CRUD DE USUÁRIOS
  // Criar novo usuário
  async createUser(userData) {
    return await httpService.post('/users', {
      name: userData.name,
      email: userData.email,
      password: userData.password,
      role: userData.role || 'participant',
      institution: userData.institution,
      phone: userData.phone,
      bio: userData.bio,
      status: userData.status || 'active',
      send_welcome_email: userData.sendWelcomeEmail || true
    })
  },

  // Atualizar usuário
  async updateUser(id, userData) {
    return await httpService.put(`/users/${id}`, {
      name: userData.name,
      email: userData.email,
      role: userData.role,
      institution: userData.institution,
      phone: userData.phone,
      bio: userData.bio,
      status: userData.status
    })
  },

  // Deletar usuário
  async deleteUser(id) {
    return await httpService.delete(`/users/${id}`)
  },

  // Deletar múltiplos usuários
  async deleteMultipleUsers(userIds) {
    return await httpService.post('/users/bulk-delete', { ids: userIds })
  },

  // GERENCIAMENTO DE STATUS
  // Ativar/Desativar usuário
  async toggleUserStatus(id, status) {
    return await httpService.patch(`/users/${id}/status`, { status })
  },

  // Banir usuário
  async banUser(id, reason = '') {
    return await httpService.post(`/users/${id}/ban`, { reason })
  },

  // Desbanir usuário
  async unbanUser(id) {
    return await httpService.delete(`/users/${id}/ban`)
  },

  // PERFIS E PERMISSÕES
  // Alterar role do usuário
  async changeUserRole(id, role) {
    return await httpService.patch(`/users/${id}/role`, { role })
  },

  // Obter permissões do usuário
  async getUserPermissions(id) {
    return await httpService.get(`/users/${id}/permissions`)
  },

  // Atualizar permissões do usuário
  async updateUserPermissions(id, permissions) {
    return await httpService.put(`/users/${id}/permissions`, { permissions })
  },

  // AVATAR E ARQUIVOS
  // Upload de avatar
  async uploadAvatar(id, file) {
    const formData = new FormData()
    formData.append('avatar', file)
    return await httpService.upload(`/users/${id}/avatar`, formData)
  },

  // Remover avatar
  async removeAvatar(id) {
    return await httpService.delete(`/users/${id}/avatar`)
  },

  // VERIFICAÇÃO E AUTENTICAÇÃO
  // Verificar email do usuário
  async verifyUserEmail(id) {
    return await httpService.post(`/users/${id}/verify-email`)
  },

  // Reenviar email de verificação
  async resendVerificationEmail(id) {
    return await httpService.post(`/users/${id}/resend-verification`)
  },

  // Forçar reset de senha
  async forcePasswordReset(id) {
    return await httpService.post(`/users/${id}/force-password-reset`)
  },

  // EVENTOS E ATIVIDADES
  // Obter eventos do usuário
  async getUserEvents(id, params = {}) {
    const queryParams = new URLSearchParams()
    if (params.role) queryParams.append('role', params.role) // 'organizer', 'participant', 'speaker'
    if (params.status) queryParams.append('status', params.status)
    if (params.page) queryParams.append('page', params.page)
    
    const query = queryParams.toString() ? `?${queryParams.toString()}` : ''
    return await httpService.get(`/users/${id}/events${query}`)
  },

  // Obter certificados do usuário
  async getUserCertificates(id, params = {}) {
    const queryParams = new URLSearchParams()
    if (params.event_id) queryParams.append('event_id', params.event_id)
    if (params.page) queryParams.append('page', params.page)
    
    const query = queryParams.toString() ? `?${queryParams.toString()}` : ''
    return await httpService.get(`/users/${id}/certificates${query}`)
  },

  // Obter histórico de atividades
  async getUserActivityLog(id, params = {}) {
    const queryParams = new URLSearchParams()
    if (params.action) queryParams.append('action', params.action)
    if (params.date_from) queryParams.append('date_from', params.date_from)
    if (params.date_to) queryParams.append('date_to', params.date_to)
    if (params.page) queryParams.append('page', params.page)
    
    const query = queryParams.toString() ? `?${queryParams.toString()}` : ''
    return await httpService.get(`/users/${id}/activity-log${query}`)
  },

  // COMUNICAÇÃO
  // Enviar email para usuário
  async sendEmailToUser(id, emailData) {
    return await httpService.post(`/users/${id}/send-email`, {
      subject: emailData.subject,
      message: emailData.message,
      template: emailData.template
    })
  },

  // Enviar notificação para usuário
  async sendNotificationToUser(id, notificationData) {
    return await httpService.post(`/users/${id}/notifications`, {
      type: notificationData.type, // 'info', 'success', 'warning', 'error'
      title: notificationData.title,
      message: notificationData.message,
      action_url: notificationData.actionUrl
    })
  },

  // RELATÓRIOS E ESTATÍSTICAS
  // Obter estatísticas do usuário
  async getUserStats(id) {
    return await httpService.get(`/users/${id}/stats`)
  },

  // Gerar relatório de atividades do usuário
  async generateUserReport(id, params = {}) {
    const queryParams = new URLSearchParams()
    if (params.format) queryParams.append('format', params.format) // 'pdf', 'excel'
    if (params.date_from) queryParams.append('date_from', params.date_from)
    if (params.date_to) queryParams.append('date_to', params.date_to)
    
    const query = queryParams.toString() ? `?${queryParams.toString()}` : ''
    return await httpService.download(
      `/users/${id}/report${query}`,
      `usuario-${id}-relatorio.${params.format || 'pdf'}`
    )
  },

  // FUNCIONALIDADES ADMINISTRATIVAS
  // Obter usuários por instituição
  async getUsersByInstitution(institution) {
    return await httpService.get(`/users/by-institution/${encodeURIComponent(institution)}`)
  },

  // Obter usuários por role
  async getUsersByRole(role) {
    return await httpService.get(`/users/by-role/${role}`)
  },

  // Importar usuários em lote
  async importUsers(file) {
    const formData = new FormData()
    formData.append('users_file', file)
    return await httpService.upload('/users/import', formData)
  },

  // Exportar usuários
  async exportUsers(params = {}) {
    const queryParams = new URLSearchParams()
    if (params.format) queryParams.append('format', params.format) // 'csv', 'excel'
    if (params.role) queryParams.append('role', params.role)
    if (params.status) queryParams.append('status', params.status)
    if (params.institution) queryParams.append('institution', params.institution)
    
    const query = queryParams.toString() ? `?${queryParams.toString()}` : ''
    return await httpService.download(
      `/users/export${query}`,
      `usuarios.${params.format || 'excel'}`
    )
  },

  // CONFIGURAÇÕES E PREFERÊNCIAS
  // Obter preferências do usuário
  async getUserPreferences(id) {
    return await httpService.get(`/users/${id}/preferences`)
  },

  // Atualizar preferências do usuário
  async updateUserPreferences(id, preferences) {
    return await httpService.put(`/users/${id}/preferences`, { preferences })
  },

  // SESSÕES E SEGURANÇA
  // Obter sessões ativas do usuário
  async getUserSessions(id) {
    return await httpService.get(`/users/${id}/sessions`)
  },

  // Revogar sessão específica
  async revokeUserSession(id, sessionId) {
    return await httpService.delete(`/users/${id}/sessions/${sessionId}`)
  },

  // Revogar todas as sessões do usuário
  async revokeAllUserSessions(id) {
    return await httpService.post(`/users/${id}/revoke-all-sessions`)
  },

  // Obter logs de login do usuário
  async getUserLoginLogs(id, params = {}) {
    const queryParams = new URLSearchParams()
    if (params.date_from) queryParams.append('date_from', params.date_from)
    if (params.date_to) queryParams.append('date_to', params.date_to)
    if (params.page) queryParams.append('page', params.page)
    
    const query = queryParams.toString() ? `?${queryParams.toString()}` : ''
    return await httpService.get(`/users/${id}/login-logs${query}`)
  },

  // GRUPOS E ORGANIZAÇÕES
  // Adicionar usuário a grupo
  async addUserToGroup(userId, groupId) {
    return await httpService.post(`/users/${userId}/groups`, { group_id: groupId })
  },

  // Remover usuário de grupo
  async removeUserFromGroup(userId, groupId) {
    return await httpService.delete(`/users/${userId}/groups/${groupId}`)
  },

  // Obter grupos do usuário
  async getUserGroups(id) {
    return await httpService.get(`/users/${id}/groups`)
  },

  // DASHBOARD E VISÃO GERAL
  // Obter dashboard do usuário
  async getUserDashboard(id) {
    return await httpService.get(`/users/${id}/dashboard`)
  },

  // Obter últimas atividades do usuário
  async getUserRecentActivity(id, limit = 10) {
    return await httpService.get(`/users/${id}/recent-activity?limit=${limit}`)
  }
}
  