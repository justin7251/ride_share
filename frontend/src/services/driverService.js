import api from './api'

export const driverService = {
    getStatus() {
        return api.get('/driver/status')
            .then(response => {
                if (response.data) {
                    return response.data
                }
                throw new Error('Failed to get driver status')
            })
    },

    updateStatus(status, driverLocation) {
        return api.post('/driver/status', { status, driver_location: driverLocation })
            .then(response => {
                if (response.data) {
                    return response.data
                }
                throw new Error('Failed to update driver status')
            })
    }
}
