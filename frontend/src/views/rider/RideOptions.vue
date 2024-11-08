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
          <h1 class="ml-4 text-xl font-semibold text-gray-900">Available Rides</h1>
        </div>
      </div>
    </div>

    <!-- Route Info -->
    <div class="bg-white border-b">
      <div class="max-w-lg mx-auto px-4 py-4">
        <div class="flex items-center space-x-4">
          <div class="flex-1">
            <p class="text-sm text-gray-600">From</p>
            <p class="font-medium text-gray-900">{{ pickup }}</p>
          </div>
          <div class="text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="flex-1">
            <p class="text-sm text-gray-600">To</p>
            <p class="font-medium text-gray-900">{{ destination }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Ride Options -->
    <div class="max-w-lg mx-auto px-4 py-6">
      <div class="space-y-4">
        <div 
          v-for="option in rideOptions" 
          :key="option.id"
          @click="selectRide(option)"
          :class="[
            'bg-white rounded-lg shadow-md p-6 cursor-pointer transition-shadow',
            selectedRide?.id === option.id ? 'ring-2 ring-green-500' : 'hover:shadow-lg'
          ]"
        >
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
              </div>
            </div>
            <div class="ml-4 flex-1">
              <div class="flex justify-between">
                <div>
                  <h3 class="text-lg font-medium text-gray-900">{{ option.name }}</h3>
                  <p class="text-sm text-gray-600">{{ option.description }}</p>
                </div>
                <p class="text-lg font-medium text-gray-900">${{ option.price }}</p>
              </div>
              <div class="mt-1 text-sm text-gray-600">
                <span>{{ option.time }} min</span>
                <span class="mx-2">•</span>
                <span>{{ option.distance }} km</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Book Button -->
      <button
        v-if="selectedRide"
        @click="bookRide"
        :disabled="isLoading"
        class="w-full mt-6 py-3 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span v-if="isLoading">
          <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Booking...
        </span>
        <span v-else>
          Book Now - ${{ selectedRide.price }}
        </span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { rideService } from '@/services/rideService'

const router = useRouter()
const route = useRoute()

// Props from route
const pickup = ref(route.params.pickup || '')
const destination = ref(route.params.destination || '')

// State
const selectedRide = ref(null)
const isLoading = ref(false)

// Sample data
const rideOptions = ref([
  {
    id: 1,
    name: 'Economy',
    description: 'Affordable rides for everyone',
    price: '12.50',
    time: 15,
    distance: '3.5'
  },
  {
    id: 2,
    name: 'Comfort',
    description: 'More legroom, newer cars',
    price: '18.00',
    time: 15,
    distance: '3.5'
  },
  {
    id: 3,
    name: 'Premium',
    description: 'Luxury vehicles with top-rated drivers',
    price: '25.00',
    time: 15,
    distance: '3.5'
  }
])

// Methods
const goBack = () => {
  router.push('/rider/search')
}

const selectRide = (option) => {
  selectedRide.value = option
}

const bookRide = async () => {
  if (!selectedRide.value) return
  
  isLoading.value = true
  try {
    const response = await rideService.bookRide({
      pickup: pickup.value,
      destination: destination.value,
      rideType: selectedRide.value.name.toLowerCase()
    })
    
    router.push({
      name: 'ride-tracking',
      params: { rideId: response.rideId }
    })
  } catch (error) {
    console.error('Booking error:', error)
  } finally {
    isLoading.value = false
  }
}
</script> 