<template>
  <BaseLayout>
    <div class="max-w-lg mx-auto px-4 py-6">
      <!-- Page Header -->
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Where are you going?</h1>
        <p class="text-gray-600">Enter your pickup and destination</p>
      </div>

      <!-- Search Container -->
      <div class="bg-white rounded-lg shadow-md p-6 space-y-4">
        <!-- Pickup Input -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Pickup Location</label>
          <div class="relative">
            <input 
              id="pickup-input"
              ref="pickupInputRef"
              v-model="pickup"
              @input="handlePickupInput"
              @keydown="handlePickupKeyDown"
              placeholder="Enter pickup location"
              class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none"
            />
            
            <!-- Pickup Suggestions Dropdown -->
            <ul 
              v-if="pickupSuggestions.length" 
              class="absolute z-10 w-full bg-white border rounded-lg shadow-lg max-h-60 overflow-y-auto mt-1"
            >
              <li 
                v-for="(suggestion, index) in pickupSuggestions" 
                :key="suggestion.place_id"
                :class="[
                  'p-3 cursor-pointer hover:bg-gray-100 transition-colors',
                  { 'bg-green-100': index === selectedPickupIndex }
                ]"
                @click="selectPickupSuggestion(suggestion)"
              >
                {{ suggestion.description }}
              </li>
            </ul>
          </div>
        </div>

        <!-- Destination Input -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Destination</label>
          <div class="relative">
            <input 
              id="destination-input"
              ref="destinationInputRef"
              v-model="destination"
              @input="handleDestinationInput"
              @keydown="handleDestinationKeyDown"
              placeholder="Enter destination"
              class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none"
            />
            
            <!-- Destination Suggestions Dropdown -->
            <ul 
              v-if="destinationSuggestions.length" 
              class="absolute z-10 w-full bg-white border rounded-lg shadow-lg max-h-60 overflow-y-auto mt-1"
            >
              <li 
                v-for="(suggestion, index) in destinationSuggestions" 
                :key="suggestion.place_id"
                :class="[
                  'p-3 cursor-pointer hover:bg-gray-100 transition-colors',
                  { 'bg-green-100': index === selectedDestinationIndex }
                ]"
                @click="selectDestinationSuggestion(suggestion)"
              >
                {{ suggestion.description }}
              </li>
            </ul>
          </div>
        </div>

        <!-- Search Button with loading and error states -->
        <button 
          @click="searchRides"
          :disabled="isSearching"
          class="w-full mt-4 py-3 bg-green-600 text-white rounded-lg"
        >
          {{ isSearching ? 'Finding a driver...' : 'Search Rides' }}
        </button>

        <!-- Error Message -->
        <div v-if="searchError" class="mt-4 text-red-600 text-center">
          {{ searchError }}
        </div>
      </div>
    </div>
  </BaseLayout>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import { rideService } from '@/services/rideService'
import BaseLayout from '@/components/BaseLayout.vue'

const router = useRouter()

// State variables
const pickup = ref('')
const destination = ref('')
const pickupLat = ref(null)
const pickupLng = ref(null)
const destinationLat = ref(null)
const destinationLng = ref(null)
const isSearching = ref(false)
const searchError = ref(null)
const pickupSuggestions = ref([])
const destinationSuggestions = ref([])
const selectedPickupIndex = ref(-1)
const selectedDestinationIndex = ref(-1)

const pickupInputRef = ref(null)
const destinationInputRef = ref(null)

// Debugging function to log coordinates
const logCoordinates = () => {
  console.log('Pickup Coordinates:', {
    address: pickup.value,
    lat: pickupLat.value,
    lng: pickupLng.value
  })
  console.log('Destination Coordinates:', {
    address: destination.value,
    lat: destinationLat.value,
    lng: destinationLng.value
  })
}

// Utility: Debounce function
const debounce = (func, delay) => {
  let timeoutId
  return (...args) => {
    clearTimeout(timeoutId)
    timeoutId = setTimeout(() => func.apply(null, args), delay)
  }
}

