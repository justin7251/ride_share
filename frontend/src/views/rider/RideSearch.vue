<template>
  <div>
    <!-- Navigation Header -->
    <div class="bg-white shadow-sm">
      <div class="max-w-lg mx-auto px-4 py-4">
        <div class="flex items-center">
          <button 
            @click="router.push('/dashboard')" 
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

    <!-- Search Form -->
    <div class="max-w-lg mx-auto px-4 py-6">
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="space-y-6">
          <!-- Pickup Location -->
          <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                <div class="w-3 h-3 bg-green-600 rounded-full"></div>
              </div>
            </div>
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-700">Pickup Location</label>
              <input 
                v-model="pickup"
                type="text"
                class="mt-1 block w-full border-0 border-b focus:border-green-500 focus:ring-0"
                placeholder="Enter pickup location"
                required
              >
            </div>
          </div>

          <!-- Progress Line -->
          <div class="ml-6 border-l-2 border-dashed border-gray-200 h-8"></div>

          <!-- Destination -->
          <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                <div class="w-3 h-3 bg-red-600 rounded-full"></div>
              </div>
            </div>
            <div class="flex-1">
              <label class="block text-sm font-medium text-gray-700">Destination</label>
              <input 
                v-model="destination"
                type="text"
                class="mt-1 block w-full border-0 border-b border-gray-300 focus:border-green-500 focus:ring-0"
                placeholder="Enter destination"
                required
              >
            </div>
          </div>

          <!-- Search Button -->
          <button
            @click="searchRide"
            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
            :disabled="isSearching"
          >
            <span v-if="isSearching">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Searching...
            </span>
            <span v-else>Find Ride</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { rideService } from '@/services/rideService'

const router = useRouter()
const pickup = ref('')
const destination = ref('')
const isSearching = ref(false)

const searchRide = async () => {
  if (!pickup.value || !destination.value) return
  
  isSearching.value = true
  try {
    // First search for available drivers
    const driversResponse = await rideService.searchDrivers(pickup.value, destination.value)
    
    if (!driversResponse.success || !driversResponse.drivers.length) {
      throw new Error('No drivers available')
    }

    // Then create a ride request
    const rideResponse = await rideService.searchRide(pickup.value, destination.value)
    
    router.push({
      name: 'ride-options',
      params: {
        rideId: rideResponse.rideId,
        pickup: pickup.value,
        destination: destination.value,
        availableDrivers: driversResponse.drivers.length
      }
    })
  } catch (error) {
    console.error('Failed to search for ride:', error)
    // TODO: Show error notification to user
  } finally {
    isSearching.value = false
  }
}
</script> 