<template>
  <BaseLayout>
    <!-- Hero Section -->
    <section :class="[styles.section, 'bg-gradient-to-b from-white to-gray-50']">
      <div :class="styles.container">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-12">
          <!-- Left side content -->
          <div class="flex-1 text-center lg:text-left">
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-6">
              Your Journey, <span class="text-green-600">Our Priority</span>
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-2xl">
              Join thousands of satisfied users who trust Ride Share for their daily commute. Safe, reliable, and affordable rides at your fingertips.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
              <RouterLink 
                to="/register" 
                :class="styles.buttonPrimary"
              >
                Get Started
              </RouterLink>
              <RouterLink 
                to="/login" 
                :class="styles.buttonSecondary"
              >
                Sign In
              </RouterLink>
            </div>
          </div>

          <!-- Right side illustration -->
          <div class="flex-1 w-full max-w-xl">
            <div class="aspect-video bg-green-50 rounded-xl shadow-lg overflow-hidden">
              <div class="w-full h-full flex items-center justify-center p-8">
                <svg class="w-full h-full text-green-600 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section :class="styles.section">
      <div :class="styles.container">
        <h2 :class="[styles.h2, 'text-center mb-12']">
          Why Choose Ride Share?
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Feature 1: Quick Pickup -->
          <div :class="[styles.card, 'hover:shadow-xl transition-shadow duration-300']">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 :class="styles.h3">Quick Pickup</h3>
            <p :class="[styles.text, 'mt-2']">
              Get a ride within minutes of booking, our drivers are always nearby.
            </p>
          </div>

          <!-- Feature 2: Safe Rides -->
          <div :class="[styles.card, 'hover:shadow-xl transition-shadow duration-300']">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
            </div>
            <h3 :class="styles.h3">Safe Rides</h3>
            <p :class="[styles.text, 'mt-2']">
              All our drivers are verified and monitored for your safety.
            </p>
          </div>

          <!-- Feature 3: Best Rates -->
          <div :class="[styles.card, 'hover:shadow-xl transition-shadow duration-300']">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 :class="styles.h3">Best Rates</h3>
            <p :class="[styles.text, 'mt-2']">
              Competitive pricing with no hidden charges.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section :class="[styles.section, 'bg-green-50']">
      <div :class="styles.container">
        <div class="text-center max-w-3xl mx-auto">
          <h2 :class="[styles.h2, 'mb-6']">
            Ready to Get Started?
          </h2>
          <p :class="[styles.text, 'mb-8 text-lg']">
            Join our community of riders and drivers today. Experience the future of transportation.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <RouterLink 
              to="/register" 
              :class="styles.buttonPrimary"
            >
              Create Account
            </RouterLink>
            <RouterLink 
              to="/login" 
              :class="styles.buttonSecondary"
            >
              Sign In
            </RouterLink>
          </div>
        </div>
      </div>
    </section>
  </BaseLayout>
</template>

<script setup>
import { ref, inject } from 'vue'
import { useRouter } from 'vue-router'
import BaseLayout from '../components/BaseLayout.vue'
import auth from '@/stores/auth'

const router = useRouter()
const styles = inject('styles') // Ensure styles are injected

if (auth.isAuthenticated) {
  router.push('/dashboard')
}
</script>

<style scoped>
.feature-card {
  @apply bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow;
}
</style>
