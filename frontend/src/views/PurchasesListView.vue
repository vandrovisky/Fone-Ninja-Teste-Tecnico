<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import { useToast } from '@/composables/useToast';
import AppPagination from '@/components/ui/AppPagination.vue';
import { PlusIcon, ShoppingCartIcon } from '@heroicons/vue/24/outline';
import { RouterLink } from 'vue-router';

interface Purchase {
  id: number;
  supplier: string;
  total_value: number;
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

const purchases = ref<Purchase[]>([]);
const loading = ref(true);
const pagination = ref<PaginationMeta>({ current_page: 1, last_page: 1, per_page: 15, total: 0 });

const fetchPurchases = async (page = 1) => {
  try {
    loading.value = true;
    const response = await api.get('/purchases', { params: { page, per_page: 15 } });
    purchases.value = response.data.data;
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      per_page: response.data.per_page,
      total: response.data.total,
    };
  } catch (error) {
    toast.error('Erro ao carregar compras.');
  } finally {
    loading.value = false;
  }
};

const handlePageChange = (page: number) => {
  fetchPurchases(page);
};

onMounted(() => fetchPurchases());

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
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Histórico de Compras</h2>
        <p class="text-slate-600 dark:text-slate-400">Visualize todas as entradas de estoque.</p>
      </div>
      <RouterLink 
        to="/purchases"
        class="flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-xl transition-colors font-medium"
      >
        <PlusIcon class="w-5 h-5" />
        Nova Compra
      </RouterLink>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white">ID</th>
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white">Data</th>
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white">Fornecedor</th>
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white text-right">Total</th>
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white text-center">Itens</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
            <tr v-if="loading" v-for="i in 5" :key="i" class="animate-pulse">
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-8"></div></td>
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-24"></div></td>
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-32"></div></td>
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-20 ml-auto"></div></td>
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-12 mx-auto"></div></td>
            </tr>
            <tr v-else-if="purchases.length === 0">
              <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                Nenhuma compra registrada.
              </td>
            </tr>
            <tr 
              v-for="purchase in purchases" 
              :key="purchase.id"
              class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"
            >
              <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">#{{ purchase.id }}</td>
              <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">{{ formatDate(purchase.created_at) }}</td>
              <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ purchase.supplier }}</td>
              <td class="px-6 py-4 text-sm text-slate-900 dark:text-white text-right font-bold">{{ formatCurrency(purchase.total_value) }}</td>
              <td class="px-6 py-4 text-sm text-center">
                <span class="inline-flex items-center px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                  <ShoppingCartIcon class="w-4 h-4 mr-1" />
                  {{ purchase.items?.length || 0 }}
                </span>
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
