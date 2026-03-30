import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api/v1',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

export default {
  getTasks(params = {}) {
    return api.get('/tasks', { params })
  },

  createTask(data) {
    return api.post('/tasks', data)
  },

  updateTask(id, data) {
    return api.put(`/tasks/${id}`, data)
  },

  updateStatus(id, status) {
    return api.patch(`/tasks/${id}/status`, { status })
  },

  deleteTask(id) {
    return api.delete(`/tasks/${id}`)
  },

  getReport(date) {
    return api.get('/tasks/report', { params: { date } })
  },

  healthCheck() {
    return api.get('/health')
  },
}
