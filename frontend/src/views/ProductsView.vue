<script setup lang="ts">
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import { PlusIcon, TrashIcon, PencilIcon } from '@heroicons/vue/24/outline';

interface Product {
  id: number;
  nome: string;
  custo_medio: number;
  preco_venda: number;
  estoque: number;
}

const products = ref<Product[]>([]);
const loading = ref(true);
const showModal = ref(false);
const form = ref({
  nome: '',
  preco_venda: 0,
});

const fetchProducts = async () => {
  try {
    loading.value = true;
    const response = await api.get('/produtos');
    products.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar produtos:', error);
  } finally {
    loading.value = false;
  }
};

const saveProduct = async () => {
  try {
    await api.post('/produtos', form.value);
    showModal.value = false;
    form.value = { nome: '', preco_venda: 0 };
    fetchProducts();
  } catch (error) {
    console.error('Erro ao salvar produto:', error);
  }
};

onMounted(fetchProducts);

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Produtos</h2>
        <p class="text-slate-600 dark:text-slate-400">Gerencie seu catálogo de produtos.</p>
      </div>
      <button 
        @click="showModal = true"
        class="flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-xl transition-colors font-medium"
      >
        <PlusIcon class="w-5 h-5" />
        Novo Produto
      </button>
    </div>

    <!-- Products Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white">ID</th>
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white">Nome</th>
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white text-right">Custo Médio</th>
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white text-right">Preço Venda</th>
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white text-center">Estoque</th>
              <th class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white text-center">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
            <tr v-if="loading" v-for="i in 3" :key="i" class="animate-pulse">
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-8"></div></td>
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-32"></div></td>
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-20 ml-auto"></div></td>
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-20 ml-auto"></div></td>
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-12 mx-auto"></div></td>
              <td class="px-6 py-4"><div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-16 mx-auto"></div></td>
            </tr>
            <tr v-else-if="products.length === 0">
              <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                Nenhum produto cadastrado.
              </td>
            </tr>
            <tr 
              v-for="product in products" 
              :key="product.id"
              class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors"
            >
              <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">{{ product.id }}</td>
              <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-white">{{ product.nome }}</td>
              <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 text-right">{{ formatCurrency(product.custo_medio) }}</td>
              <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400 text-right">{{ formatCurrency(product.preco_venda) }}</td>
              <td class="px-6 py-4 text-sm text-center">
                <span 
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="product.estoque > 10 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'"
                >
                  {{ product.estoque }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-center">
                <div class="flex items-center justify-center gap-3">
                  <button class="text-slate-400 hover:text-primary-600 transition-colors">
                    <PencilIcon class="w-5 h-5" />
                  </button>
                  <button class="text-slate-400 hover:text-red-600 transition-colors">
                    <TrashIcon class="w-5 h-5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal (New Product) -->
    <div 
      v-if="showModal" 
      class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
      @click.self="showModal = false"
    >
      <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-2xl p-6 shadow-2xl border border-slate-200 dark:border-slate-800">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Cadastrar Produto</h3>
        
        <form @submit.prevent="saveProduct" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nome</label>
            <input 
              v-model="form.nome"
              type="text" 
              required
              class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
              placeholder="Ex: iPhone 15 Pro Max"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Preço de Venda Sugerido</label>
            <input 
              v-model="form.preco_venda"
              type="number" 
              step="0.01"
              required
              class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
              placeholder="0.00"
            >
          </div>

          <div class="flex items-center gap-3 mt-8">
            <button 
              type="button"
              @click="showModal = false"
              class="flex-1 px-4 py-2 rounded-xl text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            >
              Cancelar
            </button>
            <button 
              type="submit"
              class="flex-1 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-xl transition-colors font-medium"
            >
              Salvar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
