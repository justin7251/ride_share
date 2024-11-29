import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { ref } from 'vue'

export const websocketService = {
  echo: null,
  notification: ref(null),

  initializeDriverSocket() {
    this.echo = new Echo({
      broadcaster: 'pusher',
      key: import.meta.env.VITE_PUSHER_APP_KEY,
      cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
      wsHost: import.meta.env.VITE_PUSHER_HOST,
      wsPort: import.meta.env.VITE_PUSHER_PORT,
      forceTLS: false,
      disableStats: true,
      enabledTransports: ['ws', 'wss']
    })

    this.echo.channel('available-rides')
      .listen('RideRequestEvent', (event) => {
        this.notification.value = {
          type: 'NEW_RIDE_REQUEST',
          ride: event.rideRequest,
          timestamp: new Date()
        }
      })
  },

  listenForRideUpdates(rideId) {
    if (!this.echo) return

    this.echo.channel(`ride.${rideId}`)
      .listen('RideAccepted', (event) => {
        console.log('Ride accepted:', event)
      })
      .listen('RideStarted', (event) => {
        console.log('Ride started:', event)
      })
  },

  stopListeningForRides() {
    if (this.echo) {
      this.echo.disconnect()
    }
  },

  clearNotification() {
    this.notification.value = null
  }
}
