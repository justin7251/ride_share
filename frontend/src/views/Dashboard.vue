<template>
  <BaseLayout>
    <div class="max-w-lg mx-auto px-4 py-6">
      <!-- Welcome Header -->
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Welcome!</h1>
        <p class="text-gray-600">What would you like to do today?</p>
      </div>

      <!-- Choice Cards -->
      <div class="space-y-4">
        <!-- Drive Card -->
        <div 
          @click="handleChoice('drive')"
          class="bg-white rounded-lg shadow-md p-6 cursor-pointer hover:shadow-lg transition-shadow"
        >
          <div class="flex items-center space-x-4">
            <div class="bg-green-100 p-3 rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
            <div>
              <h2 class="text-lg font-semibold text-gray-900">Start Driving</h2>
              <p class="text-gray-600">Earn money by giving rides</p>
            </div>
            <div class="ml-auto">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Ride Card -->
        <div 
          @click="handleChoice('ride')"
          class="bg-white rounded-lg shadow-md p-6 cursor-pointer hover:shadow-lg transition-shadow"
        >
          <div class="flex items-center space-x-4">
            <div class="bg-blue-100 p-3 rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
            </div>
            <div>
              <h2 class="text-lg font-semibold text-gray-900">Find a Ride</h2>
              <p class="text-gray-600">Book a ride to your destination</p>
            </div>
            <div class="ml-auto">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="mt-8 grid grid-cols-2 gap-4">
        <div class="bg-gray-50 rounded-lg p-4 text-center">
          <p class="text-sm text-gray-600">Previous Rides</p>
          <p class="text-xl font-semibold text-gray-900">{{ stats.rides }}</p>
        </div>
        <div class="bg-gray-50 rounded-lg p-4 text-center">
          <p class="text-sm text-gray-600">Total Earnings</p>
          <p class="text-xl font-semibold text-gray-900">${{ stats.earnings }}</p>
        </div>
      </div>
    </div>
  </BaseLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import BaseLayout from '../components/BaseLayout.vue'

const router = useRouter()

// State
const stats = ref({
  rides: 0,
  earnings: '0.00'
})

// Methods
const handleChoice = (choice) => {
  if (choice === 'drive') {
    router.push('/driver/dashboard')
  } else {
    router.push('/rider/search')
  }
}

// Lifecycle
onMounted(async () => {
  try {
    // You can add API call here to fetch user stats
    // const response = await userService.getStats()
    // stats.value = response.data
  } catch (error) {
    console.error('Failed to fetch stats:', error)
  }
})
</script> 