<template>
  <BaseLayout>
    <div class="max-w-md mx-auto">
      <!-- Logo or Brand (Optional) -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Ride Share</h1>
        <p class="mt-2 text-gray-600">Welcome back!</p>
      </div>

      <!-- Main Card -->
      <div class="bg-white rounded-xl shadow-lg p-8 space-y-6">
        <div class="text-center mb-4">
          <h2 class="text-2xl font-semibold text-gray-800">
            Login with Phone
          </h2>
          <p class="mt-2 text-sm text-gray-600">
            We'll send you a verification code
          </p>
        </div>
        
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div class="space-y-2">
            <label for="phone" class="block text-sm font-medium text-gray-700">
              Phone Number
            </label>
            <!-- Phone Input with improved styling -->
            <div class="relative mt-1">
              <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <span class="text-gray-500 sm:text-sm font-medium">+60</span>
              </div>
              <input 
                type="tel"
                id="phone"
                v-model="phoneNumber"
                placeholder="1XXXXXXXXX"
                pattern="[0-9]*"
                @input="validatePhoneNumber"
                required
                class="block w-full pl-14 pr-4 py-3.5 text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 shadow-sm"
                :class="{'border-red-300 focus:ring-red-500 focus:border-red-500': errorMessage}"
              >
            </div>
            <!-- Improved error message display -->
            <div class="min-h-[20px]">
              <p class="text-sm text-red-600 mt-1" v-if="errorMessage">
                <span class="flex items-center">
                  <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                  </svg>
                  {{ errorMessage }}
                </span>
              </p>
            </div>
          </div>

          <!-- Improved button styling -->
          <button 
            type="submit" 
            class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm h-12"
            :disabled="!isValidPhone || isLoading"
          >
            <template v-if="isLoading">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Verifying...
            </template>
            <span v-else>Continue</span>
          </button>
        </form>
      </div>

      <!-- Add this section after the main card -->
      <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
          Don't have an account? 
          <RouterLink 
            to="/register" 
            class="font-medium text-green-600 hover:text-green-500 transition-colors duration-200"
          >
            Sign up here
          </RouterLink>
        </p>
      </div>

      <!-- Footer Links -->
      <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
          By continuing, you agree to our 
          <a href="#" class="font-medium text-green-600 hover:text-green-500 transition-colors duration-200">Terms of Service</a> and 
          <a href="#" class="font-medium text-green-600 hover:text-green-500 transition-colors duration-200">Privacy Policy</a>
        </p>
      </div>

      <!-- Help Link -->
      <div class="mt-4 text-center">
        <a href="#" class="text-sm text-gray-600 hover:text-gray-900 transition-colors duration-200">
          Need help? Contact Support
        </a>
      </div>
    </div>
  </BaseLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import BaseLayout from '../components/BaseLayout.vue'
import { authService } from '@/services/api'

const router = useRouter()
const phoneNumber = ref('')
const errorMessage = ref('')
const isValidPhone = ref(false)
const isLoading = ref(false)

const validatePhoneNumber = () => {
  phoneNumber.value = phoneNumber.value.replace(/\D/g, '')
  const phoneRegex = /^1[0-9]{8,9}$/
  isValidPhone.value = phoneRegex.test(phoneNumber.value)
  
  if (phoneNumber.value && !isValidPhone.value) {
    errorMessage.value = 'Please enter a valid Malaysian phone number'
  } else {
    errorMessage.value = ''
  }
}

const handleSubmit = async () => {
  if (!isValidPhone.value) return
  
  isLoading.value = true
  try {
    const response = await authService.login(phoneNumber.value, 'test@example.com')
    
    if (response.success) {
      localStorage.setItem('user', JSON.stringify(response.user))
      router.push({ 
        name: 'verify',
        params: { phone: phoneNumber.value }
      })
    } else {
      errorMessage.value = response.message || 'Login failed. Please try again.'
    }
  } catch (error) {
    console.error('Login error:', error)
    errorMessage.value = error.message || 'Something went wrong. Please try again.'
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
/* ... existing styles ... */
</style> 