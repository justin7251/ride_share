import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { ref } from 'vue'
import auth from '@/stores/auth'

export const websocketService = {
  echo: null,
  notification: ref(null),
  subscribers: {},

  getUserRole() {
    return auth.isDriver() ? 'DRIVER' : 'PASSENGER'
  },

  initializeSocket(options = {}) {
    if (!import.meta.env.VITE_PUSHER_APP_KEY) {
      console.error('Pusher app key is missing')
      return null
    }

    Pusher.logToConsole = import.meta.env.DEV
    window.Pusher = Pusher

    try {
      this.echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        wsHost: import.meta.env.VITE_PUSHER_HOST,
        wsPort: import.meta.env.VITE_PUSHER_PORT,
        forceTLS: false,
        disableStats: true,
        encrypted: false,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        enabledTransports: ['ws', 'wss'],
        ...options
      })

      const userRole = this.getUserRole()

      if (userRole === 'DRIVER') {
        this.subscribeToAvailableRides()
      }

      return this.echo
    } catch (error) {
      console.error('WebSocket Initialization Error:', error)
      return null
    }
  },

  subscribe(channel, callback, options = {}) {
    if (options.requiredRole && auth.isDriver()) {
      console.warn(`Subscription requires ${options.requiredRole} role`)
      return null
    }

    if (!this.subscribers[channel]) {
      this.subscribers[channel] = []
    }
    this.subscribers[channel].push(callback)
    return callback
  },

  unsubscribe(channel, callback) {
    if (this.subscribers[channel]) {
      this.subscribers[channel] = this.subscribers[channel].filter(
        cb => cb !== callback
      )
    }
  },

  notifySubscribers(channel, data) {
    if (this.subscribers[channel]) {
      this.subscribers[channel].forEach(callback => {
        callback(data)
      })
    }
  },

  subscribeToAvailableRides() {
    if (!this.echo) return

    this.echo.channel('available-rides')
      .listen('RideRequestEvent', (data) => {
        console.log('New Ride Request:', data)
        this.notification.value = {
          type: 'NEW_RIDE_REQUEST',
          ride: data.ride,
          timestamp: new Date()
        }
        this.notifySubscribers('available-rides', data)
      })
  },

  subscribeToRideChannel(rideId) {
    if (!this.echo) return

    return this.echo.private(`ride.${rideId}`)
      .listen('ride.completed', (data) => {
        console.log('Ride Completed:', data)
        this.notification.value = {
          type: 'RIDE_COMPLETED',
          ride: data.ride,
          timestamp: new Date()
        }
        this.notifySubscribers(`ride.${rideId}`, {
          type: 'RIDE_COMPLETED',
          ride: data.ride
        })
      })
  },

  handleDriverStatusUpdate(data) {
    console.log('Driver Status Updated:', data)
    this.notification.value = {
      type: 'DRIVER_STATUS_UPDATED',
      status: data.status,
      timestamp: new Date()
    }
    this.notifySubscribers('ride-events', {
      type: 'DRIVER_STATUS_UPDATED',
      status: data.status
    })
  },

  handleRideStarted(data) {
    console.log('Ride Started:', data)
    this.notification.value = {
      type: 'RIDE_STARTED',
      ride: data.ride || {},
      timestamp: new Date()
    }
    this.notifySubscribers('ride-events', {
      type: 'RIDE_STARTED'
    })
  },

  handleRideCompleted(data) {
    console.log('Ride Completed:', data)
    this.notification.value = {
      type: 'RIDE_COMPLETED',
      ride: data.ride || {},
      timestamp: new Date()
    }
    this.notifySubscribers('ride-events', {
      type: 'RIDE_COMPLETED'
    })
  },

  disconnect() {
    if (this.echo) {
      this.echo.disconnect()
      this.echo = null
      this.subscribers = {}
      this.notification.value = null
    }
  },

  clearNotification(type = null) {
    if (type) {
      if (this.notification.value?.type === type) {
        this.notification.value = null
      }
    } else {
      this.notification.value = null
    }
  }
}
