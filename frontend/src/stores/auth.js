import { reactive } from 'vue'

const auth = reactive({
  user: (() => {
    const user = localStorage.getItem('user');
    try {
      return user ? JSON.parse(user) : null;
    } catch (error) {
      console.error('Error parsing user from localStorage:', error);
      return null;
    }
  })(),
  token: localStorage.getItem('token') || null,
  
  setUser(userData) {
    this.user = userData
    localStorage.setItem('user', JSON.stringify(userData))
  },
  
  setToken(token) {
    this.token = token
    localStorage.setItem('token', token)
  },
  
  clear() {
    this.user = null
    this.token = null
    localStorage.removeItem('user')
    localStorage.removeItem('token')
  },
  
  get isAuthenticated() {
    return !!this.token
  }
})

export default auth