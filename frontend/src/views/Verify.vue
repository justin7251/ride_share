<template>
  <BaseLayout>
    <div :class="styles.container">
      <div class="text-center mb-8">
        <h1 :class="styles.h1">Verify Phone</h1>
        <p :class="styles.subtitle">Enter the code sent to your phone</p>
      </div>

      <div :class="styles.card">
        <!-- Phone Display -->
        <div class="text-center mb-6">
          <p :class="styles.text">
            Code sent to {{ phoneNumber }}
          </p>
        </div>

        <!-- OTP Input -->
        <div class="flex justify-center space-x-2 mb-6">
          <input
            v-for="(digit, index) in 6"
            :key="index"
            type="text"
            maxlength="1"
            v-model="otpDigits[index]"
            @input="handleOtpInput($event, index)"
            @keydown="handleKeydown($event, index)"
            @paste="handlePaste($event, index)"
            class="w-12 h-12 text-center text-xl font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
            ref="otpInputs"
          >
        </div>

        <!-- Error Message -->
        <p v-if="errorMessage" :class="[styles.error, 'text-center mb-4']">
          {{ errorMessage }}
        </p>

        <!-- Submit Button -->
        <button
          @click="handleSubmit"
          :disabled="!isValidOtp || isLoading"
          :class="styles.buttonPrimary"
        >
          {{ isLoading ? 'Verifying...' : 'Verify' }}
        </button>

        <!-- Resend Code -->
        <div class="text-center mt-4">
          <button
            @click="resendCode"
            :disabled="resendTimer > 0 || isLoading"
            :class="[styles.link, 'disabled:opacity-50 disabled:cursor-not-allowed']"
          >
            {{ resendTimer > 0 ? `Resend code in ${resendTimer}s` : 'Resend code' }}
          </button>
        </div>
      </div>
    </div>
  </BaseLayout>
</template>

<script setup>
import { ref, computed, inject } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import BaseLayout from '../components/BaseLayout.vue'
import { authService } from '@/services/api'

const styles = inject('styles')
const router = useRouter()
const route = useRoute()
const phoneNumber = ref(route.params.phone || '')
const otpDigits = ref(Array(6).fill(''))
const otpInputs = ref([])
const errorMessage = ref('')
const isLoading = ref(false)
const resendTimer = ref(0)
let resendInterval = null

const isValidOtp = computed(() => {
  return otpDigits.value.every(digit => digit !== '')
})

const handleOtpInput = (event, index) => {
  const input = event.target
  input.value = input.value.replace(/\D/g, '') // Allow only digits

  // If the input is filled and it's not the last input, move to the next input
  if (input.value && index < 5) {
    otpInputs.value[index + 1].focus()
  }

  // If the input is empty and the user pressed backspace, move to the previous input
  if (!input.value && index > 0) {
    otpInputs.value[index - 1].focus()
  }
}

const handleKeydown = (event, index) => {
  if (event.key === 'Backspace' && !otpDigits.value[index] && index > 0) {
    otpInputs.value[index - 1].focus()
  }
}

const handlePaste = (event, index) => {
  const pastedData = event.clipboardData.getData('text').replace(/\D/g, ''); // Get only digits
  if (pastedData.length === 6) {
    otpDigits.value = pastedData.split(''); // Fill all inputs with pasted digits
    otpInputs.value.forEach((input, i) => {
      input.value = otpDigits.value[i]; // Set the value of each input
    });
  } else {
    // If the pasted data is not exactly 6 digits, handle it accordingly
    for (let i = 0; i < pastedData.length && i < 6; i++) {
      otpDigits.value[i] = pastedData[i];
      otpInputs.value[i].focus();
    }
  }
  event.preventDefault(); // Prevent the default paste behavior
}

const handleSubmit = async () => {
  if (!isValidOtp.value) return

  isLoading.value = true
  errorMessage.value = ''

  try {
    const verificationCode = otpDigits.value.join('')
    const response = await authService.verify(phoneNumber.value, verificationCode)

    if (response.status === "success") {
      // Store user data and token in local storage
      localStorage.setItem('user', JSON.stringify(response.user))
      localStorage.setItem('token', response.token)

      // Redirect to home or dashboard
      router.push('/dashboard')
    } else {
      errorMessage.value = response.message || 'Invalid verification code'
    }
  } catch (error) {
    errorMessage.value = 'Something went wrong. Please try again.'
  } finally {
    isLoading.value = false
  }
}

const resendCode = async () => {
  if (isLoading.value) return; // Prevent multiple submissions
  isLoading.value = true;
  errorMessage.value = '';

  try {
    const response = await authService.login(phoneNumber.value); // Call the same login API to resend the code

    if (response.status === "success") {
      errorMessage.value = 'Verification code resent successfully!';
      startResendTimer(); // Start the resend timer
    } else {
      errorMessage.value = response.message || 'Failed to resend code';
    }
  } catch (error) {
    errorMessage.value = 'Something went wrong. Please try again.';
  } finally {
    isLoading.value = false;
  }
}

const startResendTimer = () => {
  resendTimer.value = 30;
  resendInterval = setInterval(() => {
    if (resendTimer.value > 0) {
      resendTimer.value--;
    } else {
      clearInterval(resendInterval);
    }
  }, 1000);
}

startResendTimer();
</script> 