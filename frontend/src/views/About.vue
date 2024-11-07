<template>
  <BaseLayout>
    <!-- Hero Section -->
    <section :class="[styles.section, 'bg-gradient-to-b from-white to-gray-50']">
      <div :class="styles.container">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-12">
          <div class="flex-1 text-center lg:text-left">
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-6">
              About <span class="text-green-600">Ride Share</span>
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-2xl">
              Making transportation accessible and affordable for everyone. Join our growing community of riders and drivers.
            </p>
          </div>
          
          <!-- Right side illustration -->
          <div class="flex-1 w-full max-w-xl">
            <div class="aspect-video bg-green-50 rounded-xl shadow-lg overflow-hidden">
              <div class="w-full h-full flex items-center justify-center p-8">
                <svg class="w-full h-full text-green-600 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Mission Section -->
    <section :class="styles.section">
      <div :class="styles.container">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div :class="[styles.card, 'hover:shadow-xl transition-shadow duration-300']">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <h3 :class="styles.h3">Our Mission</h3>
            <p :class="[styles.text, 'mt-2']">
              To provide safe, reliable, and affordable transportation for everyone.
            </p>
          </div>

          <div :class="[styles.card, 'hover:shadow-xl transition-shadow duration-300']">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
            <h3 :class="styles.h3">Our Vision</h3>
            <p :class="[styles.text, 'mt-2']">
              To revolutionize urban mobility and create opportunities for drivers.
            </p>
          </div>

          <div :class="[styles.card, 'hover:shadow-xl transition-shadow duration-300']">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <h3 :class="styles.h3">Our Community</h3>
            <p :class="[styles.text, 'mt-2']">
              Building a trusted network of drivers and riders worldwide.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section :class="[styles.section, 'bg-green-50']">
      <div :class="styles.container">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
          <div>
            <div class="text-4xl font-bold text-green-600">100K+</div>
            <div class="text-gray-600 mt-2">Active Users</div>
          </div>
          <div>
            <div class="text-4xl font-bold text-green-600">50+</div>
            <div class="text-gray-600 mt-2">Cities</div>
          </div>
          <div>
            <div class="text-4xl font-bold text-green-600">1M+</div>
            <div class="text-gray-600 mt-2">Rides Completed</div>
          </div>
          <div>
            <div class="text-4xl font-bold text-green-600">4.8</div>
            <div class="text-gray-600 mt-2">Average Rating</div>
          </div>
        </div>
      </div>
    </section>
  </BaseLayout>
</template>

<script setup>
import { inject } from 'vue'
import BaseLayout from '../components/BaseLayout.vue'

const styles = inject('styles')
</script> 