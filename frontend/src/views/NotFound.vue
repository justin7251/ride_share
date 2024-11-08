<template>
  <BaseLayout>
    <div class="flex flex-col items-center justify-center h-screen">
      <h1 class="text-4xl font-bold text-gray-900 mb-4">404 - Page Not Found</h1>
      <p class="text-lg text-gray-600 mb-6">
        Sorry, the page you are looking for does not exist.
      </p>
      <RouterLink to="/" class="text-blue-500 hover:underline">
        Go back to Home
      </RouterLink>
    </div>
  </BaseLayout>
</template>

<script setup>
import BaseLayout from '../components/BaseLayout.vue'
</script>

<style scoped>
/* You can add any additional styles here */
</style>
