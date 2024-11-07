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

    <!-- Driver Details Card -->
    <div class="max-w-lg mx-auto px-4 py-4">
      <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center">
          <!-- Driver Photo -->
          <div class="flex-shrink-0">
            <img 
              :src="driver.photo" 
              alt="Driver"
              class="w-16 h-16 rounded-full object-cover"
            >
          </div>
          
          <!-- Driver Info -->
          <div class="ml-4 flex-1">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="font-medium text-gray-900">{{ driver.name }}</h3>
                <div class="flex items-center text-sm text-gray-600">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <span class="ml-1">{{ driver.rating }} ({{ driver.totalRides }}+ rides)</span>
                </div>
              </div>
              
              <!-- Contact Buttons -->
              <div class="flex space-x-2">
                <button 
                  @click="callDriver"
                  class="p-2 rounded-full bg-green-100 text-green-600 hover:bg-green-200"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                  </svg>
                </button>
                <button 
                  @click="messageDriver"
                  class="p-2 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Vehicle Info -->
            <div class="mt-2 flex items-center text-sm text-gray-600">
              <span>{{ driver.vehicle.model }}</span>
              <span class="mx-2">•</span>
              <span>{{ driver.vehicle.color }}</span>
              <span class="mx-2">•</span>
              <span>{{ driver.vehicle.plate }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Trip Progress -->
    <div class="max-w-lg mx-auto px-4 py-4">
      <div class="bg-white rounded-lg shadow-md p-4">
        <div class="space-y-4">
          <!-- Pickup -->
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                <div class="w-3 h-3 bg-green-600 rounded-full"></div>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Pickup</p>
              <p class="font-medium text-gray-900">{{ tripDetails.pickup }}</p>
            </div>
          </div>

          <!-- Progress Line -->
          <div class="ml-4 border-l-2 border-dashed border-gray-200 h-8"></div>

          <!-- Destination -->
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                <div class="w-3 h-3 bg-red-600 rounded-full"></div>
              </div>
            </div>
            <div class="ml-4">
              <p class="text-sm text-gray-600">Destination</p>
              <p class="font-medium text-gray-900">{{ tripDetails.destination }}</p>
            </div>
          </div>
        </div>

        <!-- ETA -->
        <div class="mt-4 pt-4 border-t">
          <div class="flex justify-between items-center">
            <div>
              <p class="text-sm text-gray-600">Estimated Time</p>
              <p class="font-medium text-gray-900">{{ tripDetails.eta }} min</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Distance</p>
              <p class="font-medium text-gray-900">{{ tripDetails.distance }} km</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Cancellation Modal -->
    <div 
      v-if="showCancelModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
    >
      <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Cancel Ride?</h3>
        
        <p class="text-gray-600 mb-4">
          Cancellation fee of $5.00 may apply. Are you sure you want to cancel?
        </p>

        <div class="space-y-2">
          <button
            @click="cancelRide"
            class="w-full py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700"
          >
            Yes, Cancel Ride
          </button>
          <button
            @click="showCancelModal = false"
            class="w-full py-2 px-4 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
          >
            No, Keep Ride
          </button>
        </div>
      </div>
    </div>

    <!-- Map View -->
    <div class="h-[300px] relative">
      <div id="map" class="w-full h-full"></div>
      
      <!-- Fare Meter Overlay -->
      <div class="absolute top-4 right-4 bg-white rounded-lg shadow-lg p-4">
        <div class="text-sm text-gray-600">Current Fare</div>
        <div class="text-xl font-bold text-gray-900">${{ currentFare }}</div>
        <div class="text-xs text-gray-500">{{ tripDuration }} mins</div>
      </div>
    </div>

    <!-- Emergency & Share Buttons -->
    <div class="max-w-lg mx-auto px-4 py-2">
      <div class="flex justify-between space-x-4">
        <button 
          @click="showEmergencyModal = true"
          class="flex-1 flex items-center justify-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zm0 16a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
          </svg>
          Emergency
        </button>
        
        <button 
          @click="showShareModal = true"
          class="flex-1 flex items-center justify-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
          </svg>
          Share Trip
        </button>
      </div>
    </div>

    <!-- Emergency Contacts Modal -->
    <div v-if="showEmergencyModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Emergency Options</h3>
          <button @click="showEmergencyModal = false" class="text-gray-400 hover:text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="space-y-4">
          <button 
            @click="callEmergency('911')"
            class="w-full flex items-center justify-between p-4 bg-red-50 text-red-700 rounded-lg hover:bg-red-100"
          >
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
              </svg>
              Call Emergency Services (911)
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
            </svg>
          </button>

          <div class="border-t border-gray-200 pt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Emergency Contacts</h4>
            <div class="space-y-2">
              <button 
                v-for="contact in emergencyContacts" 
                :key="contact.id"
                @click="callEmergency(contact.phone)"
                class="w-full flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100"
              >
                <div>
                  <div class="font-medium text-gray-900">{{ contact.name }}</div>
                  <div class="text-sm text-gray-600">{{ contact.relation }}</div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                </svg>
              </button>
            </div>
          </div>

          <button 
            @click="sendSOS"
            class="w-full py-3 bg-red-600 text-white rounded-lg hover:bg-red-700"
          >
            Send SOS Alert
          </button>
        </div>
      </div>
    </div>

    <!-- Share Trip Modal -->
    <div v-if="showShareModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Share Trip Details</h3>
          <button @click="showShareModal = false" class="text-gray-400 hover:text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="space-y-4">
          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="text-sm text-gray-600">Trip Link</p>
            <div class="flex items-center mt-1">
              <input 
                type="text" 
                :value="tripShareLink" 
                readonly
                class="flex-1 bg-white border rounded-l-lg px-3 py-2 text-sm"
              >
              <button 
                @click="copyShareLink"
                class="bg-blue-600 text-white px-4 py-2 rounded-r-lg hover:bg-blue-700"
              >
                Copy
              </button>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-4">
            <button 
              v-for="platform in sharePlatforms" 
              :key="platform.name"
              @click="shareTrip(platform.name)"
              class="flex flex-col items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100"
            >
              <img :src="platform.icon" :alt="platform.name" class="w-8 h-8 mb-2">
              <span class="text-sm text-gray-600">{{ platform.name }}</span>
            </button>
          </div>

          <div class="border-t border-gray-200 pt-4">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Share with Trusted Contacts</h4>
            <div class="space-y-2">
              <button 
                v-for="contact in trustedContacts" 
                :key="contact.id"
                @click="shareWithContact(contact)"
                class="w-full flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100"
              >
                <div class="flex items-center">
                  <img :src="contact.photo" :alt="contact.name" class="w-8 h-8 rounded-full">
                  <div class="ml-3">
                    <div class="font-medium text-gray-900">{{ contact.name }}</div>
                    <div class="text-sm text-gray-600">{{ contact.phone }}</div>
                  </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M8.707 7.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l2-2a1 1 0 00-1.414-1.414L11 7.586V3a1 1 0 10-2 0v4.586l-.293-.293z" />
                  <path d="M3 5a2 2 0 012-2h1a1 1 0 010 2H5v7h2l1 2h4l1-2h2V5h-1a1 1 0 110-2h1a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V5z" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ETA Status Bar -->
    <div class="bg-white shadow-sm sticky top-0 z-10">
      <div class="max-w-lg mx-auto px-4 py-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <div class="bg-green-100 rounded-full p-2 mr-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
              </svg>
            </div>
            <div>
              <p class="text-sm text-gray-600">Estimated Arrival</p>
              <p class="font-medium text-gray-900">{{ formatETA(currentETA) }}</p>
            </div>
          </div>
          <div class="text-right">
            <p class="text-sm text-gray-600">Distance</p>
            <p class="font-medium text-gray-900">{{ remainingDistance }} km</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Route Progress -->
    <div class="max-w-lg mx-auto px-4 py-3 bg-white border-b">
      <div class="flex items-center space-x-4">
        <div class="flex-1">
          <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
            <div 
              class="h-full bg-green-500 transition-all duration-500"
              :style="{ width: `${tripProgress}%` }"
            ></div>
          </div>
        </div>
        <div class="text-sm font-medium text-gray-900">
          {{ Math.round(tripProgress) }}%
        </div>
      </div>
    </div>

    <!-- Traffic Updates -->
    <div v-if="trafficAlerts.length > 0" class="max-w-lg mx-auto px-4 py-2">
      <div 
        v-for="alert in trafficAlerts" 
        :key="alert.id"
        class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-2"
      >
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-amber-800">{{ alert.title }}</h3>
            <p class="text-sm text-amber-700 mt-1">{{ alert.description }}</p>
            <p class="text-xs text-amber-600 mt-1">Delay: +{{ alert.delay }} mins</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { format, addMinutes } from 'date-fns'

const route = useRoute()
const currentETA = ref(new Date())
const initialETA = ref(new Date())
const remainingDistance = ref(0)
const trafficAlerts = ref([])
const tripProgress = ref(0)

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
    const data = await response.json()
    
    currentETA.value = new Date(data.eta)
    remainingDistance.value = data.remainingDistance.toFixed(1)
    tripProgress.value = data.progress
    
    // Update traffic alerts
    if (data.trafficAlerts) {
      trafficAlerts.value = data.trafficAlerts
    }
  } catch (error) {
    console.error('Failed to update ETA:', error)
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

// Create ETA service
const etaService = {
  async startTracking(rideId) {
    try {
      const response = await fetch(`/api/rides/${rideId}/start-tracking`, {
        method: 'POST'
      })
      return response.json()
    } catch (error) {
      console.error('Failed to start ETA tracking:', error)
    }
  },

  async stopTracking(rideId) {
    try {
      await fetch(`/api/rides/${rideId}/stop-tracking`, {
        method: 'POST'
      })
    } catch (error) {
      console.error('Failed to stop ETA tracking:', error)
    }
  }
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
})

onUnmounted(() => {
  if (updateInterval) {
    clearInterval(updateInterval)
  }
  etaService.stopTracking(route.params.rideId)
})
</script>

<style scoped>
.progress-transition {
  transition: width 0.5s ease-in-out;
}
</style> 