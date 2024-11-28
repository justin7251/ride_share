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
          <h1 class="ml-4 text-xl font-semibold text-gray-900">Driver Dashboard</h1>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-lg mx-auto px-4 py-6">
      <!-- Status Toggle -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-semibold text-gray-900">Driver Status</h2>
            <p class="text-sm text-gray-600">
              {{ isOnline ? 'You are online and can receive ride requests' : 'Go online to start receiving requests' }}
            </p>
          </div>
          <button
            @click="toggleOnlineStatus"
            :class="[
              'px-6 py-2 rounded-full font-medium transition-colors',
              isOnline
                ? 'bg-green-600 text-white hover:bg-green-700'
                : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
            ]"
          >
            {{ isOnline ? 'Online' : 'Offline' }}
          </button>
        </div>
      </div>

      <!-- Today's Stats -->
      <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-4">
          <p class="text-sm text-gray-600">Today's Earnings</p>
          <p class="text-2xl font-semibold text-gray-900">${{ todayStats.earnings }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4">
          <p class="text-sm text-gray-600">Completed rides</p>
          <p class="text-2xl font-semibold text-gray-900">{{ todayStats.rides }}</p>
        </div>
      </div>

      <!-- Recent rides -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Rides</h2>
        
        <div v-if="recentrides.length > 0" class="space-y-4">
          <div 
            v-for="ride in recentrides" 
            :key="ride.id"
            class="border-b last:border-b-0 pb-4 last:pb-0"
          >
            <div class="flex justify-between items-start mb-2">
              <div>
                <p class="font-medium text-gray-900">{{ ride.destination }}</p>
                <p class="text-sm text-gray-600">{{ ride.date }}</p>
              </div>
              <p class="font-medium text-green-600">${{ ride.earnings }}</p>
            </div>
            <div class="flex items-center text-sm text-gray-600">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ ride.duration }} • {{ ride.distance }}
            </div>
          </div>
        </div>

        <div 
          v-else 
          class="text-center py-8 text-gray-600"
        >
          No rides yet today
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { driverService } from '@/services/driverService'

const router = useRouter()
const isOnline = ref(false)
const initialStatusLoaded = ref(false)

// State
const todayStats = ref({
  earnings: '0.00',
  rides: 0
})

const recentrides = ref([
  {
    id: 1,
    destination: 'Central Mall',
    date: '2 hours ago',
    earnings: '15.00',
    duration: '25 mins',
    distance: '3.5 km'
  },
  {
    id: 2,
    destination: 'Airport Terminal 2',
    date: '4 hours ago',
    earnings: '32.50',
    duration: '45 mins',
    distance: '12.8 km'
  }
])

// Navigation
const goBack = () => {
  router.push('/')
}

// Methods
const toggleOnlineStatus = async () => {
  try {
    const newStatus = !isOnline.value;

    if (newStatus) {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(async (position) => {
          const { latitude, longitude } = position.coords;

          // Update the driver's status and location
          const response = await driverService.updateStatus('active', {
            lat: latitude,
            lng: longitude
          });

          if (response.status === 'success') {
            isOnline.value = true;
            startListeningForrides();
          }
        }, (error) => {
          console.error('Error getting location:', error);
          alert('Location access is required to go online.');
          isOnline.value = false;
        });
      } else {
        alert('Geolocation is not supported by this browser.');
        isOnline.value = false;
      }
    } else {
      const response = await driverService.updateStatus('inactive');
      if (response.status === 'success') {
        isOnline.value = false;
        stopListeningForrides();
      }
    }
  } catch (error) {
    console.error('Failed to update status:', error);
  }
};

const startListeningForrides = () => {
  window.Echo.channel('rides')
    .listen('ridestarted', (event) => {
      console.log('New ride started:', event.ride);
      // Handle new ride request
    })
    .listen('rideAccepted', (event) => {
      console.log('ride accepted:', event.ride);
      // Handle ride acceptance
    })
    .listen('rideLocationUpdated', (event) => {
      console.log('ride location updated:', event.ride, event.location);
      // Handle location update
    })
    .listen('rideCompleted', (event) => {
      console.log('ride completed:', event.ride);
      // Handle ride completion
    });
};

const stopListeningForrides = () => {
  if (window.Echo) {
    window.Echo.leaveChannel('rides');
  }
};

// Only fetch status once when component mounts
onMounted(async () => {
  if (!initialStatusLoaded.value) {
    console.log('Fetching driver status');
    try {
      const response = await driverService.getStatus()
      isOnline.value = response.driver_status === 'active'
      initialStatusLoaded.value = true
    } catch (error) {
      console.error('Failed to fetch driver status:', error)
    }
  }
})

onUnmounted(() => {
  stopListeningForrides()
})
</script> 