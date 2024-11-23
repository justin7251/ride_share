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

  async searchRide(pickup, destination) {
    try {
      const response = await api.post('/rides/search', {
        pickup,
        destination
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

  async searchDrivers(pickup, destination) {
    try {
      const response = await api.post('/rides/search-drivers', {
        pickup,
        destination,
        timestamp: new Date().toISOString()
      });

      return response.data;
    } catch (error) {
      console.error('Error searching for drivers:', error);
      if (process.env.NODE_ENV === 'development') {
        await new Promise(resolve => setTimeout(resolve, 1500));
        return {
          success: true,
          drivers: [
            {
              id: 'dev-driver-1',
              name: 'John Driver',
              rating: 4.8,
              distance: 1.2, // km from pickup
              estimatedArrival: new Date(Date.now() + 5 * 60000), // 5 mins
              vehicle: {
                model: 'Toyota Camry',
                color: 'Silver',
                plate: 'ABC 123'
              }
            },
            {
              id: 'dev-driver-2',
              name: 'Sarah Driver',
              rating: 4.9,
              distance: 1.8,
              estimatedArrival: new Date(Date.now() + 7 * 60000),
              vehicle: {
                model: 'Honda Civic',
                color: 'Black',
                plate: 'XYZ 789'
              }
            }
          ]
        };
      }
      throw error;
    }
  }
};
