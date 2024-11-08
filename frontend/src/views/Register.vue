<template>
  <BaseLayout>
    <div :class="styles.container">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 :class="styles.h1">Create Account</h1>
        <p :class="styles.subtitle">Join Ride Share today</p>
      </div>

      <!-- Main Card -->
      <div :class="styles.card">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Phone Number -->
          <div :class="styles.formGroup">
            <label :class="styles.label" for="phone">Phone Number</label>
            <div class="relative mt-1">
              <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <span class="text-gray-500 sm:text-sm font-medium">+</span>
              </div>
              <input
                id="phone"
                v-model="phone"
                type="tel"
                required
                placeholder="XXXXXXXXXX"
                pattern="[0-9]*"
                @input="validatePhone"
                :class="[
                  styles.phoneInput,
                  phoneError ? 'border-red-300 focus:ring-red-500 focus:border-red-500' : ''
                ]"
              >
            </div>
            <p v-if="phoneError" :class="styles.error">{{ phoneError }}</p>
          </div>

          <!-- Email -->
          <div :class="styles.formGroup">
            <label :class="styles.label" for="email">Email</label>
            <input
              id="email"
              v-model="email"
              type="email"
              placeholder="your@email.com"
              :class="styles.input"
            >
          </div>

          <!-- Name -->
          <div :class="styles.formGroup">
            <label :class="styles.label" for="name">Full Name</label>
            <input
              id="name"
              v-model="name"
              type="text"
              required
              placeholder="Enter your full name"
              :class="styles.input"
            >
          </div>

          <!-- Error Message -->
          <div class="min-h-[20px]">
            <p v-if="errorMessage" :class="styles.error">{{ errorMessage }}</p>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="!isValid || isLoading"
            :class="styles.buttonPrimary"
          >
            <template v-if="isLoading">
              <svg :class="styles.loadingSpinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Creating Account...
            </template>
            <span v-else>Create Account</span>
          </button>
        </form>

        <!-- Terms and Privacy -->
        <div class="mt-6 text-center">
          <p :class="styles.text">
            By creating an account, you agree to our
            <a href="#" :class="styles.link">Terms of Service</a> and
            <a href="#" :class="styles.link">Privacy Policy</a>
          </p>
        </div>
      </div>

      <!-- Login Link -->
      <div class="mt-6 text-center">
        <p :class="styles.text">
          Already have an account?
          <RouterLink to="/login" :class="styles.link">
            Sign in here
          </RouterLink>
        </p>
      </div>
    </div>
  </BaseLayout>
</template>

<script setup>
import { ref, computed, inject } from 'vue'
import { useRouter } from 'vue-router'
import BaseLayout from '../components/BaseLayout.vue'
import { authService } from '@/services/api'

const router = useRouter()
const phone = ref('')
const email = ref('')
const name = ref('')
const phoneError = ref('')
const errorMessage = ref('')
const isLoading = ref(false)

const styles = inject('styles')

const validatePhone = () => {
  phone.value = phone.value.replace(/\D/g, '')
  const phoneRegex = /^[0-9]{7,15}$/
  
  if (phone.value && !phoneRegex.test(phone.value)) {
    phoneError.value = 'Please enter a valid Malaysian phone number'
    return false
  }
  
  phoneError.value = ''
  return true
}

const isValid = computed(() => {
  return phone.value && name.value && !phoneError.value
})

const handleSubmit = async () => {
  if (!isValid.value) return
  
  isLoading.value = true
  errorMessage.value = ''
  
  try {
    const response = await authService.register({
      phone: phone.value,
      email: email.value,
      name: name.value
    })
    
    if (response.success) {
      router.push({
        name: 'verify',
        params: { phone: phone.value }
      })
    } else {
      errorMessage.value = response.message || 'Registration failed'
    }
  } catch (error) {
    console.error('Registration error:', error)
    errorMessage.value = error.message || 'Something went wrong'
  } finally {
    isLoading.value = false
  }
}
</script>
