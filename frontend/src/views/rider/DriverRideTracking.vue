<template>
  <div class="driver-ride-tracking-container w-full">
    <!-- Header -->
    <div class="bg-blue-500 text-white p-4 shadow-md">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Ride Tracking</h1>
        <div class="text-sm">
          <span class="bg-blue-600 px-3 py-2 rounded">
            {{ ride.status || 'Accepted' }}
          </span>
        </div>
      </div>
    </div>

    <!-- Google Maps Container -->
    <div 
      id="map" 
      class="w-full h-screen/2 rounded-lg shadow-md"
      style="height: 50vh;"
    ></div>

    <!-- Content Container -->
    <div class="flex">
      <!-- Ride Details Column -->
      <div class="w-1/2 p-4">
        <!-- Passenger Details -->
        <div class="flex items-center mb-6">
          <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mr-6">
            <i class="fas fa-user text-gray-600 text-2xl"></i>
          </div>
          <div>
            <p class="text-xl font-semibold">{{ ride.user?.name }}</p>
            <p class="text-base text-gray-600">{{ ride.user?.phone }}</p>
          </div>
        </div>

        <!-- Ride Route -->
        <div class="mb-6">
          <div class="flex items-center mb-3">
            <i class="fas fa-map-marker-alt text-green-500 mr-4 text-xl"></i>
            <p class="text-lg font-medium">From: {{ ride.origin }}</p>
          </div>
          <div class="flex items-center">
            <i class="fas fa-flag-checkered text-red-500 mr-4 text-xl"></i>
            <p class="text-lg font-medium">To: {{ ride.destination }}</p>
          </div>
        </div>

        <!-- Vehicle Details -->
        <div v-if="ride.driver?.vehicle_info" class="bg-gray-100 p-4 rounded-lg">
          <div class="flex justify-between items-center">
            <div>
              <p class="text-xl font-semibold">
                {{ ride.driver.vehicle_info.make }} 
                {{ ride.driver.vehicle_info.model }}
              </p>
              <p class="text-base text-gray-600">
                {{ ride.driver.vehicle_info.color }} | 
                Plate: {{ ride.driver.vehicle_info.plate_number }}
              </p>
            </div>
            <div class="text-3xl text-gray-600">
              <i class="fas fa-car"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions and Additional Info Column -->
      <div class="w-1/2 p-4">
        <!-- Ride Actions -->
        <div class="space-y-4 mb-6">
          <button 
            v-if="ride.status === 'accepted'"
            @click="navigateToPickup"
            class="w-full bg-blue-500 text-white px-6 py-4 rounded-lg 
                   hover:bg-blue-600 transition duration-300 flex items-center justify-center text-lg"
          >
            <i class="fas fa-route mr-4"></i>
            Navigate to Pickup
          </button>

          <button 
            v-if="ride.status === 'accepted'"
            @click="startRide"
            class="w-full bg-green-500 text-white px-6 py-4 rounded-lg 
                   hover:bg-green-600 transition duration-300 flex items-center justify-center text-lg"
          >
            <i class="fas fa-play mr-4"></i>
            Start Ride
          </button>

          <button 
            v-if="ride.status === 'started'"
            @click="completeRide"
            class="w-full bg-purple-500 text-white px-6 py-4 rounded-lg 
                   hover:bg-purple-600 transition duration-300 flex items-center justify-center text-lg"
          >
            <i class="fas fa-flag-checkered mr-4"></i>
            Complete Ride
          </button>
        </div>

        <!-- Ride Additional Info -->
        <div class="bg-gray-100 rounded-lg p-6">
          <div class="grid grid-cols-3 gap-4 text-center">
            <div>
              <p class="font-semibold text-gray-600 text-base mb-2">Status</p>
              <p class="text-xl">{{ ride.driverStatus || 'Pending' }}</p>
            </div>
            <div>
              <p class="font-semibold text-gray-600 text-base mb-2">ETA</p>
              <p class="text-xl">{{ ride.eta || 'N/A' }} mins</p>
            </div>
            <div>
              <p class="font-semibold text-gray-600 text-base mb-2">Distance</p>
              <p class="text-xl">
                {{ ride.distance ? ride.distance.toFixed(2) : 'N/A' }} km
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
.driver-ride-tracking-container {
  @apply bg-gray-50 w-full md:w-[70%] sm:w-[90%] mx-auto;
}

