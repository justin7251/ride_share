import api from './api'
export const userService = {
    getUserWithDriver: async () => {
      try {
        const response = await api.get('/user/driver')
        return response.data
      } catch (error) {
        throw error.response?.data || error
      }
    },
  
    updateUserWithDriver: async (userData) => {
      try {
        const response = await api.put('/user/driver', userData)
        return response.data
      } catch (error) {
        throw error.response?.data || error
      }
    }
  }
  
  export default userService