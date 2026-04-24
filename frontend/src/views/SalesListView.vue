<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import { PlusIcon, CurrencyDollarIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { RouterLink } from 'vue-router';

interface Sale {
  id: number;
  client: string;
  total_value: number;
  total_profit: number;
  created_at: string;
  items: any[];
}

const sales = ref<Sale[]>([]);
const loading = ref(true);

const fetchSales = async () => {
  try {
    loading.value = true;
    const response = await api.get('/vendas');
    sales.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar vendas:', error);
  } finally {
    loading.value = false;
  }
};

const deleteSale = async (id: number) => {
  if (!confirm('Tem certeza que deseja cancelar esta venda? O estoque será revertido.')) return;
  
  try {
    await api.delete(`/vendas/${id}`);
    fetchSales();
  } catch (error) {
    console.error('Erro ao cancelar venda:', error);
  }
};

onMounted(fetchSales);

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
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Histórico de Vendas</h2>
        <p class="text-slate-600 dark:text-slate-400">Acompanhe suas receitas e lucros.</p>
      </div>
      <RouterLink 
        to="/sales"
        class="flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-xl transition-colors font-medium"
      >
        <PlusIcon class="w-5 h-5" />
        Nova Venda
      </RouterLink>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white">ID</th>
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white">Data</th>
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white">Cliente</th>
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white text-right">Total</th>
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white text-right text-green-600 dark:text-green-400">Lucro</th>
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white text-center">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
            <tr v-if="loading" v-for="i in 3" :key="i" class="animate-pulse">
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-8"></div></td>
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-24"></div></td>
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-32"></div></td>
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-20 ml-auto"></div></td>
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-20 ml-auto"></div></td>
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-8 mx-auto"></div></td>
            </tr>
            <tr v-else-if="sales.length === 0">
              <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                Nenhuma venda registrada.
              </td>
            </tr>
            <tr 
              v-for="sale in sales" 
              :key="sale.id"
              class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"
            >
              <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">#{{ sale.id }}</td>
              <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ formatDate(sale.created_at) }}</td>
              <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ sale.client }}</td>
              <td class="px-6 py-4 text-sm text-slate-900 dark:text-white text-right font-bold">{{ formatCurrency(sale.total_value) }}</td>
              <td class="px-6 py-4 text-sm text-right font-medium text-green-600 dark:text-green-400">
                +{{ formatCurrency(sale.total_profit) }}
              </td>
              <td class="px-6 py-4 text-sm text-center">
                <button 
                  @click="deleteSale(sale.id)"
                  class="text-slate-400 hover:text-red-600 transition-colors"
                  title="Cancelar Venda"
                >
                  <TrashIcon class="w-5 h-5" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
