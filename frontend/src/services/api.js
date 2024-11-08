import axios from 'axios'

const api = axios.create({
  baseURL: 'http://localhost:9001/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

export const authService = {
  register: async (userData) => {
    try {
      const response = await api.post('/register', userData)
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  verify: async (phone, verification_code) => {
    try {
      const response = await api.post('/login/verify', {
        phone,
        verification_code
      })
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  },

  login: async (phone) => {
    try {
      const response = await api.post('/login', {
        phone
      })
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  }
}

// Add interceptor to handle auth token
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export default api 