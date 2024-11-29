<script setup>
import GlobalNotification from '@/components/GlobalNotification.vue'
import { websocketService } from '@/services/websocketService'
import { onMounted } from 'vue'
import { RouterView } from 'vue-router'

onMounted(() => {
  // Initialize WebSocket when app loads
  websocketService.initializeDriverSocket()
})
</script>

<template>
  <RouterView />
  <GlobalNotification />
</template>

