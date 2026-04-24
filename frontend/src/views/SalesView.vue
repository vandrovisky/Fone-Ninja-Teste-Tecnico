<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import api from '@/services/api';
import { PlusIcon, TrashIcon, BanknotesIcon } from '@heroicons/vue/24/outline';

interface Product {
  id: number;
  nome: string;
  estoque: number;
  preco_venda: number;
}

interface SaleItem {
  id: number;
  quantidade: number;
  preco_unitario: number;
}

const products = ref<Product[]>([]);
const saleItems = ref<SaleItem[]>([]);
const client = ref('');
const loading = ref(false);

const fetchProducts = async () => {
  try {
    const response = await api.get('/produtos');
    products.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar produtos:', error);
  }
};

const addItem = () => {
  const firstProduct = products.value[0];
  saleItems.value.push({
    id: firstProduct?.id || 0,
    quantidade: 1,
    preco_unitario: firstProduct?.preco_venda || 0,
  });
};

const updatePrice = (index: number) => {
  const item = saleItems.value[index];
  if (!item) return;
  const product = products.value.find(p => p.id === item.id);
  if (product) {
    item.preco_unitario = product.preco_venda;
  }
};

const removeItem = (index: number) => {
  saleItems.value.splice(index, 1);
};

const totalSale = computed(() => {
  return saleItems.value.reduce((acc, item) => acc + (item.quantidade * item.preco_unitario), 0);
});

const submitSale = async () => {
  if (!client.value || saleItems.value.length === 0) return;

  try {
    loading.value = true;
    const response = await api.post('/vendas', {
      cliente: client.value,
      produtos: saleItems.value
    });
    
    // Reset form after success
    client.value = '';
    saleItems.value = [];
    alert(`Venda realizada! Total: ${formatCurrency(response.data.total_venda)} | Lucro: ${formatCurrency(response.data.lucro_total)}`);
  } catch (error: any) {
    console.error('Erro ao registrar venda:', error);
    const message = error.response?.data?.message || 'Erro ao registrar venda.';
    alert(message);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchProducts);

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};
</script>

<template>
  <div class="space-y-6">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Registrar Venda</h2>
      <p class="text-slate-600 dark:text-slate-400">Saída de produtos e cálculo de lucro.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Form Section -->
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
          <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Cliente</label>
            <input 
              v-model="client"
              type="text" 
              class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
              placeholder="Nome do cliente"
            >
          </div>

          <div class="space-y-4">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider">Produtos</h3>
              <button 
                @click="addItem"
                class="text-sm text-primary-600 dark:text-primary-400 font-medium hover:underline flex items-center gap-1"
              >
                <PlusIcon class="w-4 h-4" />
                Adicionar Item
              </button>
            </div>

            <div v-if="saleItems.length === 0" class="py-8 text-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-xl text-slate-400">
              Nenhum item adicionado à venda.
            </div>

            <div v-for="(item, index) in saleItems" :key="index" class="flex flex-wrap md:flex-nowrap items-end gap-4 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
              <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-slate-500 mb-1">Produto</label>
                <select 
                  v-model="item.id"
                  @change="updatePrice(index)"
                  class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-900 dark:text-white"
                >
                  <option v-for="p in products" :key="p.id" :value="p.id">
                    {{ p.nome }} (Estoque: {{ p.estoque }})
                  </option>
                </select>
              </div>
              <div class="w-24">
                <label class="block text-xs font-medium text-slate-500 mb-1">Qtd</label>
                <input 
                  v-model.number="item.quantidade"
                  type="number" 
                  min="1"
                  class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-900 dark:text-white"
                >
              </div>
              <div class="w-32">
                <label class="block text-xs font-medium text-slate-500 mb-1">Preço Unit.</label>
                <input 
                  v-model.number="item.preco_unitario"
                  type="number" 
                  step="0.01"
                  class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-900 dark:text-white"
                >
              </div>
              <button 
                @click="removeItem(index)"
                class="p-2 text-slate-400 hover:text-red-600 transition-colors"
              >
                <TrashIcon class="w-5 h-5" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Summary Section -->
      <div class="space-y-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm sticky top-8">
          <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Resumo da Venda</h3>
          
          <div class="space-y-3 mb-8">
            <div class="flex justify-between text-slate-600 dark:text-slate-400">
              <span>Itens</span>
              <span>{{ saleItems.length }}</span>
            </div>
            <div class="flex justify-between text-lg font-bold text-slate-900 dark:text-white pt-3 border-t border-slate-100 dark:border-slate-800">
              <span>Total</span>
              <span>{{ formatCurrency(totalSale) }}</span>
            </div>
          </div>

          <button 
            @click="submitSale"
            :disabled="loading || saleItems.length === 0 || !client"
            class="w-full flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 disabled:bg-slate-300 dark:disabled:bg-slate-800 disabled:cursor-not-allowed text-white px-6 py-3 rounded-xl transition-colors font-bold shadow-lg shadow-primary-500/20"
          >
            <BanknotesIcon class="w-5 h-5" v-if="!loading" />
            <span v-if="loading" class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent"></span>
            Finalizar Venda
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
