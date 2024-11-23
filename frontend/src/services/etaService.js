import { ref } from 'vue'

const driverStatus = ref('Finding your driver...')
const driverLocation = ref(null)

export const etaService = {
  async startTracking(rideId) {
    try {
      const response = await fetch(`/api/rides/${rideId}/track`, {
        method: 'POST'
      })
      
      if (!response.ok) {
        throw new Error('Failed to start tracking')
      }
      
      const data = await response.json()
      
      // Initialize tracking data
      driverStatus.value = data.status || 'Driver assigned'
      driverLocation.value = data.location
      
      return {
        eta: data.eta,
        distance: data.distance,
        driver: data.driver
      }
    } catch (error) {
      console.error('Failed to start tracking:', error)
      if (process.env.NODE_ENV === 'development') {
        return this.getSimulatedData()
      }
      throw error
    }
  },

  async stopTracking(rideId) {
    try {
      await fetch(`/api/rides/${rideId}/track`, {
        method: 'DELETE'
      })
    } catch (error) {
      console.error('Failed to stop tracking:', error)
    }
  },

  getDriverStatus() {
    return driverStatus
  },

  getDriverLocation() {
    return driverLocation
  },

  getSimulatedData() {
    return {
      eta: new Date(Date.now() + 15 * 60000),
      distance: 3.5,
      driver: {
        id: 'demo-driver',
        name: 'John Driver',
        photo: '/default-avatar.png',
        rating: 4.8,
        totalRides: 1250,
        vehicle: {
          model: 'Toyota Camry',
          color: 'Silver',
          plate: 'ABC 123'
        }
      }
    }
  }
} 