// Initialization function
const initializeAutocomplete = () => {
  nextTick(() => {
    if (!window.google || !window.google.maps || !window.google.maps.places) {
      console.error('Google Maps API not loaded')
      return
    }

    const pickupInput = document.getElementById('pickup-input')
    const destinationInput = document.getElementById('destination-input')

    if (!pickupInput || !destinationInput) {
      console.error('Input elements not found')
      return
    }

    // Create autocomplete for pickup
    const pickupAutocomplete = new window.google.maps.places.Autocomplete(pickupInput, {
      types: ['geocode', 'establishment'],
      componentRestrictions: { country: 'gb' }
    })

    // Create autocomplete for destination
    const destinationAutocomplete = new window.google.maps.places.Autocomplete(destinationInput, {
      types: ['geocode', 'establishment'],
      componentRestrictions: { country: 'gb' }
    })

    // Pickup place change listener
    pickupAutocomplete.addListener('place_changed', () => {
      const place = pickupAutocomplete.getPlace()
      if (place.geometry) {
        pickup.value = place.formatted_address
        pickupLat.value = place.geometry.location.lat()
        pickupLng.value = place.geometry.location.lng()
      }
    })

    // Destination place change listener
    destinationAutocomplete.addListener('place_changed', () => {
      const place = destinationAutocomplete.getPlace()
      if (place.geometry) {
        destination.value = place.formatted_address
        destinationLat.value = place.geometry.location.lat()
        destinationLng.value = place.geometry.location.lng()
      }
    })
  })
}

// Pickup Location Handlers
const handlePickupInput = debounce((query) => {
  if (!query || query.length < 3) {
    pickupSuggestions.value = []
    return
  }

  const autocompleteService = new window.google.maps.places.AutocompleteService()
  autocompleteService.getPlacePredictions(
    { 
      input: query,
      types: ['geocode', 'establishment'],
      componentRestrictions: { country: 'us' }
    }, 
    (predictions, status) => {
      if (status === window.google.maps.places.PlacesServiceStatus.OK) {
        pickupSuggestions.value = predictions || []
        selectedPickupIndex.value = -1
      }
    }
  )
}, 300)

// Enhanced suggestion selection method
const selectPickupSuggestion = (suggestion) => {
  const placesService = new window.google.maps.places.PlacesService(document.createElement('div'))
  
  placesService.getDetails(
    { 
      placeId: suggestion.place_id,
      fields: ['geometry', 'formatted_address'] 
    }, 
    (place, status) => {
      if (status === window.google.maps.places.PlacesServiceStatus.OK) {
        // Ensure we have a valid geometry
        if (place.geometry && place.geometry.location) {
          pickup.value = place.formatted_address || suggestion.description
          
          // Explicitly get lat and lng
          const location = place.geometry.location
          pickupLat.value = typeof location.lat === 'function' ? location.lat() : location.lat
          pickupLng.value = typeof location.lng === 'function' ? location.lng() : location.lng
          
          // Clear suggestions
          pickupSuggestions.value = []
          
          // Log for debugging
          console.log('Pickup Selected:', {
            address: pickup.value,
            lat: pickupLat.value,
            lng: pickupLng.value
          })
        } else {
          searchError.value = 'Unable to retrieve pickup location coordinates'
          pickupLat.value = null
          pickupLng.value = null
        }
      } else {
        searchError.value = 'Failed to retrieve place details'
        pickupLat.value = null
        pickupLng.value = null
      }
    }
  )
}

const handlePickupKeyDown = (event) => {
  if (!pickupSuggestions.value.length) return

  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault()
      selectedPickupIndex.value = Math.min(
        selectedPickupIndex.value + 1, 
        pickupSuggestions.value.length - 1
      )
      break
    
    case 'ArrowUp':
      event.preventDefault()
      selectedPickupIndex.value = Math.max(
        selectedPickupIndex.value - 1, 
        0
      )
      break
    
    case 'Enter':
      if (selectedPickupIndex.value !== -1) {
        event.preventDefault()
        selectPickupSuggestion(
          pickupSuggestions.value[selectedPickupIndex.value]
        )
      }
      break
    
    case 'Escape':
      pickupSuggestions.value = []
      break
  }
}

