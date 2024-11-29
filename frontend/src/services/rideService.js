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
      const response = await api.get(`/rides/${rideId}/track`);
      return response.data;
    } catch (error) {
      console.error('Error tracking ride:', error);
      throw error;
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
      return response.data
    } catch (error) {
      console.error('Ride cancellation error:', error)
      throw error
    }
  }
};
