import { reactive } from 'vue'

const auth = reactive({
  isAuthenticated: !!localStorage.getItem('token'),
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
    this.isAuthenticated = true
  },
  
  setToken(token) {
    this.token = token
    localStorage.setItem('token', token)
    this.isAuthenticated = true
  },
  
  clear() {
    this.user = null
    this.token = null
    this.isAuthenticated = false
    localStorage.removeItem('user')
    localStorage.removeItem('token')
  },
  
  init() {
    const hasToken = !!localStorage.getItem('token')
    const hasUser = !!localStorage.getItem('user')
    this.isAuthenticated = hasToken && hasUser
  },
  
  setAuthenticated(status) {
    this.isAuthenticated = status
  }
})

auth.init()

export default auth