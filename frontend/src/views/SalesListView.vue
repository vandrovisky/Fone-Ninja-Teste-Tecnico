<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import { useToast } from '@/composables/useToast';
import { useConfirm } from '@/composables/useConfirm';
import AppPagination from '@/components/ui/AppPagination.vue';
import { PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';
import { RouterLink } from 'vue-router';

interface Sale {
  id: number;
  client: string;
  total_value: number;
  total_profit: number;
  created_at: string;
  items: any[];
}

interface PaginationMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

const toast = useToast();
const { confirm } = useConfirm();

const sales = ref<Sale[]>([]);
const loading = ref(true);
const pagination = ref<PaginationMeta>({ current_page: 1, last_page: 1, per_page: 15, total: 0 });

const fetchSales = async (page = 1) => {
  try {
    loading.value = true;
    const response = await api.get('/sales', { params: { page, per_page: 15 } });
    sales.value = response.data.data;
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      per_page: response.data.per_page,
      total: response.data.total,
    };
  } catch (error) {
    toast.error('Erro ao carregar vendas.');
  } finally {
    loading.value = false;
  }
};

const deleteSale = async (sale: Sale) => {
  const confirmed = await confirm({
    title: 'Cancelar Venda',
    message: `Tem certeza que deseja cancelar a venda #${sale.id} para "${sale.client}"? O estoque dos produtos será revertido.`,
    confirmText: 'Sim, Cancelar Venda',
    cancelText: 'Não, Manter',
    variant: 'danger',
  });

  if (!confirmed) return;

  try {
    await api.delete(`/sales/${sale.id}`);
    toast.success('Venda cancelada com sucesso! Estoque revertido.');
    fetchSales(pagination.value.current_page);
  } catch (error: any) {
    const message = error.response?.data?.message || 'Erro ao cancelar venda.';
    toast.error(message);
  }
};

const handlePageChange = (page: number) => {
  fetchSales(page);
};

onMounted(() => fetchSales());

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
              <th class="px-6 py-4 text-sm font-semibold text-green-600 dark:text-green-400 text-right">Lucro</th>
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white text-center">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
            <tr v-if="loading" v-for="i in 5" :key="i" class="animate-pulse">
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
                  @click="deleteSale(sale)"
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

      <!-- Pagination -->
      <div class="px-6 py-3 border-t border-slate-200 dark:border-slate-800">
        <AppPagination
          :current-page="pagination.current_page"
          :total-items="pagination.total"
          :per-page="pagination.per_page"
          @page-change="handlePageChange"
        />
      </div>
    </div>
  </div>
</template>
