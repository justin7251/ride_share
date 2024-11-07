<template>
  <div>
    <!-- Navigation Header -->
    <div class="bg-white shadow-sm">
      <div class="max-w-lg mx-auto px-4 py-4">
        <div class="flex items-center">
          <button 
            @click="goBack" 
            class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <h1 class="ml-4 text-xl font-semibold text-gray-900">Find a Ride</h1>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-lg mx-auto px-4 py-6">
      <!-- Location Inputs -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="space-y-4">
          <!-- Pickup Location -->
          <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-1">Pickup Location</label>
            <div class="relative">
              <input
                v-model="pickup"
                type="text"
                placeholder="Enter pickup location"
                class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                @focus="showPickupSuggestions = true"
              >
              <div class="absolute left-3 top-2.5 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                </svg>
              </div>
            </div>
            <!-- Pickup Suggestions -->
            <div v-if="showPickupSuggestions && pickup" class="absolute z-10 w-full mt-1 bg-white rounded-lg shadow-lg">
              <div 
                v-for="suggestion in pickupSuggestions" 
                :key="suggestion.id"
                @click="selectPickup(suggestion)"
                class="px-4 py-2 hover:bg-gray-50 cursor-pointer"
              >
                <p class="font-medium text-gray-900">{{ suggestion.name }}</p>
                <p class="text-sm text-gray-600">{{ suggestion.address }}</p>
              </div>
            </div>
          </div>

          <!-- Destination -->
          <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-1">Destination</label>
            <div class="relative">
              <input
                v-model="destination"
                type="text"
                placeholder="Enter destination"
                class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                @focus="showDestSuggestions = true"
              >
              <div class="absolute left-3 top-2.5 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
            </div>
            <!-- Destination Suggestions -->
            <div v-if="showDestSuggestions && destination" class="absolute z-10 w-full mt-1 bg-white rounded-lg shadow-lg">
              <div 
                v-for="suggestion in destSuggestions" 
                :key="suggestion.id"
                @click="selectDestination(suggestion)"
                class="px-4 py-2 hover:bg-gray-50 cursor-pointer"
              >
                <p class="font-medium text-gray-900">{{ suggestion.name }}</p>
                <p class="text-sm text-gray-600">{{ suggestion.address }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Search Button -->
      <button
        @click="searchRides"
        :disabled="!isValid || isLoading"
        class="w-full py-3 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span v-if="isLoading" class="flex items-center justify-center">
          <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Searching...
        </span>
        <span v-else>Search Rides</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// State
const pickup = ref('')
const destination = ref('')
const showPickupSuggestions = ref(false)
const showDestSuggestions = ref(false)
const isLoading = ref(false)

// Computed
const isValid = computed(() => {
  return pickup.value.length > 0 && destination.value.length > 0
})

const pickupSuggestions = computed(() => {
  if (!pickup.value) return []
  return [
    { id: 1, name: 'Current Location', address: 'Using GPS' },
    { id: 2, name: 'Home', address: '123 Home Street' },
    { id: 3, name: 'Work', address: '456 Office Road' },
  ]
})

const destSuggestions = computed(() => {
  if (!destination.value) return []
  return [
    { id: 1, name: 'Central Mall', address: '789 Shopping Avenue' },
    { id: 2, name: 'Airport', address: 'International Terminal' },
    { id: 3, name: 'Downtown', address: 'City Center' },
  ]
})

// Methods
const goBack = () => {
  router.push('/')
}

const selectPickup = (suggestion) => {
  pickup.value = suggestion.name
  showPickupSuggestions.value = false
}

const selectDestination = (suggestion) => {
  destination.value = suggestion.name
  showDestSuggestions.value = false
}

const searchRides = async () => {
  if (!isValid.value) return
  
  isLoading.value = true
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500))
    router.push({
      path: '/rider/options',
      query: {
        pickup: pickup.value,
        destination: destination.value
      }
    })
  } catch (error) {
    console.error('Search error:', error)
  } finally {
    isLoading.value = false
  }
}

// Click outside to close suggestions
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showPickupSuggestions.value = false
    showDestSuggestions.value = false
  }
}

// Lifecycle
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script> 