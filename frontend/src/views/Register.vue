<template>
  <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-4">
    <div class="max-w-md w-full">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Register</h1>
        <p class="mt-2 text-gray-600">Create your account</p>
      </div>

      <div class="bg-white rounded-xl shadow-lg p-8">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
            <div class="mt-1 relative">
              <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">+60</span>
              <input
                id="phone"
                v-model="phone"
                type="tel"
                required
                class="block w-full pl-12 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                placeholder="1XXXXXXXXX"
              >
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email (Optional)</label>
            <input
              id="email"
              v-model="email"
              type="email"
              class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
              placeholder="Enter your email"
            >
          </div>

          <p v-if="errorMessage" class="text-sm text-red-600 text-center">{{ errorMessage }}</p>

          <button
            type="submit"
            :disabled="isLoading"
            class="w-full py-3 px-4 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isLoading ? 'Creating Account...' : 'Create Account' }}
          </button>
        </form>

        <div class="mt-6 text-center">
          <p class="text-sm text-gray-600">
            Already have an account?
            <router-link to="/login" class="text-green-600 hover:text-green-500 font-medium">Sign in</router-link>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { authService } from '@/services/api'

const router = useRouter()
const phone = ref('')
const email = ref('')
const errorMessage = ref('')
const isLoading = ref(false)

const handleSubmit = async () => {
  if (!phone.value) return
  
  isLoading.value = true
  errorMessage.value = ''
  
  try {
    const response = await authService.login(phone.value, email.value)
    
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