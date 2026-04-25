<script setup lang="ts">
import { useConfirm } from '@/composables/useConfirm';
import { ExclamationTriangleIcon, InformationCircleIcon } from '@heroicons/vue/24/outline';

const { isOpen, options, handleConfirm, handleCancel } = useConfirm();

const variants = {
  danger: 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
  warning: 'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500',
  info: 'bg-primary-600 hover:bg-primary-700 focus:ring-primary-500',
};
</script>

<template>
  <Transition
    enter-active-class="transition duration-200 ease-out"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition duration-150 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div v-if="isOpen" class="fixed inset-0 z-[10000] overflow-y-auto">
      <div class="flex min-h-full items-center justify-center p-4 text-center">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="handleCancel"></div>

        <!-- Dialog Panel -->
        <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 p-6 text-left align-middle shadow-2xl transition-all border border-slate-200 dark:border-slate-800">
          <div class="flex items-start gap-4">
            <div :class="[
              'flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full',
              options.variant === 'danger' ? 'bg-red-100 dark:bg-red-900/30 text-red-600' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-600'
            ]">
              <ExclamationTriangleIcon v-if="options.variant === 'danger'" class="h-6 w-6" aria-hidden="true" />
              <InformationCircleIcon v-else class="h-6 w-6" aria-hidden="true" />
            </div>
            
            <div class="flex-1">
              <h3 class="text-xl font-bold leading-6 text-slate-900 dark:text-white mb-2">
                {{ options.title }}
              </h3>
              <p class="text-sm text-slate-600 dark:text-slate-400">
                {{ options.message }}
              </p>
            </div>
          </div>

          <div class="mt-8 flex flex-col sm:flex-row-reverse gap-3">
            <button
              type="button"
              :class="['flex-1 inline-flex justify-center rounded-xl px-4 py-2.5 text-sm font-bold text-white shadow-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-2', variants[options.variant || 'info']]"
              @click="handleConfirm"
            >
              {{ options.confirmText }}
            </button>
            <button
              type="button"
              class="flex-1 inline-flex justify-center rounded-xl bg-slate-100 dark:bg-slate-800 px-4 py-2.5 text-sm font-semibold text-slate-900 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors focus:outline-none"
              @click="handleCancel"
            >
              {{ options.cancelText }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>
