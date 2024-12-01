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
        .listen('DriverLocationUpdated', (data) => {
            console.log('Driver Location Updated:', data)
            this.notification.value = {
              type: 'DRIVER_LOCATION_UPDATE',
              location: data.location || {},
              timestamp: new Date()
            }
        })
        .listen('RideStarted', (data) => {
            console.log('Ride Started:', data)
            this.notification.value = {
            type: 'RIDE_STARTED',
            ride: data.ride || {},
            timestamp: new Date()
            }
        })
        .listen('RideCompleted', (data) => {
            console.log('Ride Completed:', data)
            this.notification.value = {
              type: 'RIDE_COMPLETED',
              ride: data.ride || {},
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
  handleRideAcceptance(data) {
    this.notification.value = {
      type: 'RIDE_ACCEPTED',
      ride: data.ride,
      driver: data.driver,
      timestamp: new Date()
    }
  },
  handleRideStart(data) {
    this.notification.value = {
      type: 'RIDE_STARTED',
      ride: data.ride,
      timestamp: new Date()
    }
  }
  
}
