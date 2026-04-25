<script setup lang="ts">
import { computed } from 'vue';
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';

const props = defineProps<{
  currentPage: number;
  totalItems: number;
  perPage: number;
}>();

const emit = defineEmits(['page-change']);

const totalPages = computed(() => Math.ceil(props.totalItems / props.perPage));

const showingFrom = computed(() => (props.currentPage - 1) * props.perPage + 1);
const showingTo = computed(() => Math.min(props.currentPage * props.perPage, props.totalItems));

const pages = computed(() => {
  const range = [];
  const delta = 2;
  
  for (let i = Math.max(2, props.currentPage - delta); i <= Math.min(totalPages.value - 1, props.currentPage + delta); i++) {
    range.push(i);
  }

  if (props.currentPage - delta > 2) range.unshift('...');
  range.unshift(1);
  if (props.currentPage + delta < totalPages.value - 1) range.push('...');
  if (totalPages.value > 1) range.push(totalPages.value);

  return range;
});
</script>

<template>
  <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-3">
    <p class="text-sm text-slate-500 dark:text-slate-400">
      Mostrando <span class="font-medium text-slate-900 dark:text-white">{{ showingFrom }}</span> a 
      <span class="font-medium text-slate-900 dark:text-white">{{ showingTo }}</span> de 
      <span class="font-medium text-slate-900 dark:text-white">{{ totalItems }}</span> resultados
    </p>

    <nav class="flex items-center gap-1 shadow-sm rounded-xl border border-slate-200 dark:border-slate-800 p-1 bg-white dark:bg-slate-900">
      <button 
        @click="emit('page-change', currentPage - 1)"
        :disabled="currentPage === 1"
        class="p-2 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-slate-50 dark:hover:bg-slate-800 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
      >
        <ChevronLeftIcon class="w-5 h-5" />
      </button>

      <div class="flex items-center gap-1">
        <template v-for="page in pages" :key="page">
          <span v-if="page === '...'" class="px-3 text-slate-400">...</span>
          <button
            v-else
            @click="emit('page-change', page)"
            :class="[
              'w-9 h-9 flex items-center justify-center rounded-lg text-sm font-bold transition-all',
              currentPage === page 
                ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/20' 
                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800'
            ]"
          >
            {{ page }}
          </button>
        </template>
      </div>

      <button 
        @click="emit('page-change', currentPage + 1)"
        :disabled="currentPage === totalPages"
        class="p-2 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-slate-50 dark:hover:bg-slate-800 disabled:opacity-30 disabled:cursor-not-allowed transition-all"
      >
        <ChevronRightIcon class="w-5 h-5" />
      </button>
    </nav>
  </div>
</template>
