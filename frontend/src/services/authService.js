import api from './api'
export const authService = {
  register: async (userData) => {
    try {
      const response = await api.post('/login/register', userData)
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