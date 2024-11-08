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
};
