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

    <!-- Trip Progress -->
    <div class="max-w-lg mx-auto px-4 py-4">
      <TripProgress :trip-details="tripDetails" />
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
import TripProgress from '@/components/ride/TripProgress.vue'

const route = useRoute()
const router = useRouter()

// Get refs from etaService
const driverStatus = etaService.getDriverStatus()
const driverLocation = etaService.getDriverLocation()

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
})

const currentETA = ref(new Date())
const initialETA = ref(new Date())
const remainingDistance = ref(0)
const trafficAlerts = ref([])
const tripProgress = ref(0)
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

// Update ETA and distance based on real-time data
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
    tripProgress.value = data.progress
    
    // Update traffic alerts
    if (data.trafficAlerts) {
      trafficAlerts.value = data.trafficAlerts
    }
  } catch (error) {
    console.error('Failed to update ETA:', error)
    // Use simulated updates in case of API failure
    if (process.env.NODE_ENV === 'development') {
      updateInterval = simulateUpdates()
    }
  }
}

// Simulate real-time updates
const simulateUpdates = () => {
  // Sample traffic alerts
  const sampleAlerts = [
    {
      id: 1,
      title: 'Heavy Traffic',
      description: 'Congestion on Main Street',
      delay: 5
    },
    {
      id: 2,
      title: 'Road Work',
      description: 'Lane closure ahead',
      delay: 3
    }
  ]

  let progress = 0
  let distance = 10 // Initial distance in km

  return setInterval(() => {
    // Update progress
    progress += 1
    tripProgress.value = Math.min(progress, 100)

    // Update remaining distance
    distance = Math.max(0, distance - 0.2)
    remainingDistance.value = distance.toFixed(1)

    // Randomly add/remove traffic alerts
    if (Math.random() > 0.8) {
      const randomAlert = sampleAlerts[Math.floor(Math.random() * sampleAlerts.length)]
      if (!trafficAlerts.value.find(alert => alert.id === randomAlert.id)) {
        trafficAlerts.value.push({ ...randomAlert, id: Date.now() })
      }
    }

    // Update ETA based on progress and traffic
    const totalDelay = trafficAlerts.value.reduce((acc, alert) => acc + alert.delay, 0)
    currentETA.value = addMinutes(initialETA.value, totalDelay)
  }, 3000)
}

let updateInterval

onMounted(async () => {
  // Initialize ETA tracking
  const rideId = route.params.rideId
  const initialData = await etaService.startTracking(rideId)
  
  initialETA.value = new Date(initialData.eta)
  currentETA.value = new Date(initialData.eta)
  remainingDistance.value = initialData.distance

  // Start real-time updates
  if (process.env.NODE_ENV === 'development') {
    updateInterval = simulateUpdates()
  } else {
    updateInterval = setInterval(updateETAAndDistance, 10000) // Update every 10 seconds
  }

  try {
    const response = await rideService.getRideDetails(route.params.rideId)
    driver.value = {
      ...driver.value,
      ...response.driver
    }
  } catch (error) {
    console.error('Failed to fetch ride details:', error)
  }
})

onUnmounted(() => {
  if (updateInterval) {
    clearInterval(updateInterval)
  }
  etaService.stopTracking(route.params.rideId)
})

// Add the cancel ride function
const cancelRide = async () => {
  isCancelling.value = true
  try {
    await fetch(`/api/rides/${route.params.rideId}/cancel`, {
      method: 'POST'
    })
    
    // Stop tracking updates
    if (updateInterval) {
      clearInterval(updateInterval)
    }
    await etaService.stopTracking(route.params.rideId)
    
    // Navigate back to search
    router.push('/rider/search')
  } catch (error) {
    console.error('Failed to cancel ride:', error)
  } finally {
    isCancelling.value = false
    showCancelModal.value = false
  }
}
</script>

<style scoped>
.progress-transition {
  transition: width 0.5s ease-in-out;
}
</style> 