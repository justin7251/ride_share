import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { ref } from 'vue'

export const websocketService = {
  echo: null,
  notification: ref(null),

  initializeDriverSocket() {
    Pusher.logToConsole = true
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
        enabledTransports: ['ws', 'wss']
      })

      this.echo.channel('available-rides')
        .listen('RideRequestEvent', (data) => {
          console.log('WebSocket Ride Request:', data)
          this.notification.value = {
            type: 'NEW_RIDE_REQUEST',
            ride: data.ride || {},
            activeDrivers: data.activeDrivers || [],
            timestamp: new Date()
          }
        })
        .listen('RideCancelled', (data) => {
          console.log('Ride Cancelled:', data)
          this.notification.value = {
            type: 'RIDE_CANCELLED',
            ride: data.ride || {},
            reason: data.reason || 'Unspecified',
            timestamp: new Date()
          }
        })
    } catch (error) {
      console.error('WebSocket Initialization Error:', error)
    }
  },

  stopListeningForRides() { 
    this.echo.channel('available-rides').stopListening('RideRequestEvent')
  },
  clearNotification() {
    this.notification.value = null
  },
  handleRideRequest(data) {
    this.notification.value = data
  },
  handleRideCancellation(ride) {
    this.notification.value = {
      type: 'RIDE_CANCELLED',
      ride: ride,
      timestamp: new Date()
    }
  }
}
