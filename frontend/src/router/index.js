import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Verify from '../views/Verify.vue'
import auth  from '../stores/auth'
import Dashboard from '../views/Dashboard.vue'
import RideSearch from '../views/rider/RideSearch.vue';
import RideOptions from '../views/rider/RideOptions.vue';
import RideTracking from '../views/rider/RideTracking.vue';
import DriverDashboard from '../views/driver/Dashboard.vue';
import NotFound from '../views/NotFound.vue';
import UserEdit from '../views/user/Edit.vue';
const routes = [
  { path: '/', name: 'home', component: Home },
  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },
  { path: '/verify/:phone', name: 'verify', component: Verify },
  { path: '/dashboard', name: 'dashboard', component: Dashboard },
  { path: '/rider/search', name: 'ride-search', component: RideSearch },
  { path: '/rider/options', name: 'ride-options', component: RideOptions },
  { path: '/rider/tracking/:rideId', name: 'ride-tracking', component: RideTracking },
  { path: '/driver/dashboard', name: 'driver-dashboard', component: DriverDashboard },
  { path: '/user/edit', name: 'user-edit', component: UserEdit },
  { path: '/:pathMatch(.*)*', name: 'not-found', component: NotFound }
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