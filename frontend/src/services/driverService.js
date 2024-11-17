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

    updateStatus(status) {
        return api.post('/driver/status', { status })
            .then(response => {
                if (response.data) {
                    return response.data
                }
                throw new Error('Failed to update driver status')
            })
    }
}
