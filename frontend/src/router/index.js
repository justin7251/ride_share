import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Verify from '../views/Verify.vue'
import auth  from '../stores/auth'
import Dashboard from '../views/Dashboard.vue'

const routes = [
  { path: '/', name: 'home', component: Home },
  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },
  { path: '/verify/:phone', name: 'verify', component: Verify },
  { path: '/dashboard', name: 'dashboard', component: Dashboard }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    next({ name: 'login' })
  } else {
    next()
  }
})

export default router