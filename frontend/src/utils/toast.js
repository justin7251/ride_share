import { ref } from 'vue'

class Toast {
  constructor() {
    this.notifications = ref([])
    this.defaultOptions = {
      duration: 3000,
      position: 'top-right'
    }
  }

  // Internal method to add a toast
  _addToast(message, type = 'info', options = {}) {
    const id = Date.now()
    const toastOptions = { 
      ...this.defaultOptions, 
      ...options 
    }

    const toast = {
      id,
      message,
      type,
      ...toastOptions
    }

    this.notifications.value.push(toast)

    // Automatically remove toast after duration
    setTimeout(() => {
      this.remove(id)
    }, toastOptions.duration)

    return id
  }

  // Public methods for different toast types
  success(message, options = {}) {
    return this._addToast(message, 'success', options)
  }

  error(message, options = {}) {
    return this._addToast(message, 'error', options)
  }

  info(message, options = {}) {
    return this._addToast(message, 'info', options)
  }

  warning(message, options = {}) {
    return this._addToast(message, 'warning', options)
  }

  // Remove a specific toast
  remove(id) {
    this.notifications.value = this.notifications.value.filter(
      toast => toast.id !== id
    )
  }

  // Clear all toasts
  clear() {
    this.notifications.value = []
  }
}

// Create a singleton instance
export const toast = new Toast()
