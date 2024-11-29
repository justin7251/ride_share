import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { ref } from 'vue'

export const websocketService = {
  echo: null,
  notification: ref(null),

  initializeDriverSocket() {
    console.group('WebSocket Initialization')
    console.log('Detailed Initialization:', {
      key: import.meta.env.VITE_PUSHER_APP_KEY,
      cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
      host: window.location.hostname,
      port: 6001
    })

    window.Pusher = Pusher

    try {
      this.echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        wsHost: window.location.hostname,
        wsPort: 6001,
        forceTLS: false,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
        encrypted: false,
        authEndpoint: '/broadcasting/auth'
      })

      const channel = this.echo.channel('available-rides')
      
      console.log('Detailed Channel Subscription:', {
        channelName: 'available-rides',
        channelObject: channel,
        channelMethods: Object.keys(channel)
      })

      channel.listen('RideRequestEvent', (event) => {
        console.group('🚨 Comprehensive WebSocket Event')
        console.log('Raw Event Structure:', JSON.stringify(event, null, 2))
        console.log('Event Type:', event.type)
        console.log('Ride Request Details:', event.rideRequest)
        console.log('Active Drivers:', event.activeDrivers)
        console.groupEnd()

        this.notification.value = {
          type: 'NEW_RIDE_REQUEST',
          ride: event.rideRequest || {},
          activeDrivers: event.activeDrivers || [],
          timestamp: new Date()
        }
      })

      console.log('Driver Socket Initialized Successfully')
      console.groupEnd()

    } catch (error) {
      console.error('Comprehensive WebSocket Error:', {
        message: error.message,
        stack: error.stack,
        name: error.name
      })
      console.groupEnd()
    }
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

// Prevent auto-initialization
// websocketService.initializeDriverSocket()
