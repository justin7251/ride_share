<template>
  <div v-if="rideDetails?.id" class="bg-white rounded-lg shadow-md p-4">
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
          <p class="font-medium text-gray-900">{{ rideDetails?.pickup || 'Loading...' }}</p>
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
          <p class="font-medium text-gray-900">{{ rideDetails?.destination || 'Loading...' }}</p>
        </div>
      </div>
    </div>

    <!-- ETA and Distance -->
    <div class="mt-4 pt-4 border-t">
      <div class="flex justify-between items-center">
        <div>
          <p class="text-sm text-gray-600">Estimated Time</p>
          <p class="font-medium text-gray-900">{{ rideDetails?.eta || '0' }} min</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Distance</p>
          <p class="font-medium text-gray-900">{{ rideDetails?.distance || '0' }} km</p>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="bg-white rounded-lg shadow-md p-4">
    <div class="animate-pulse space-y-4">
      <div class="h-4 bg-gray-200 rounded w-3/4"></div>
      <div class="h-4 bg-gray-200 rounded w-1/2"></div>
      <div class="h-4 bg-gray-200 rounded w-2/3"></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  rideDetails: {
    type: Object,
    default: () => ({
      id: null,
      pickup: '',
      destination: '',
      eta: 0,
      distance: 0
    })
  }
})
</script> 