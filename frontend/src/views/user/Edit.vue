<template>
    <BaseLayout>
      <div class="max-w-lg mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Edit User</h1>
        <form @submit.prevent="updateUser">
          <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input
              type="text"
              id="name"
              v-model="user.name"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-500"
              required
            />
          </div>
  
          <div class="mb-4">
            <label for="license_number" class="block text-sm font-medium text-gray-700">License Number</label>
            <input
              type="text"
              id="license_number"
              v-model="user.license_number"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-500"
              required
            />
          </div>
  
          <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select
              id="status"
              v-model="user.status"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-500"
            >
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
  
          <div class="mb-4">
            <label for="vehicle_info" class="block text-sm font-medium text-gray-700">Vehicle Info</label>
            <textarea
              id="vehicle_info"
              v-model="user.vehicle_info"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-green-500"
              placeholder='{"make": "Toyota", "model": "Camry", "year": 2020}'
            ></textarea>
          </div>
  
          <button
            type="submit"
            class="w-full bg-green-600 text-white font-semibold py-2 rounded-md hover:bg-green-700 transition duration-200"
          >
            Update User
          </button>
        </form>
      </div>
    </BaseLayout>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import { useRouter } from 'vue-router'
  import BaseLayout from '../../components/BaseLayout.vue'
  
  const router = useRouter()
  const user = ref({
    name: '',
    license_number: '',
    status: 'inactive',
    vehicle_info: ''
  })
  
  // Fetch user data (you can replace this with an API call)
  const fetchUserData = async () => {
    // Simulated API call
    const response = {
      name: 'John Doe',
      license_number: 'ABC123',
      status: 'active',
      vehicle_info: '{"make": "Toyota", "model": "Camry", "year": 2020}'
    }
    user.value = response
  }
  
  // Update user data (you can replace this with an API call)
  const updateUser = async () => {
    try {
      // Simulated API call to update user
      console.log('User updated:', user.value)
      // Redirect after successful update
      router.push('/dashboard')
    } catch (error) {
      console.error('Failed to update user:', error)
    }
  }
  
  // Lifecycle
  fetchUserData()
  </script>
  
  <style scoped>
  /* Add any additional styles here */
  </style>