#map {
  height: 50vh;
  width: 100%;
}
</style>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { rideService } from '@/services/rideService'
import { websocketService } from '@/services/websocketService'

const route = useRoute()
const router = useRouter()

// Use route params to get ride ID
const rideId = computed(() => route.params.rideId)

// Optional: Use query params for additional context
const origin = computed(() => route.query.origin)
const destination = computed(() => route.query.destination)

const ride = ref({
  user: null,
  driver: null,
  origin: origin.value || '',
  destination: destination.value || '',
  status: 'accepted',
  pickup_lat: null,
  pickup_lng: null,
  destination_lat: null,
  destination_lng: null
})

// Google Maps methods
const initializeMap = () => {
  // Safely check coordinates
  if (!ride.value.pickup_lat || !ride.value.pickup_lng || 
      !ride.value.destination_lat || !ride.value.destination_lng) {
    console.error('Incomplete coordinates')
    return
  }

  const pickupCoords = {
    lat: parseFloat(ride.value.pickup_lat),
    lng: parseFloat(ride.value.pickup_lng)
  }

  const destinationCoords = {
    lat: parseFloat(ride.value.destination_lat),
    lng: parseFloat(ride.value.destination_lng)
  }

  // Initialize map with pickup coordinates
  const map = new google.maps.Map(document.getElementById('map'), {
    center: pickupCoords,
    zoom: 15,
    disableDefaultUI: true, // Optional: Remove default Google Maps controls
    styles: [
      // Optional: Custom map styling for cleaner look
      {
        featureType: 'poi',
        elementType: 'labels',
        stylers: [{ visibility: 'off' }]
      }
    ]
  })

  // Add markers and route
  const directionsService = new google.maps.DirectionsService()
  const directionsRenderer = new google.maps.DirectionsRenderer()
  directionsRenderer.setMap(map)

  directionsService.route(
    {
      origin: pickupCoords,
      destination: destinationCoords,
      travelMode: 'DRIVING'
    },
    (response, status) => {
      if (status === 'OK') {
        directionsRenderer.setDirections(response)
      }
    }
  )
}

// Ride Actions
const navigateToPickup = () => {
  // Implement navigation logic
  console.log('Navigating to pickup')
}

const startRide = async () => {
  try {
    const response = await rideService.startRide(rideId.value)
    ride.value = response.ride
    
    // Update map to show destination route
    initializeMap()
  } catch (error) {
    console.error('Failed to start ride:', error)
  }
}

const completeRide = async () => {
  try {
    const response = await rideService.completeRide(rideId.value)
    router.push('/driver/dashboard')
  } catch (error) {
    console.error('Failed to complete ride:', error)
  }
}

// Fetch ride details on component mount
const fetchRideDetails = async () => {
  try {
    const rideDetails = await rideService.trackRide(rideId.value)
    
    ride.value = {
      ...rideDetails.ride,
      driverStatus: rideDetails.status,
      eta: rideDetails.eta,
      distance: rideDetails.distance
    }

    console.log('Ride Details:', ride.value)
  } catch (error) {
    console.error('Failed to fetch ride details:', error)
    // router.push('/driver/dashboard')
  }
}

onMounted(async () => {
  await fetchRideDetails()
  
  // Load Google Maps script
  const script = document.createElement('script')
  script.src = `https://maps.googleapis.com/maps/api/js?key=${import.meta.env.VITE_GOOGLE_MAPS_API_KEY}&libraries=places`
  script.async = true
  script.defer = true
  script.onload = initializeMap
  document.head.appendChild(script)

  websocketService.initializeDriverSocket()
})

onUnmounted(() => {
  websocketService.stopListeningForRides()
})
</script>

