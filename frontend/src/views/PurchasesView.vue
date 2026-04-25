<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import { useToast } from '@/composables/useToast';
import SearchableSelect from '@/components/ui/SearchableSelect.vue';
import CurrencyInput from '@/components/ui/CurrencyInput.vue';
import { PlusIcon, TrashIcon, ShoppingBagIcon, ArrowLeftIcon } from '@heroicons/vue/24/outline';
import type { SelectOption } from '@/components/ui/SearchableSelect.vue';

interface Product {
  id: number;
  nome: string;
  preco_venda: number;
}

interface PurchaseItem {
  id: number;
  quantidade: number;
  preco_unitario: number;
}

const toast = useToast();
const router = useRouter();

const products = ref<Product[]>([]);
const purchaseItems = ref<PurchaseItem[]>([]);
const supplier = ref('');
const loading = ref(false);

const fetchProducts = async () => {
  try {
    const response = await api.get('/products', { params: { per_page: 100 } });
    products.value = response.data.data ?? response.data;
  } catch (error) {
    toast.error('Erro ao carregar produtos.');
  }
};

const productOptions = computed<SelectOption[]>(() => {
  return products.value.map(p => ({
    value: p.id,
    label: p.nome,
  }));
});

const addItem = () => {
  if (products.value.length === 0) {
    toast.warning('Cadastre um produto antes de adicionar itens.');
    return;
  }
  purchaseItems.value.push({
    id: products.value[0]?.id || 0,
    quantidade: 1,
    preco_unitario: 0,
  });
};

const removeItem = (index: number) => {
  purchaseItems.value.splice(index, 1);
};

const getSubtotal = (item: PurchaseItem) => {
  return item.quantidade * item.preco_unitario;
};

const totalQuantity = computed(() => {
  return purchaseItems.value.reduce((acc, item) => acc + item.quantidade, 0);
});

const totalPurchase = computed(() => {
  return purchaseItems.value.reduce((acc, item) => acc + getSubtotal(item), 0);
});

const submitPurchase = async () => {
  if (!supplier.value.trim()) {
    toast.warning('Informe o nome do fornecedor.');
    return;
  }
  if (purchaseItems.value.length === 0) {
    toast.warning('Adicione pelo menos um item à compra.');
    return;
  }

  const invalidItems = purchaseItems.value.filter(i => i.preco_unitario <= 0 || i.quantidade <= 0);
  if (invalidItems.length > 0) {
    toast.warning('Todos os itens devem ter quantidade e preço válidos.');
    return;
  }

  try {
    loading.value = true;
    await api.post('/purchases', {
      fornecedor: supplier.value,
      produtos: purchaseItems.value
    });
    
    toast.success('Compra registrada com sucesso! Estoque e custo médio atualizados.');
    supplier.value = '';
    purchaseItems.value = [];
    fetchProducts();
  } catch (error: any) {
    const message = error.response?.data?.message || 'Erro ao registrar compra.';
    toast.error(message);
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
    <div class="flex items-center gap-4">
      <button
        @click="router.push('/purchases/list')"
        class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
        title="Voltar"
      >
        <ArrowLeftIcon class="w-5 h-5" />
      </button>
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Registrar Compra</h2>
        <p class="text-slate-600 dark:text-slate-400">Entrada de produtos no estoque.</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Form Section -->
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
          <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Fornecedor</label>
            <input 
              v-model="supplier"
              type="text" 
              class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all outline-none"
              placeholder="Nome do fornecedor"
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

            <div v-if="purchaseItems.length === 0" class="py-8 text-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-xl text-slate-400">
              Nenhum item adicionado à compra.
            </div>

            <div v-for="(item, index) in purchaseItems" :key="index" class="flex flex-wrap md:flex-nowrap items-end gap-4 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
              <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-slate-500 mb-1">Produto</label>
                <SearchableSelect
                  v-model="item.id"
                  :options="productOptions"
                  placeholder="Buscar produto..."
                />
              </div>
              <div class="w-24">
                <label class="block text-xs font-medium text-slate-500 mb-1">Qtd</label>
                <input 
                  v-model.number="item.quantidade"
                  type="number" 
                  min="1"
                  class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-900 dark:text-white outline-none"
                >
              </div>
              <div class="w-36">
                <label class="block text-xs font-medium text-slate-500 mb-1">Preço Unit.</label>
                <CurrencyInput v-model="item.preco_unitario" />
              </div>
              <div class="w-28 text-right">
                <label class="block text-xs font-medium text-slate-500 mb-1">Subtotal</label>
                <p class="text-sm font-medium text-slate-700 dark:text-slate-300 py-2">{{ formatCurrency(getSubtotal(item)) }}</p>
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
          <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Resumo da Compra</h3>
          
          <div class="space-y-3 mb-8">
            <div class="flex justify-between text-slate-600 dark:text-slate-400">
              <span>Itens</span>
              <span>{{ purchaseItems.length }}</span>
            </div>
            <div class="flex justify-between text-slate-600 dark:text-slate-400">
              <span>Quantidade Total</span>
              <span>{{ totalQuantity }}</span>
            </div>
            <div class="flex justify-between text-lg font-bold text-slate-900 dark:text-white pt-3 border-t border-slate-100 dark:border-slate-800">
              <span>Total</span>
              <span>{{ formatCurrency(totalPurchase) }}</span>
            </div>
          </div>

          <button 
            @click="submitPurchase"
            :disabled="loading || purchaseItems.length === 0 || !supplier"
            class="w-full flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 disabled:bg-slate-300 dark:disabled:bg-slate-800 disabled:cursor-not-allowed text-white px-6 py-3 rounded-xl transition-colors font-bold shadow-lg shadow-primary-500/20"
          >
            <ShoppingBagIcon class="w-5 h-5" v-if="!loading" />
            <span v-if="loading" class="animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent"></span>
            Finalizar Compra
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