// Destination Handlers (similar to pickup)
const handleDestinationInput = debounce((query) => {
  if (!query || query.length < 3) {
    destinationSuggestions.value = []
    return
  }

  const autocompleteService = new window.google.maps.places.AutocompleteService()
  autocompleteService.getPlacePredictions(
    { 
      input: query,
      types: ['geocode', 'establishment'],
      componentRestrictions: { country: 'us' }
    }, 
    (predictions, status) => {
      if (status === window.google.maps.places.PlacesServiceStatus.OK) {
        destinationSuggestions.value = predictions || []
        selectedDestinationIndex.value = -1
      }
    }
  )
}, 300)

// Similar method for destination
const selectDestinationSuggestion = (suggestion) => {
  const placesService = new window.google.maps.places.PlacesService(document.createElement('div'))
  
  placesService.getDetails(
    { 
      placeId: suggestion.place_id,
      fields: ['geometry', 'formatted_address'] 
    }, 
    (place, status) => {
      if (status === window.google.maps.places.PlacesServiceStatus.OK) {
        // Ensure we have a valid geometry
        if (place.geometry && place.geometry.location) {
          destination.value = place.formatted_address || suggestion.description
          
          // Explicitly get lat and lng
          const location = place.geometry.location
          destinationLat.value = typeof location.lat === 'function' ? location.lat() : location.lat
          destinationLng.value = typeof location.lng === 'function' ? location.lng() : location.lng
          
          // Clear suggestions
          destinationSuggestions.value = []
          
          // Log for debugging
          console.log('Destination Selected:', {
            address: destination.value,
            lat: destinationLat.value,
            lng: destinationLng.value
          })
        } else {
          searchError.value = 'Unable to retrieve destination location coordinates'
          destinationLat.value = null
          destinationLng.value = null
        }
      } else {
        searchError.value = 'Failed to retrieve place details'
        destinationLat.value = null
        destinationLng.value = null
      }
    }
  )
}

const handleDestinationKeyDown = (event) => {
  if (!destinationSuggestions.value.length) return

  switch (event.key) {
    case 'ArrowDown':
      event.preventDefault()
      selectedDestinationIndex.value = Math.min(
        selectedDestinationIndex.value + 1, 
        destinationSuggestions.value.length - 1
      )
      break
    
    case 'ArrowUp':
      event.preventDefault()
      selectedDestinationIndex.value = Math.max(
        selectedDestinationIndex.value - 1, 
        0
      )
      break
    
    case 'Enter':
      if (selectedDestinationIndex.value !== -1) {
        event.preventDefault()
        selectDestinationSuggestion(
          destinationSuggestions.value[selectedDestinationIndex.value]
        )
      }
      break
    
    case 'Escape':
      destinationSuggestions.value = []
      break
  }
}

// Load Google Maps API
const loadGoogleMapsAPI = () => {
  if (window.google && window.google.maps) {
    initializeAutocomplete()
    return
  }

  const script = document.createElement('script')
  script.src = `https://maps.googleapis.com/maps/api/js?key=${import.meta.env.VITE_GOOGLE_MAPS_API_KEY}&libraries=places`
  script.async = true
  script.defer = true
  script.onload = () => {
    initializeAutocomplete()
  }
  
  document.head.appendChild(script)
}

// Component lifecycle
onMounted(loadGoogleMapsAPI)

// Updated search rides method
const searchRides = async () => {
  // Reset previous errors
  searchError.value = null
  isSearching.value = true

  try {
    const searchResult = await rideService.searchRide(
      pickup.value, 
      destination.value, 
      pickupLat.value, 
      pickupLng.value,
      destinationLat.value,
      destinationLng.value
    )

    // Check for successful response and existing ride request
    if (searchResult.success && searchResult.rideRequestId) {
      // Navigate directly to ride tracking
      router.push({
        name: 'ride-tracking',
        params: {
          rideId: searchResult.rideRequestId
        },
        query: {
          pickup: pickup.value,
          destination: destination.value
        }
      })
    } else {
      // Handle case where no drivers are available
      if (searchResult.drivers && searchResult.drivers.length === 0) {
        searchError.value = 'No drivers available at the moment. Please try again later.'
      } else {
        searchError.value = 'Unable to process your ride request'
      }
      isSearching.value = false
    }
  } catch (error) {
    searchError.value = error.message || 'Failed to search for rides'
    isSearching.value = false
  }
}
</script>
