<template>
  <div>
    <!-- Status Bar -->
    <div class="bg-white shadow-sm">
      <div class="max-w-lg mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <button 
              @click="showCancelModal = true"
              class="p-2 rounded-lg text-red-600 hover:bg-red-50"
            >
              Cancel Ride
            </button>
          </div>
          <div class="text-sm font-medium text-gray-600">
            {{ rideStatus }}
          </div>
        </div>
      </div>
    </div>

    <!-- Driver Details -->
    <div class="max-w-lg mx-auto px-4 py-4">
      <DriverCard :driver="driver" />
    </div>

    <!-- ride Progress -->
    <div class="max-w-lg mx-auto px-4 py-4">
      <rideProgress :ride-details="rideDetails" />
    </div>

    <!-- Cancel Modal -->
    <div v-if="showCancelModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Cancel Ride</h3>
        <p class="text-gray-600 mb-6">Are you sure you want to cancel this ride? A cancellation fee may apply.</p>
        <div class="flex justify-end space-x-4">
          <button 
            @click="showCancelModal = false"
            class="px-4 py-2 text-gray-600 hover:text-gray-800"
          >
            Keep Ride
          </button>
          <button 
            @click="cancelRide"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
            :disabled="isCancelling"
          >
            {{ isCancelling ? 'Cancelling...' : 'Yes, Cancel' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { format, addMinutes } from 'date-fns'
import { etaService } from '@/services/etaService'
import DriverCard from '@/components/ride/DriverCard.vue'
import RideProgress from '@/components/ride/rideProgress.vue'

const route = useRoute()  // Make sure each statement has a semicolon
const router = useRouter()  // Make sure each statement has a semicolon

// Initialize component state
const driver = ref({
  id: null,
  photo: '/default-avatar.png',
  name: 'Loading...',
  rating: 0,
  totalRides: 0,
  vehicle: {
    model: '-',
    color: '-',
    plate: '-'
  }
})  // Ensure proper semicolon usage at the end of every declaration

const currentETA = ref(new Date())
const initialETA = ref(new Date())
const remainingDistance = ref(0)
const trafficAlerts = ref([])
const rideProgressPercentage = ref(0)  // Renamed this to avoid conflict with component
const rideStatus = ref('Finding your driver...')
const showCancelModal = ref(false)
const isCancelling = ref(false)

// Computed ETA updates
const etaDelayMinutes = computed(() => {
  const totalDelay = trafficAlerts.value.reduce((acc, alert) => acc + alert.delay, 0)
  return totalDelay
})

const updatedETA = computed(() => {
  return addMinutes(initialETA.value, etaDelayMinutes.value)
})

// Format ETA for display
const formatETA = (date) => {
  return format(date, 'h:mm a')
}

// Ensure proper indentation and semicolons in the functions
const updateETAAndDistance = async () => {
  try {
    const response = await fetch(`/api/rides/${route.params.rideId}/eta`)
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    const data = await response.json()

    if (!data) {
      throw new Error('No data received from server')
    }

    currentETA.value = new Date(data.eta)
    remainingDistance.value = data.remainingDistance.toFixed(1)
    rideProgressPercentage.value = data.progress

    if (data.trafficAlerts) {
      trafficAlerts.value = data.trafficAlerts
    }
  } catch (error) {
    console.error('Failed to update ETA:', error)
  }
}
</script>