<template>
  <div 
    v-if="websocketService.notification.value && websocketService.notification.value.type === 'NEW_RIDE_REQUEST'"
    class="fixed top-4 right-4 z-50 bg-white shadow-lg rounded-lg p-4 border-l-4 border-green-500"
  >
    <div class="flex items-center justify-between">
      <div>
        <h3 class="font-bold text-gray-900">New Ride Request</h3>
        Debug: {{ JSON.stringify(websocketService.notification.value) }}
        <p class="text-sm text-gray-600">
            Ride from {{ websocketService.notification.value.ride.pickup }} 
            to {{ websocketService.notification.value.ride.destination }}
        </p>
      </div>
    </div>
    
    <div class="mt-3 flex space-x-2">
      <button 
        @click="acceptRide"
        class="bg-green-500 text-white px-3 py-1 rounded-lg text-sm"
      >
        Accept Ride
      </button>
      <button 
        @click="dismissNotification"
        class="bg-red-500 text-white px-3 py-1 rounded-lg text-sm"
      >
        Dismiss
      </button>
    </div>
  </div>
</template>

<script setup>
import { websocketService } from '@/services/websocketService'
import { rideService } from '@/services/rideService'
import { useRouter } from 'vue-router'

const router = useRouter()

const acceptRide = async () => {
  const ride = websocketService.notification.value.ride
  try {
    const response = await rideService.acceptRide(ride.id)
    router.push(`/driver/ride/${ride.id}`)
    websocketService.clearNotification()
  } catch (error) {
    console.error('Failed to accept ride:', error)
  }
}

const dismissNotification = () => {
  websocketService.clearNotification()
}
</script>
