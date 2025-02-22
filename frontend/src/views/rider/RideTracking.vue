<template>
  <div class="min-h-screen bg-gray-100 flex flex-col">
    <div class="max-w-md mx-auto w-full flex-grow flex flex-col justify-center p-6">
      <!-- Navigation Header -->
      <div class="absolute top-4 left-4 z-10">
        <button 
          @click="goBack" 
          class="bg-white rounded-full p-2 shadow-md hover:bg-gray-100 transition-colors"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
        </button>
      </div>

      <!-- Ride Tracking Card -->
      <div class="bg-white rounded-lg shadow-xl p-6 text-center relative">
        <!-- Cancel Ride Button -->
        <button 
          v-if="!isRideCancelled && !driver"
          @click="cancelRide"
          class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm hover:bg-red-600 transition-colors"
        >
          Cancel Ride
        </button>

        <!-- Ride Status Visualization -->
        <div class="mb-6 flex justify-center">
          <div class="relative w-32 h-32">
            <div class="absolute inset-0 bg-green-500 opacity-75 rounded-full animate-ping"></div>
            <div class="relative z-10 bg-green-600 w-32 h-32 rounded-full flex items-center justify-center">
              <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Status Message -->
        <h2 class="text-2xl font-bold mb-4 text-gray-800">
          {{ isRideCancelled ? 'Ride Cancelled' : rideStatus }}
        </h2>
        
        <!-- Ride Details -->
        <div class="mb-6 text-gray-600">
          <p class="mb-2">
            <span class="font-semibold">Pickup:</span> {{ pickup }}
          </p>
          <p>
            <span class="font-semibold">Destination:</span> {{ destination }}
          </p>
        </div>

        <!-- Driver Details Section -->
        <div v-if="driver && driver.name" class="mt-6 bg-gray-50 rounded-lg p-4 shadow-sm">
          <div class="flex items-center space-x-4">
            <!-- Driver Avatar -->
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
              <i class="fas fa-user-circle text-blue-500 text-3xl"></i>
            </div>
            
            <!-- Driver Information -->
            <div class="flex-grow">
              <h3 class="text-lg font-semibold text-gray-800">
                {{ driver.name }}
              </h3>
              
              <!-- Vehicle Details -->
              <div class="text-sm text-gray-600 mt-1">
                <div class="flex items-center">
                  <i class="fas fa-car mr-2 text-gray-500"></i>
                  <span>
                    {{ driver.vehicle.make }} {{ driver.vehicle.model }} 
                    ({{ driver.vehicle.color }})
                  </span>
                </div>
                <div class="flex items-center mt-1">
                  <i class="fas fa-id-badge mr-2 text-gray-500"></i>
                  <span>Plate: {{ driver.vehicle.plate }}</span>
                </div>
              </div>
            </div>
            
            <!-- Driver Rating -->
            <div class="text-right">
              <div class="flex items-center justify-end text-yellow-500">
                <i class="fas fa-star mr-1"></i>
                <span class="font-semibold">{{ driver.rating || 'N/A' }}</span>
              </div>
              <p class="text-xs text-gray-500">Driver Rating</p>
            </div>
          </div>
        </div>

        <!-- Cancellation Confirmation -->
        <div v-if="showCancellationConfirmation" class="mt-4">
          <p class="text-red-600 mb-2">Are you sure you want to cancel this ride?</p>
          <div class="flex justify-center space-x-4">
            <button 
              @click="confirmCancelRide"
              class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600"
            >
              Yes, Cancel Ride
            </button>
            <button 
              @click="cancelCancellation"
              class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300"
            >
              No, Keep Ride
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { rideService } from '@/services/rideService'
import { websocketService } from '@/services/websocketService'
import { toast } from '@/utils/toast'

const router = useRouter()
const route = useRoute()

// Ride tracking state
const rideId = computed(() => route.params.rideId)
const pickup = ref(null)
const destination = ref(null)
const rideStatus = ref('Finding your driver...')
const driver = ref(null)
const isRideCancelled = ref(false)
const showCancellationConfirmation = ref(false)

// WebSocket event handler
const handleRideEvents = (event) => {
  console.log('Ride Event:', event)
  
  switch(event.type) {
    case 'RIDE_ACCEPTED':
      // Update driver details when ride is accepted
      driver.value = event.driver
      rideStatus.value = 'Driver Assigned'
      toast.success('Driver has been assigned to your ride')
      break
    
    case 'RIDE_STARTED':
      rideStatus.value = 'Ride in Progress'
      toast.info('Your ride has started')
      break
    
    case 'RIDE_COMPLETED':
      rideStatus.value = 'Ride Completed'
      toast.success('Your ride has been completed')
      router.push('/ride-rating')
      break
    
    case 'DRIVER_STATUS_UPDATED':
      // Handle any driver status updates
      rideStatus.value = event.status
      break
  }
}

// Fetch ride details
const fetchRideDetails = async () => {
  try {
    const trackingDetails = await rideService.trackRide(rideId.value)
    
    // Update component state
    driver.value = trackingDetails.driver
    rideStatus.value = trackingDetails.status
    pickup.value = trackingDetails.ride.origin
    destination.value = trackingDetails.ride.destination
  } catch (error) {
    console.error('Ride tracking error:', error)
    toast.error('Unable to track ride')
    router.push('/dashboard')
  }
}

// Ride cancellation methods
const cancelRide = () => {
  showCancellationConfirmation.value = true
}

const confirmCancelRide = async () => {
  try {
    await rideService.cancelRide(rideId.value)
    
    isRideCancelled.value = true
    rideStatus.value = 'Ride Cancelled'
    showCancellationConfirmation.value = false
    
    toast.info('Ride has been cancelled')
    
    // Navigate back after a short delay
    setTimeout(() => {
      router.push('/dashboard')
    }, 2000)
  } catch (error) {
    console.error('Failed to cancel ride:', error)
    toast.error('Failed to cancel ride')
  }
}

const cancelCancellation = () => {
  showCancellationConfirmation.value = false
}

// Navigation
const goBack = () => {
  router.push('/dashboard')
}

onMounted(() => {
  // Fetch initial ride details
  fetchRideDetails()
  
  // Initialize WebSocket
  websocketService.initializeSocket()
  
  // Subscribe to specific ride channel
  websocketService.subscribeToRideChannel(`ride.${rideId.value}`)
  
  // Add event listener for ride updates
  websocketService.subscribe(`ride.${rideId.value}`, handleRideEvents)
})

onUnmounted(() => {
  // Cleanup WebSocket subscriptions
  websocketService.unsubscribe(`ride.${rideId.value}`, handleRideEvents)
  websocketService.disconnect()
})
</script>