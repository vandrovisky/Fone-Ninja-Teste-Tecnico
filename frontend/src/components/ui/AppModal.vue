<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue';
import { XMarkIcon } from '@heroicons/vue/24/outline';

const props = defineProps<{
  show: boolean;
  title: string;
}>();

const emit = defineEmits(['close']);

const close = () => emit('close');

const handleEsc = (e: KeyboardEvent) => {
  if (e.key === 'Escape' && props.show) close();
};

onMounted(() => window.addEventListener('keydown', handleEsc));
onUnmounted(() => window.removeEventListener('keydown', handleEsc));
</script>

<template>
  <Transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex min-h-full items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="close"></div>

        <!-- Modal Panel -->
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 shadow-2xl transition-all border border-slate-200 dark:border-slate-800">
          <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-6 py-4">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ title }}</h3>
            <button @click="close" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
          <div class="p-6">
            <slot />
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>
