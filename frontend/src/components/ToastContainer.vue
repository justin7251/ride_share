<template>
  <div 
    class="fixed z-50 w-full max-w-xs right-4 top-4 space-y-2"
  >
    <transition-group 
      name="toast" 
      tag="div"
    >
      <div 
        v-for="toast in toast.notifications.value" 
        :key="toast.id"
        :class="[
          'p-4 rounded-lg shadow-lg transform transition-all duration-300 ease-in-out',
          {
            'bg-green-500 text-white': toast.type === 'success',
            'bg-red-500 text-white': toast.type === 'error',
            'bg-blue-500 text-white': toast.type === 'info',
            'bg-yellow-500 text-white': toast.type === 'warning'
          }
        ]"
      >
        <div class="flex justify-between items-center">
          <span>{{ toast.message }}</span>
          <button 
            @click="toast.remove(toast.id)"
            class="ml-4 focus:outline-none"
          >
            <svg 
              class="w-4 h-4" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path 
                fill-rule="evenodd" 
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" 
                clip-rule="evenodd" 
              />
            </svg>
          </button>
        </div>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { toast } from '@/utils/toast'
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.5s ease;
}

.toast-enter-from,
.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.toast-enter-to,
.toast-leave-from {
  opacity: 1;
  transform: translateX(0);
}
</style>
