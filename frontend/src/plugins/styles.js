import { styles } from '@/assets/styles/shared'

export default {
  install: (app) => {
    app.config.globalProperties.$styles = styles
    app.provide('styles', styles)
  }
} 