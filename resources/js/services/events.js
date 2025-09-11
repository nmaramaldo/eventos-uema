import { httpService } from './utils/http.js'

export const eventsService = {
  // Listar eventos com filtros e paginação
  async getEvents(params = {}) {
    const queryParams = new URLSearchParams()
    
    // Adicionar parâmetros de filtro
    if (params.search) queryParams.append('search', params.search)
    if (params.category) queryParams.append('category', params.category)
    if (params.status) queryParams.append('status', params.status)
    if (params.type) queryParams.append('type', params.type)
    if (params.date_from) queryParams.append('date_from', params.date_from)
    if (params.date_to) queryParams.append('date_to', params.date_to)
    if (params.organizer) queryParams.append('organizer', params.organizer)
    if (params.page) queryParams.append('page', params.page)
    if (params.per_page) queryParams.append('per_page', params.per_page)
    if (params.sort_by) queryParams.append('sort_by', params.sort_by)
    if (params.sort_order) queryParams.append('sort_order', params.sort_order)
    
    const query = queryParams.toString() ? `?${queryParams.toString()}` : ''
    return await httpService.get(`/events${query}`)
  },

  // Obter evento por ID
  async getEvent(id) {
    return await httpService.get(`/events/${id}`)
  },

  // Criar novo evento
  async createEvent(eventData) {
    return await httpService.post('/events', {
      title: eventData.title,
      description: eventData.description,
      short_description: eventData.shortDescription,
      type: eventData.type, // 'online', 'presencial', 'hibrido'
      category: eventData.category,
      status: eventData.status || 'draft',
      start_date: eventData.startDate,
      end_date: eventData.endDate,
      start_time: eventData.startTime,
      end_time: eventData.endTime,
      timezone: eventData.timezone,
      max_participants: eventData.maxParticipants,
      registration_start: eventData.registrationStart,
      registration_end: eventData.registrationEnd,
      location: eventData.location,
      address: eventData.address,
      latitude: eventData.latitude,
      longitude: eventData.longitude,
      online_link: eventData.onlineLink,
      meeting_id: eventData.meetingId,
      meeting_password: eventData.meetingPassword,
      banner_image: eventData.bannerImage,
      price: eventData.price || 0,
      is_free: eventData.isFree || true,
      payment_methods: eventData.paymentMethods || [],
      certificate_template: eventData.certificateTemplate,
      requires_approval: eventData.requiresApproval || false,
      tags: eventData.tags || [],
      speakers: eventData.speakers || [],
      sponsors: eventData.sponsors || [],
      schedule: eventData.schedule || [],
      custom_fields: eventData.customFields || {},
      notification_settings: eventData.notificationSettings || {}
    })
  },

  // Atualizar evento
  async updateEvent(id, eventData) {
    return await httpService.put(`/events/${id}`, eventData)
  },

  // Deletar evento
  async deleteEvent(id) {
    return await httpService.delete(`/events/${id}`)
  },

  // Duplicar evento
  async duplicateEvent(id, newEventData = {}) {
    return await httpService.post(`/events/${id}/duplicate`, newEventData)
  },

  // Publicar/Despublicar evento
  async toggleEventStatus(id, status) {
    return await httpService.patch(`/events/${id}/status`, { status })
  },

  // Upload de banner do evento
  async uploadBanner(id, file) {
    const formData = new FormData()
    formData.append('banner', file)
    return await httpService.upload(`/events/${id}/banner`, formData)
  },

  // INSCRIÇÕES
  // Inscrever-se em evento
  async registerForEvent(id, registrationData) {
    return await httpService.post(`/events/${id}/register`, {
      custom_fields: registrationData.customFields || {},
      payment_method: registrationData.paymentMethod,
      special_needs: registrationData.specialNeeds,
      dietary_restrictions: registrationData.dietaryRestrictions
    })
  },

  // Cancelar inscrição
  async cancelRegistration(id, reason = '') {
    return await httpService.delete(`/events/${id}/register`, {
      data: { reason }
    })
  },

  // Listar inscrições do evento
  async getEventRegistrations(id, params = {}) {
    const queryParams = new URLSearchParams()
    if (params.status) queryParams.append('status', params.status)
    if (params.search) queryParams.append('search', params.search)
    if (params.page) queryParams.append('page', params.page)
    
    const query = queryParams.toString() ? `?${queryParams.toString()}` : ''
    return await httpService.get(`/events/${id}/registrations${query}`)
  },

  // Aprovar/Rejeitar inscrição
  async updateRegistrationStatus(eventId, registrationId, status, message = '') {
    return await httpService.patch(`/events/${eventId}/registrations/${registrationId}`, {
      status,
      message
    })
  },

  // CHECK-IN
  // Fazer check-in
  async checkIn(eventId, data) {
    return await httpService.post(`/events/${eventId}/checkin`, {
      qr_code: data.qrCode,
      registration_id: data.registrationId,
      type: data.type || 'arrival' // 'arrival', 'departure'
    })
  },

  // Obter dados para check-in
  async getCheckInData(eventId) {
    return await httpService.get(`/events/${eventId}/checkin-data`)
  },

  // Listar presenças
  async getAttendance(eventId, params = {}) {
    const queryParams = new URLSearchParams()
    if (params.search) queryParams.append('search', params.search)
    if (params.status) queryParams.append('status', params.status)
    
    const query = queryParams.toString() ? `?${queryParams.toString()}` : ''
    return await httpService.get(`/events/${eventId}/attendance${query}`)
  },

  // CERTIFICADOS
  // Gerar certificados
  async generateCertificates(eventId, criteria = {}) {
    return await httpService.post(`/events/${eventId}/certificates/generate`, {
      min_attendance: criteria.minAttendance || 75,
      include_speakers: criteria.includeSpeakers || false,
      include_organizers: criteria.includeOrganizers || false
    })
  },

  // Baixar certificado
  async downloadCertificate(eventId, participantId) {
    return await httpService.download(
      `/events/${eventId}/certificates/${participantId}`,
      `certificado-evento-${eventId}.pdf`
    )
  },

  // Validar certificado
  async validateCertificate(hash) {
    return await httpService.get(`/certificates/validate/${hash}`)
  },

  // FORMULÁRIOS DINÂMICOS
  // Salvar formulário de inscrição
  async saveRegistrationForm(eventId, formData) {
    return await httpService.put(`/events/${eventId}/registration-form`, {
      fields: formData.fields,
      settings: formData.settings
    })
  },

  // Obter formulário de inscrição
  async getRegistrationForm(eventId) {
    return await httpService.get(`/events/${eventId}/registration-form`)
  },

  // ESTATÍSTICAS
  // Obter estatísticas do evento
  async getEventStats(eventId) {
    return await httpService.get(`/events/${eventId}/stats`)
  },

  // Obter dados para dashboard
  async getEventDashboard(eventId) {
    return await httpService.get(`/events/${eventId}/dashboard`)
  },

  // RELATÓRIOS
  // Gerar relatório de inscrições
  async generateRegistrationReport(eventId, format = 'excel') {
    return await httpService.download(
      `/events/${eventId}/reports/registrations?format=${format}`,
      `relatorio-inscricoes-${eventId}.${format}`
    )
  },

  // Gerar relatório de presença
  async generateAttendanceReport(eventId, format = 'excel') {
    return await httpService.download(
      `/events/${eventId}/reports/attendance?format=${format}`,
      `relatorio-presenca-${eventId}.${format}`
    )
  },

  // Gerar relatório de certificados
  async generateCertificateReport(eventId, format = 'excel') {
    return await httpService.download(
      `/events/${eventId}/reports/certificates?format=${format}`,
      `relatorio-certificados-${eventId}.${format}`
    )
  },

  // EVENTOS PÚBLICOS
  // Listar eventos públicos
  async getPublicEvents(params = {}) {
    const queryParams = new URLSearchParams()
    if (params.search) queryParams.append('search', params.search)
    if (params.category) queryParams.append('category', params.category)
    if (params.type) queryParams.append('type', params.type)
    if (params.city) queryParams.append('city', params.city)
    if (params.date) queryParams.append('date', params.date)
    if (params.page) queryParams.append('page', params.page)
    
    const query = queryParams.toString() ? `?${queryParams.toString()}` : ''
    return await httpService.get(`/public/events${query}`)
  },

  // Obter evento público por slug
  async getPublicEvent(slug) {
    return await httpService.get(`/public/events/${slug}`)
  },

  // CATEGORIAS
  // Listar categorias
  async getCategories() {
    return await httpService.get('/events/categories')
  },

  // SPEAKERS/PALESTRANTES
  // Adicionar palestrante ao evento
  async addSpeaker(eventId, speakerData) {
    return await httpService.post(`/events/${eventId}/speakers`, speakerData)
  },

  // Remover palestrante do evento
  async removeSpeaker(eventId, speakerId) {
    return await httpService.delete(`/events/${eventId}/speakers/${speakerId}`)
  },

  // PATROCINADORES
  // Adicionar patrocinador
  async addSponsor(eventId, sponsorData) {
    return await httpService.post(`/events/${eventId}/sponsors`, sponsorData)
  },

  // Remover patrocinador
  async removeSponsor(eventId, sponsorId) {
    return await httpService.delete(`/events/${eventId}/sponsors/${sponsorId}`)
  },

  // AGENDA/PROGRAMAÇÃO
  // Salvar programação do evento
  async saveSchedule(eventId, scheduleData) {
    return await httpService.put(`/events/${eventId}/schedule`, {
      sessions: scheduleData.sessions,
      breaks: scheduleData.breaks,
      settings: scheduleData.settings
    })
  },

  // NOTIFICAÇÕES
  // Enviar notificação para participantes
  async sendNotification(eventId, notificationData) {
    return await httpService.post(`/events/${eventId}/notifications`, {
      type: notificationData.type, // 'email', 'sms', 'push'
      subject: notificationData.subject,
      message: notificationData.message,
      recipients: notificationData.recipients, // 'all', 'registered', 'attended'
      schedule_at: notificationData.scheduleAt
    })
  },

  // INTEGRAÇÕES
  // Sincronizar com plataforma externa
  async syncWithPlatform(eventId, platform, settings) {
    return await httpService.post(`/events/${eventId}/integrations/${platform}`, settings)
  },

  // Obter link de transmissão
  async getStreamingLink(eventId) {
    return await httpService.get(`/events/${eventId}/streaming-link`)
  }
}