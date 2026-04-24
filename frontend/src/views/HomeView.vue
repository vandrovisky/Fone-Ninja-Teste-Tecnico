<script setup lang="ts">
import { 
  CubeIcon, 
  ArrowTrendingUpIcon, 
  ArrowTrendingDownIcon,
  BanknotesIcon 
} from '@heroicons/vue/24/outline';

const stats = [
  { name: 'Total de Produtos', value: '0', icon: CubeIcon, color: 'text-blue-600', bg: 'bg-blue-100 dark:bg-blue-900/20' },
  { name: 'Total de Vendas', value: 'R$ 0,00', icon: ArrowTrendingUpIcon, color: 'text-green-600', bg: 'bg-green-100 dark:bg-green-900/20' },
  { name: 'Total de Compras', value: 'R$ 0,00', icon: ArrowTrendingDownIcon, color: 'text-red-600', bg: 'bg-red-100 dark:bg-red-900/20' },
  { name: 'Lucro Estimado', value: 'R$ 0,00', icon: BanknotesIcon, color: 'text-purple-600', bg: 'bg-purple-100 dark:bg-purple-900/20' },
];
</script>

<template>
  <div class="space-y-8">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Dashboard</h2>
      <p class="text-slate-600 dark:text-slate-400">Visão geral do seu sistema de estoque.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div 
        v-for="stat in stats" 
        :key="stat.name"
        class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow"
      >
        <div class="flex items-center gap-4">
          <div :class="['p-3 rounded-xl', stat.bg, stat.color]">
            <component :is="stat.icon" class="w-6 h-6" />
          </div>
          <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ stat.name }}</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ stat.value }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Recent Activity or Charts could go here -->
      <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Últimas Vendas</h3>
        <div class="flex flex-col items-center justify-center py-12 text-slate-400">
          <p>Nenhuma venda registrada.</p>
        </div>
      </div>

      <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Produtos com Baixo Estoque</h3>
        <div class="flex flex-col items-center justify-center py-12 text-slate-400">
          <p>Estoque regular em todos os produtos.</p>
        </div>
      </div>
    </div>
  </div>
</template>
