import { reactive } from 'vue'

const auth = reactive({
  user: JSON.parse(localStorage.getItem('user') || 'null'),
  token: localStorage.getItem('token'),
  
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