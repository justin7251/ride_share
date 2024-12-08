<script setup>
import GlobalNotification from '@/components/GlobalNotification.vue'
import { websocketService } from '@/services/websocketService'
import { onMounted } from 'vue'
import { RouterView } from 'vue-router'
import ToastContainer from '@/components/ToastContainer.vue'

onMounted(() => {
  // Initialize WebSocket when app loads
  websocketService.initializeSocket()
})
</script>

<template>
  <div id="app">
    <RouterView />
    <GlobalNotification />
    <ToastContainer />
  </div>
</template>

