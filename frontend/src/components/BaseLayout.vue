<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Logo -->
          <div class="flex-shrink-0 flex items-center">
            <RouterLink to="/" class="flex items-center space-x-2">
              <!-- Logo SVG -->
              <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              <span class="text-xl font-bold text-gray-900">Ride Share</span>
            </RouterLink>
          </div>

          <!-- Desktop Navigation -->
          <div class="hidden md:flex md:items-center md:space-x-8">
            <RouterLink 
              v-for="item in navigationItems"
              :key="item.path"
              :to="item.path"
              class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200"
              :class="[
                route.path === item.path
                  ? 'text-green-600 bg-green-50'
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
              ]"
              @click="item.function ? item.function() : null"
            >
              {{ item.name }}
            </RouterLink>
          </div>

          <!-- Mobile menu button -->
          <div class="flex items-center md:hidden">
            <button 
              @click="isMobileMenuOpen = !isMobileMenuOpen"
              class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500"
              aria-expanded="false"
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

      <!-- Mobile menu, show/hide based on menu state -->
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="transform -translate-y-2 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition duration-100 ease-in"
        leave-from-class="transform translate-y-0 opacity-100"
        leave-to-class="transform -translate-y-2 opacity-0"
      >
        <div v-show="isMobileMenuOpen" class="md:hidden bg-white border-b">
          <div class="px-2 pt-2 pb-3 space-y-1">
            <RouterLink
              v-for="item in navigationItems"
              :key="item.path"
              :to="item.path"
              class="block px-3 py-2 rounded-md text-base font-medium transition-colors duration-200"
              :class="[
                route.path === item.path
                  ? 'text-green-600 bg-green-50'
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
              ]"
              @click="isMobileMenuOpen = false; item.function ? item.function() : null"
            >
              {{ item.name }}
            </RouterLink>
          </div>
        </div>
      </transition>
    </nav>

    <!-- Main Content with responsive padding -->
    <main class="flex-grow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-12">
        <slot></slot>
      </div>
    </main>

    <!-- Footer with responsive design -->
    <footer class="bg-white border-t mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Company Info -->
          <div class="text-center md:text-left">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ride Share</h3>
            <p class="text-gray-600 text-sm">
              Making transportation accessible and affordable for everyone.
            </p>
          </div>

          <!-- Quick Links -->
          <div class="text-center md:text-left">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Links</h3>
            <ul class="space-y-2">
              <li>
                <a href="#" class="text-gray-600 hover:text-gray-900 text-sm">About Us</a>
              </li>
              <li>
                <a href="#" class="text-gray-600 hover:text-gray-900 text-sm">Contact</a>
              </li>
              <li>
                <a href="#" class="text-gray-600 hover:text-gray-900 text-sm">Careers</a>
              </li>
              <li>
                <a href="#" class="text-gray-600 hover:text-gray-900 text-sm">Support</a>
              </li>
            </ul>
          </div>

          <!-- Legal -->
          <div class="text-center md:text-left">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Legal</h3>
            <ul class="space-y-2">
              <li>
                <a href="#" class="text-gray-600 hover:text-gray-900 text-sm">Terms of Service</a>
              </li>
              <li>
                <a href="#" class="text-gray-600 hover:text-gray-900 text-sm">Privacy Policy</a>
              </li>
              <li>
                <a href="#" class="text-gray-600 hover:text-gray-900 text-sm">Cookie Policy</a>
              </li>
            </ul>
          </div>
        </div>

        <!-- Bottom Footer -->
        <div class="mt-8 pt-8 border-t text-center">
          <p class="text-gray-600 text-sm">
            © {{ new Date().getFullYear() }} Ride Share. All rights reserved.
          </p>
          <div class="mt-4 flex justify-center space-x-6">
            <!-- Social Media Icons -->
            <a href="#" class="text-gray-400 hover:text-gray-500">
              <span class="sr-only">Facebook</span>
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
              </svg>
            </a>
            <a href="#" class="text-gray-400 hover:text-gray-500">
              <span class="sr-only">Twitter</span>
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import auth from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const isMobileMenuOpen = ref(false)

const logout = () => {
  auth.clear()
  router.push('/') // Redirect to the home page after logout
}

let navigationItems = []

if (auth.isAuthenticated) {
  navigationItems = [
    { name: 'Dashboard', path: '/dashboard' },
    { name: 'Logout', path: '/', function: logout }
  ]
} else {
  navigationItems = [
    { name: 'Home', path: '/' },
    { name: 'Login', path: '/login' },
    { name: 'Register', path: '/register' }
  ]
}
</script> 