export const etaService = {
  async calculateETA(routeData) {
    const { origin, destination, waypoints, trafficConditions } = routeData
    
    try {
      const response = await fetch('/api/routes/eta', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          origin,
          destination,
          waypoints,
          trafficConditions
        })
      })
      
      return response.json()
    } catch (error) {
      console.error('ETA calculation failed:', error)
      throw new Error('Failed to calculate ETA')
    }
  },

  async subscribeToUpdates(rideId, callback) {
    // WebSocket connection for real-time updates
    const ws = new WebSocket(`ws://your-api-url/rides/${rideId}/eta-updates`)
    
    ws.onmessage = (event) => {
      const data = JSON.parse(event.data)
      callback(data)
    }

    return () => ws.close() // Return cleanup function
  },

  getTrafficConditions(location) {
    return fetch(`/api/traffic/${location.lat}/${location.lng}`)
      .then(response => response.json())
  }
} 