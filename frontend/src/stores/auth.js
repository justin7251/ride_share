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
  },

  // Methods aligned with database schema
  isDriver() {
    // Check if user is a driver based on is_driver column
    return this.user?.is_driver === true
  },

  isDriverVerified() {
    // Check if driver is verified
    return this.isDriver() && !!this.user?.driver_verified_at
  },

  canBecomeDriver() {
    // Check if user can potentially become a driver
    return this.user && !this.isDriver()
  },

  getDriverStatus() {
    // Provide detailed driver status
    if (!this.isDriver()) return 'NOT_DRIVER'
    if (!this.user?.driver_verified_at) return 'PENDING_VERIFICATION'
    return 'VERIFIED_DRIVER'
  }
})

auth.init()

export default auth