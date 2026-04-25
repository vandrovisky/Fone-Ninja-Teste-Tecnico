<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import { useToast } from '@/composables/useToast';
import { 
  CubeIcon, 
  ArrowTrendingUpIcon, 
  ArrowTrendingDownIcon,
  BanknotesIcon 
} from '@heroicons/vue/24/outline';

const toast = useToast();
const loading = ref(true);

const dashboardData = ref({
  total_products: 0,
  total_sales: 0,
  total_purchases: 0,
  total_profit: 0,
  recent_sales: [] as any[],
  low_stock_products: [] as any[]
});

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const fetchDashboard = async () => {
  try {
    loading.value = true;
    const response = await api.get('/dashboard');
    dashboardData.value = response.data;
  } catch (error) {
    toast.error('Erro ao carregar dados do dashboard.');
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchDashboard();
});
</script>

<template>
  <div class="space-y-8">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Dashboard</h2>
      <p class="text-slate-600 dark:text-slate-400">Visão geral do seu sistema de estoque.</p>
    </div>

    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="i in 4" :key="i" class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 animate-pulse">
        <div class="flex items-center gap-4">
          <div class="p-3 rounded-xl bg-slate-100 dark:bg-slate-800 w-12 h-12"></div>
          <div class="flex-1 space-y-2">
            <div class="h-4 bg-slate-100 dark:bg-slate-800 rounded w-1/2"></div>
            <div class="h-6 bg-slate-100 dark:bg-slate-800 rounded w-3/4"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Total Produtos -->
      <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4">
          <div class="p-3 rounded-xl bg-blue-100 dark:bg-blue-900/20 text-blue-600">
            <CubeIcon class="w-6 h-6" />
          </div>
          <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total de Produtos</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ dashboardData.total_products }}</p>
          </div>
        </div>
      </div>

      <!-- Total Vendas -->
      <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4">
          <div class="p-3 rounded-xl bg-green-100 dark:bg-green-900/20 text-green-600">
            <ArrowTrendingUpIcon class="w-6 h-6" />
          </div>
          <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total de Vendas</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ formatCurrency(dashboardData.total_sales) }}</p>
          </div>
        </div>
      </div>

      <!-- Total Compras -->
      <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4">
          <div class="p-3 rounded-xl bg-red-100 dark:bg-red-900/20 text-red-600">
            <ArrowTrendingDownIcon class="w-6 h-6" />
          </div>
          <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total de Compras</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ formatCurrency(dashboardData.total_purchases) }}</p>
          </div>
        </div>
      </div>

      <!-- Lucro -->
      <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4">
          <div class="p-3 rounded-xl bg-purple-100 dark:bg-purple-900/20 text-purple-600">
            <BanknotesIcon class="w-6 h-6" />
          </div>
          <div>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Lucro Estimado</p>
            <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ formatCurrency(dashboardData.total_profit) }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Últimas Vendas</h3>
        
        <div v-if="loading" class="animate-pulse space-y-4">
          <div v-for="i in 3" :key="i" class="h-16 bg-slate-100 dark:bg-slate-800 rounded-xl w-full"></div>
        </div>
        <div v-else-if="dashboardData.recent_sales.length === 0" class="flex flex-col items-center justify-center py-12 text-slate-400">
          <p>Nenhuma venda registrada.</p>
        </div>
        <div v-else class="space-y-3">
          <div 
            v-for="sale in dashboardData.recent_sales" 
            :key="sale.id"
            class="flex items-center justify-between p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 hover:border-slate-200 dark:hover:border-slate-700 transition-colors"
          >
            <div>
              <p class="font-bold text-slate-900 dark:text-white">{{ sale.client }}</p>
              <p class="text-sm text-slate-500 dark:text-slate-400">{{ formatDate(sale.created_at) }} • {{ sale.items_count }} itens</p>
            </div>
            <div class="text-right">
              <p class="font-bold text-green-600">{{ formatCurrency(sale.total_value) }}</p>
              <p class="text-xs text-slate-500 dark:text-slate-400">Lucro: {{ formatCurrency(sale.profit) }}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Produtos com Baixo Estoque</h3>
        
        <div v-if="loading" class="animate-pulse space-y-4">
          <div v-for="i in 3" :key="i" class="h-16 bg-slate-100 dark:bg-slate-800 rounded-xl w-full"></div>
        </div>
        <div v-else-if="dashboardData.low_stock_products.length === 0" class="flex flex-col items-center justify-center py-12 text-slate-400">
          <p>Estoque regular em todos os produtos.</p>
        </div>
        <div v-else class="space-y-3">
          <div 
            v-for="product in dashboardData.low_stock_products" 
            :key="product.id"
            class="flex items-center justify-between p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 hover:border-slate-200 dark:hover:border-slate-700 transition-colors"
          >
            <div>
              <p class="font-bold text-slate-900 dark:text-white">{{ product.nome }}</p>
              <p class="text-sm text-slate-500 dark:text-slate-400">Preço: {{ formatCurrency(product.preco_venda) }}</p>
            </div>
            <div class="text-right flex items-center gap-3">
              <div class="flex flex-col items-end">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Estoque</span>
                <span :class="[
                  'px-2.5 py-1 rounded-lg text-sm font-bold',
                  product.estoque === 0 ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
                ]">
                  {{ product.estoque }} un
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
