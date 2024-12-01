import api from './api';

export const rideService = {
  async bookRide({ pickup, destination, rideType }) {
    try {
      const response = await api.post('/rides/book', {
        pickup,
        destination,
        rideType,
      });

      return response.data;
    } catch (error) {
      console.error('Error booking ride:', error);
      throw error;
    }
  },

  async getAvailableRides() {
    try {
      const response = await api.get('/rides/available');
      return response.data;
    } catch (error) {
      console.error('Error fetching available rides:', error);
      throw error;
    }
  },

  async trackRide(rideId) {
    try {
      const response = await api.get(`/rides/${rideId}/track`)
      
      return {
        ride: response.data.ride,
        status: response.data.status,
        driver: response.data.driver,
        location: response.data.location,
        eta: response.data.eta,
        distance: response.data.distance
      }
    } catch (error) {
      console.error('Ride tracking error:', error)
      throw error
    }
  },

  async searchRide(pickup, destination, pickupLat, pickupLng, destinationLat, destinationLng) {
    try {
      const response = await api.post('/rides/search-drivers', {
        pickup,
        destination,
        pickupLat,
        pickupLng,
        destinationLat,
        destinationLng
      });
      return response.data;
    } catch (error) {
      console.error('Error searching for ride:', error);
      if (process.env.NODE_ENV === 'development') {
        // Simulate API response
        await new Promise(resolve => setTimeout(resolve, 1500));
        return {
          rideId: `dev-${Date.now()}`,
          pickup,
          destination,
          availableDrivers: 3
        };
      }
      throw error;
    }
  },

  async cancelRide(rideId) {
    try {
      const response = await api.post(`/rides/${rideId}/cancel`)
      
      if (!response.data.success) {
        throw new Error(response.data.message || 'Failed to cancel ride')
      }
      
      return response.data
    } catch (error) {
      console.error('Ride cancellation error:', error)
      throw error
    }
  },

  async acceptRide(rideId) {
    try {
      const response = await api.put(`/rides/${rideId}/accept`)
      
      console.log('Full Ride Accept Response:', {
        ride: response.data.ride,
        driver: response.data.driver
      })

      return response.data
    } catch (error) {
      console.error('Ride Acceptance Full Error:', {
        status: error.response?.status,
        data: error.response?.data,
        headers: error.response?.headers,
        message: error.message
      })
      throw error
    }
  },

  // Helper method to sanitize data
  sanitizeRideData(ride) {
    // Remove or replace problematic characters
    const sanitizedRide = { ...ride }
    
    // Example: Clean specific fields
    if (sanitizedRide.origin) {
      sanitizedRide.origin = this.cleanString(sanitizedRide.origin)
    }
    
    if (sanitizedRide.destination) {
      sanitizedRide.destination = this.cleanString(sanitizedRide.destination)
    }
    
    return sanitizedRide
  },

  cleanString(str) {
    // Remove non-printable characters and normalize
    return str.replace(/[^\x20-\x7E]/g, '')
  },

  async startRide(rideId) {
    try {
      const response = await api.put(`/rides/${rideId}/start`)
      return response.data
    } catch (error) {
      console.error('Ride Start Error:', error.response?.data || error.message)
      throw error
    }
  },

  async completeRide(rideId) {
    try {
      const response = await api.put(`/rides/${rideId}/complete`)
      return response.data
    } catch (error) {
      console.error('Ride Complete Error:', error.response?.data || error.message)
      throw error
    }
  }
};
