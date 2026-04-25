<script setup lang="ts">
import { ref, watch, computed } from 'vue';

const props = defineProps<{
  modelValue: number;
  placeholder?: string;
  min?: number;
}>();

const emit = defineEmits<{
  'update:modelValue': [value: number];
}>();

const displayValue = ref(formatFromNumber(props.modelValue));

function formatFromNumber(value: number): string {
  if (!value && value !== 0) return '';
  return new Intl.NumberFormat('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(value);
}

function parseToNumber(formatted: string): number {
  if (!formatted) return 0;
  const digits = formatted.replace(/\D/g, '');
  if (!digits) return 0;
  return parseInt(digits, 10) / 100;
}

const handleInput = (event: Event) => {
  const input = event.target as HTMLInputElement;
  const rawValue = input.value;
  const digits = rawValue.replace(/\D/g, '');

  if (!digits) {
    displayValue.value = '';
    emit('update:modelValue', 0);
    return;
  }

  const numericValue = parseInt(digits, 10) / 100;
  displayValue.value = formatFromNumber(numericValue);
  emit('update:modelValue', numericValue);

  requestAnimationFrame(() => {
    input.value = displayValue.value;
    input.setSelectionRange(input.value.length, input.value.length);
  });
};

watch(() => props.modelValue, (newVal) => {
  const currentParsed = parseToNumber(displayValue.value);
  if (Math.abs(currentParsed - newVal) > 0.001) {
    displayValue.value = formatFromNumber(newVal);
  }
});

const prefixClass = computed(() => {
  return displayValue.value ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500';
});
</script>

<template>
  <div class="relative">
    <span :class="['absolute left-3 top-1/2 -translate-y-1/2 text-sm font-medium pointer-events-none select-none', prefixClass]">R$</span>
    <input
      type="text"
      inputmode="numeric"
      :value="displayValue"
      @input="handleInput"
      :placeholder="placeholder || '0,00'"
      class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg pl-10 pr-3 py-2 text-sm text-slate-900 dark:text-white text-right focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-colors"
    >
  </div>
</template>
