<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Logo -->
          <div class="flex-shrink-0 flex items-center">
            <RouterLink to="/" class="text-2xl font-bold text-green-600">
              Ride Share
            </RouterLink>
          </div>

          <!-- Desktop Navigation -->
          <div class="hidden sm:flex sm:space-x-8 sm:items-center">
            <RouterLink 
              v-for="item in navigationItems"
              :key="item.path"
              :to="item.path"
              class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
              :class="{ 'text-green-600': route.path === item.path }"
            >
              {{ item.name }}
            </RouterLink>
          </div>

          <!-- Mobile menu button -->
          <div class="flex items-center sm:hidden">
            <button 
              @click="isMobileMenuOpen = !isMobileMenuOpen"
              class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500"
            >
              <span class="sr-only">Open main menu</span>
              <!-- Icon when menu is closed -->
              <svg 
                v-if="!isMobileMenuOpen"
                class="block h-6 w-6" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <!-- Icon when menu is open -->
              <svg 
                v-else
                class="block h-6 w-6" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile menu -->
      <div 
        v-show="isMobileMenuOpen" 
        class="sm:hidden"
      >
        <div class="pt-2 pb-3 space-y-1">
          <RouterLink
            v-for="item in navigationItems"
            :key="item.path"
            :to="item.path"
            class="block px-3 py-2 text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50"
            :class="{ 'text-green-600': route.path === item.path }"
            @click="isMobileMenuOpen = false"
          >
            {{ item.name }}
          </RouterLink>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <slot></slot>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="text-center text-sm text-gray-500">
          <p>© {{ new Date().getFullYear() }} Ride Share. All rights reserved.</p>
          <div class="mt-2 space-x-4">
            <a href="#" class="hover:text-gray-900">Terms</a>
            <a href="#" class="hover:text-gray-900">Privacy</a>
            <a href="#" class="hover:text-gray-900">Support</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink, useRoute } from 'vue-router'

const route = useRoute()
const isMobileMenuOpen = ref(false)

const navigationItems = [
  { name: 'Home', path: '/' },
  { name: 'Login', path: '/login' },
  { name: 'Register', path: '/register' }
]
</script> 