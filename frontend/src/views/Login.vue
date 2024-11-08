<template>
  <BaseLayout>
    <div class="max-w-md mx-auto">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 :class="styles.h1">Ride Share</h1>
        <p :class="styles.subtitle">Welcome back!</p>
      </div>

      <!-- Main Card -->
      <div :class="styles.card">
        <div class="text-center mb-6">
          <h2 :class="styles.h2">Login with Phone</h2>
          <p :class="[styles.subtitle, 'mt-2']">
            We'll send you a verification code
          </p>
        </div>
        
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div :class="styles.formGroup">
            <label :class="styles.label" for="phone">
              Phone Number
            </label>
            <div class="relative mt-1">
              <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <span class="text-gray-500 sm:text-sm font-medium">+</span>
              </div>
              <input 
                type="tel"
                id="phone"
                v-model="phoneNumber"
                placeholder="Enter your phone number"
                pattern="[0-9]*"
                @input="validatePhoneNumber"
                required
                :class="[
                  styles.phoneInput,
                  errorMessage ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : ''
                ]"
              >
            </div>
            <!-- Error message -->
            <div class="min-h-[20px]">
              <p v-if="errorMessage" :class="styles.error">
                <span class="flex items-center">
                  <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                  </svg>
                  {{ errorMessage }}
                </span>
              </p>
            </div>
          </div>

          <!-- Submit Button -->
          <button 
            type="submit" 
            :class="styles.buttonPrimary"
            :disabled="!isValidPhone || isLoading"
          >
            <template v-if="isLoading">
              <svg :class="styles.loadingSpinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Verifying...
            </template>
            <span v-else>Continue</span>
          </button>
        </form>

        <!-- Links -->
        <div class="mt-6 text-center">
          <p :class="styles.text">
            Don't have an account? 
            <RouterLink 
              to="/register" 
              :class="styles.link"
            >
              Sign up here
            </RouterLink>
          </p>
        </div>
      </div>
    </div>
  </BaseLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import BaseLayout from '../components/BaseLayout.vue'
import { authService } from '@/services/api'
import { styles } from '@/assets/styles/shared'

const router = useRouter()
const phoneNumber = ref('')
const errorMessage = ref('')
const isValidPhone = ref(false)
const isLoading = ref(false)

const validatePhoneNumber = () => {
  phoneNumber.value = phoneNumber.value.replace(/\D/g, '')
  const phoneRegex = /^[0-9]{7,15}$/
  isValidPhone.value = phoneRegex.test(phoneNumber.value)
  
  if (phoneNumber.value && !isValidPhone.value) {
    errorMessage.value = 'Please enter a valid phone number'
  } else {
    errorMessage.value = ''
  }
}

const handleSubmit = async () => {
  if (!isValidPhone.value || isLoading.value) return // Prevent double submission
  
  isLoading.value = true
  errorMessage.value = ''; // Clear previous error messages
  try {
    const response = await authService.login(phoneNumber.value)
    // Check for success status
    if (response.status === "success") {
      localStorage.setItem('user', JSON.stringify(response.user)); // Ensure response.user is valid
      // Clear error message on success
      errorMessage.value = ''; 
      // Redirect to verify page after sending the verification code
      router.push({ 
        name: 'verify',
        params: { phone: phoneNumber.value }
      })
    } else {
      errorMessage.value = response.message || 'Login failed. Please try again.'
    }
  } catch (error) {
    errorMessage.value = error.message || 'Something went wrong. Please try again.'
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
/* ... existing styles ... */
</style> 