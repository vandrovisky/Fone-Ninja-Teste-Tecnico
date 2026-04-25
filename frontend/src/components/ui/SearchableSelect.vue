<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, watch, nextTick } from 'vue';
import { ChevronUpDownIcon, MagnifyingGlassIcon, CheckIcon } from '@heroicons/vue/24/outline';

export interface SelectOption {
  value: number | string;
  label: string;
  sublabel?: string;
}

const props = defineProps<{
  modelValue: number | string;
  options: SelectOption[];
  placeholder?: string;
}>();

const emit = defineEmits<{
  'update:modelValue': [value: number | string];
}>();

const isOpen = ref(false);
const search = ref('');
const searchInputRef = ref<HTMLInputElement | null>(null);
const containerRef = ref<HTMLDivElement | null>(null);
const highlightedIndex = ref(-1);

const selectedOption = computed(() => {
  return props.options.find(o => o.value === props.modelValue);
});

const filteredOptions = computed(() => {
  if (!search.value.trim()) return props.options;
  const term = search.value.toLowerCase();
  return props.options.filter(o =>
    o.label.toLowerCase().includes(term) ||
    (o.sublabel && o.sublabel.toLowerCase().includes(term))
  );
});

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    search.value = '';
    highlightedIndex.value = -1;
    nextTick(() => searchInputRef.value?.focus());
  }
};

const selectOption = (option: SelectOption) => {
  emit('update:modelValue', option.value);
  isOpen.value = false;
  search.value = '';
};

const handleKeydown = (e: KeyboardEvent) => {
  if (!isOpen.value) return;

  switch (e.key) {
    case 'ArrowDown':
      e.preventDefault();
      highlightedIndex.value = Math.min(highlightedIndex.value + 1, filteredOptions.value.length - 1);
      break;
    case 'ArrowUp':
      e.preventDefault();
      highlightedIndex.value = Math.max(highlightedIndex.value - 1, 0);
      break;
    case 'Enter':
      e.preventDefault();
      if (highlightedIndex.value >= 0 && filteredOptions.value[highlightedIndex.value]) {
        selectOption(filteredOptions.value[highlightedIndex.value]);
      }
      break;
    case 'Escape':
      isOpen.value = false;
      break;
  }
};

const handleClickOutside = (e: MouseEvent) => {
  if (containerRef.value && !containerRef.value.contains(e.target as Node)) {
    isOpen.value = false;
  }
};

watch(search, () => {
  highlightedIndex.value = 0;
});

onMounted(() => document.addEventListener('click', handleClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside));
</script>

<template>
  <div ref="containerRef" class="relative">
    <button
      type="button"
      @click="toggleDropdown"
      class="w-full flex items-center justify-between gap-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm text-left transition-colors hover:border-slate-300 dark:hover:border-slate-600 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none"
    >
      <span v-if="selectedOption" class="truncate text-slate-900 dark:text-white">
        {{ selectedOption.label }}
        <span v-if="selectedOption.sublabel" class="text-slate-400 dark:text-slate-500 ml-1">{{ selectedOption.sublabel }}</span>
      </span>
      <span v-else class="text-slate-400 dark:text-slate-500 truncate">{{ placeholder || 'Selecionar...' }}</span>
      <ChevronUpDownIcon class="w-4 h-4 flex-shrink-0 text-slate-400" />
    </button>

    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="opacity-0 -translate-y-1 scale-[0.98]"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 -translate-y-1 scale-[0.98]"
    >
      <div
        v-if="isOpen"
        class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg overflow-hidden"
      >
        <div class="p-2 border-b border-slate-100 dark:border-slate-800">
          <div class="relative">
            <MagnifyingGlassIcon class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
            <input
              ref="searchInputRef"
              v-model="search"
              type="text"
              placeholder="Buscar produto..."
              @keydown="handleKeydown"
              class="w-full pl-8 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none"
            >
          </div>
        </div>
        <ul class="max-h-52 overflow-y-auto py-1">
          <li v-if="filteredOptions.length === 0" class="px-3 py-3 text-sm text-slate-400 dark:text-slate-500 text-center">
            Nenhum produto encontrado.
          </li>
          <li
            v-for="(option, index) in filteredOptions"
            :key="option.value"
            @click="selectOption(option)"
            :class="[
              'flex items-center justify-between px-3 py-2.5 text-sm cursor-pointer transition-colors',
              highlightedIndex === index ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300' : 'text-slate-900 dark:text-white hover:bg-slate-50 dark:hover:bg-slate-800',
            ]"
          >
            <div class="truncate">
              <span class="font-medium">{{ option.label }}</span>
              <span v-if="option.sublabel" class="ml-1.5 text-xs text-slate-400 dark:text-slate-500">{{ option.sublabel }}</span>
            </div>
            <CheckIcon v-if="option.value === modelValue" class="w-4 h-4 flex-shrink-0 text-primary-600 dark:text-primary-400" />
          </li>
        </ul>
      </div>
    </Transition>
  </div>
</template>
