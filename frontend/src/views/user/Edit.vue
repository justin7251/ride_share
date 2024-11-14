<template>
  <BaseLayout>
    <div class="max-w-lg mx-auto px-4 py-6">
      <LoadingSpinner v-if="isLoading" />
      
      <!-- Form Content (only show when not loading) -->
      <div v-else>
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Edit User</h1>
        <form @submit.prevent="updateUser">
          <!-- User Details Section -->
          <h2 class="text-lg font-semibold text-gray-900 mb-2">User Details</h2>
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input
              type="text"
              id="name"
              v-model="user.name"
              class="mt-1 block w-full pl-3 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
              required
            />
          </div>

          <div class="mb-4">
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
            <input
              type="text"
              id="phone"
              v-model="user.phone"
              disabled
              class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm text-gray-500 cursor-not-allowed"
            >
          </div>

          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input
              type="email"
              id="email"
              v-model="user.email"
              class="mt-1 block w-full pl-3 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
              required
            />
          </div>

          <!-- Driver Details Section -->
          <h2 class="text-lg font-semibold text-gray-900 mb-2">Driver Details</h2>
          <div class="mb-4">
            <label for="license_number" class="block text-sm font-medium text-gray-700">License Number</label>
            <input
              type="text"
              id="license_number"
              v-model="driver.license_number"
              class="mt-1 block w-full pl-3 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
              required
            />
          </div>

          <div class="mb-4">
            <label for="vehicle_make" class="block text-sm font-medium text-gray-700">Vehicle Make</label>
            <input
              type="text"
              id="vehicle_make"
              v-model="driver.vehicle.make"
              class="mt-1 block w-full pl-3 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
              required
            />
          </div>

          <div class="mb-4">
            <label for="vehicle_model" class="block text-sm font-medium text-gray-700">Vehicle Model</label>
            <input
              type="text"
              id="vehicle_model"
              v-model="driver.vehicle.model"
              class="mt-1 block w-full pl-3 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
              required
            />
          </div>

          <div class="mb-4">
            <label for="vehicle_year" class="block text-sm font-medium text-gray-700">Vehicle Year</label>
            <input
              type="number"
              id="vehicle_year"
              v-model="driver.vehicle.year"
              class="mt-1 block w-full pl-3 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
              required
            />
          </div>

          <button
            type="submit"
            class="w-full bg-green-600 text-white font-semibold py-2 rounded-md hover:bg-green-700 transition duration-200"
          >
            Update User
          </button>
        </form>
      </div>
    </div>
  </BaseLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import BaseLayout from '../../components/BaseLayout.vue'
import { userService } from '../../services/userService'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

const router = useRouter()
const isLoading = ref(true)

const user = ref({
  name: '',
  phone: '',
  email: '',
})

const driver = ref({
  license_number: '',
  vehicle: {
    make: '',
    model: '',
    year: ''
  }
})

// Fetch user data (you can replace this with an API call)
const fetchUserData = async () => {
  try {
    isLoading.value = true
    const data = await userService.getUserWithDriver()
    user.value = {
      name: data.name,
      phone: data.phone,
      email: data.email,
    }
    
    driver.value = {
      license_number: data.license_number,
      vehicle: data.vehicle || { make: '', model: '', year: '' }
    }
  } catch (error) {
    console.error('Failed to fetch user data:', error)
  } finally {
    isLoading.value = false
  }
}

// Update user data (you can replace this with an API call)
const updateUser = async () => {
  try {
    const data = await userService.updateUserWithDriver({
      name: user.value.name,
      email: user.value.email,
      license_number: driver.value.license_number,
      vehicle: driver.value.vehicle
    })
    
    if (data.status === 'success') {
      router.push('/dashboard')
    }
  } catch (error) {
    console.error('Failed to update user:', error)
  }
}

// Lifecycle
onMounted(fetchUserData)
</script>

<style scoped>
/* Add any additional styles here */
</style>