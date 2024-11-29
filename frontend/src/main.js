import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { styles } from './assets/styles/shared'
import './assets/main.css'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
  wsHost: import.meta.env.VITE_PUSHER_HOST,
  wsPort: import.meta.env.VITE_PUSHER_PORT,
  forceTLS: false,
  disableStats: true
})

const app = createApp(App)

// Make styles available globally
app.provide('styles', styles)

app.use(router)
app.mount('#app')
