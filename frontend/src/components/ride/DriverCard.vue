<template>
  <div v-if="driver?.id" class="bg-white rounded-lg shadow-md p-4">
    <div class="flex items-center">
      <!-- Driver Photo -->
      <div class="flex-shrink-0">
        <img 
          :src="driver?.photo || '/default-avatar.png'"
          alt="Driver"
          class="w-16 h-16 rounded-full object-cover"
        >
      </div>
      
      <!-- Driver Info -->
      <div class="ml-4 flex-1">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="font-medium text-gray-900">{{ driver?.name || 'Loading...' }}</h3>
            <div class="flex items-center text-sm text-gray-600">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              <span class="ml-1">{{ driver?.rating || '0' }} ({{ driver?.totalRides || '0' }}+ rides)</span>
            </div>
          </div>
        </div>

        <!-- Vehicle Info -->
        <div class="mt-2 flex items-center text-sm text-gray-600">
          <span>{{ driver?.vehicle?.model || '-' }}</span>
          <span class="mx-2">•</span>
          <span>{{ driver?.vehicle?.color || '-' }}</span>
          <span class="mx-2">•</span>
          <span>{{ driver?.vehicle?.plate || '-' }}</span>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="bg-white rounded-lg shadow-md p-4">
    <div class="animate-pulse flex space-x-4">
      <div class="rounded-full bg-gray-200 h-16 w-16"></div>
      <div class="flex-1 space-y-3">
        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
        <div class="h-4 bg-gray-200 rounded w-2/3"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  driver: {
    type: Object,
    default: () => ({
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
  }
})
</script> 