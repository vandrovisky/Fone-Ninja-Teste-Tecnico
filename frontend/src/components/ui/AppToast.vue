<script setup lang="ts">
import { useToast } from '@/composables/useToast';
import { 
  CheckCircleIcon, 
  XCircleIcon, 
  InformationCircleIcon, 
  ExclamationTriangleIcon,
  XMarkIcon 
} from '@heroicons/vue/24/outline';

const { toasts, removeToast } = useToast();

const icons = {
  success: CheckCircleIcon,
  error: XCircleIcon,
  info: InformationCircleIcon,
  warning: ExclamationTriangleIcon,
};

const styles = {
  success: 'bg-green-50 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800',
  error: 'bg-red-50 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800',
  info: 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800',
  warning: 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800',
};
</script>

<template>
  <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 min-w-[320px] max-w-md">
    <TransitionGroup 
      enter-active-class="transform transition duration-300 ease-out"
      enter-from-class="-translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-for="toast in toasts" 
        :key="toast.id"
        :class="['relative flex items-start p-4 rounded-xl border shadow-lg backdrop-blur-sm overflow-hidden', styles[toast.type]]"
      >
        <component :is="icons[toast.type]" class="w-5 h-5 flex-shrink-0 mt-0.5 mr-3" />
        <div class="flex-1 text-sm font-medium pr-2">
          {{ toast.message }}
        </div>
        <button 
          @click="removeToast(toast.id)"
          class="flex-shrink-0 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
        >
          <XMarkIcon class="w-5 h-5" />
        </button>

        <!-- Progress Bar -->
        <div 
          class="absolute bottom-0 left-0 h-1 bg-current opacity-20 toast-progress"
          :style="{ animationDuration: (toast.duration || 4000) + 'ms' }"
        ></div>
      </div>
    </TransitionGroup>
  </div>
</template>

<style scoped>
.toast-progress {
  width: 100%;
  animation: shrink linear forwards;
}

@keyframes shrink {
  from { width: 100%; }
  to { width: 0%; }
}
</style>
