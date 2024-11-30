import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { styles } from './assets/styles/shared'
import './assets/main.css'

const app = createApp(App)

// Make styles available globally
app.provide('styles', styles)

app.use(router)
app.mount('#app')
