<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import { useToast } from '@/composables/useToast';
import SearchableSelect from '@/components/ui/SearchableSelect.vue';
import CurrencyInput from '@/components/ui/CurrencyInput.vue';
import { PlusIcon, TrashIcon, BanknotesIcon, ArrowLeftIcon } from '@heroicons/vue/24/outline';
import type { SelectOption } from '@/components/ui/SearchableSelect.vue';

interface Product {
  id: number;
  nome: string;
  estoque: number;
  preco_venda: number;
  custo_medio: number;
}

interface SaleItem {
  id: number;
  quantidade: number;
  preco_unitario: number;
}

const toast = useToast();
const router = useRouter();

const products = ref<Product[]>([]);
const saleItems = ref<SaleItem[]>([]);
const client = ref('');
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
    sublabel: `Estoque: ${p.estoque}`,
  }));
});

const addItem = () => {
  if (products.value.length === 0) {
    toast.warning('Cadastre um produto antes de adicionar itens.');
    return;
  }
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

const getSubtotal = (item: SaleItem) => {
  return item.quantidade * item.preco_unitario;
};

const getItemProfit = (item: SaleItem) => {
  const product = products.value.find(p => p.id === item.id);
  if (!product) return 0;
  return (item.preco_unitario - product.custo_medio) * item.quantidade;
};

const getItemStock = (item: SaleItem) => {
  const product = products.value.find(p => p.id === item.id);
  return product?.estoque ?? 0;
};

const isItemOverStock = (item: SaleItem) => {
  return item.quantidade > getItemStock(item);
};

const totalSale = computed(() => {
  return saleItems.value.reduce((acc, item) => acc + getSubtotal(item), 0);
});

const estimatedProfit = computed(() => {
  return saleItems.value.reduce((acc, item) => acc + getItemProfit(item), 0);
});

const profitMargin = computed(() => {
  if (totalSale.value === 0) return 0;
  return (estimatedProfit.value / totalSale.value) * 100;
});

const hasStockIssues = computed(() => {
  return saleItems.value.some(item => isItemOverStock(item));
});

const submitSale = async () => {
  if (!client.value.trim()) {
    toast.warning('Informe o nome do cliente.');
    return;
  }
  if (saleItems.value.length === 0) {
    toast.warning('Adicione pelo menos um item à venda.');
    return;
  }
  if (hasStockIssues.value) {
    toast.error('Existem itens com quantidade superior ao estoque disponível.');
    return;
  }

  try {
    loading.value = true;
    const response = await api.post('/sales', {
      cliente: client.value,
      produtos: saleItems.value
    });
    
    const totalVenda = formatCurrency(response.data.total_venda);
    const lucro = formatCurrency(response.data.lucro_total);
    toast.success(`Venda realizada! Total: ${totalVenda} | Lucro: ${lucro}`);
    
    client.value = '';
    saleItems.value = [];
    fetchProducts();
  } catch (error: any) {
    const message = error.response?.data?.message || 'Erro ao registrar venda.';
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
        @click="router.push('/sales/list')"
        class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
        title="Voltar"
      >
        <ArrowLeftIcon class="w-5 h-5" />
      </button>
      <div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Registrar Venda</h2>
        <p class="text-slate-600 dark:text-slate-400">Saída de produtos e cálculo de lucro.</p>
      </div>
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
              class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all outline-none"
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

            <div 
              v-for="(item, index) in saleItems" 
              :key="index" 
              :class="[
                'flex flex-wrap md:flex-nowrap items-end gap-4 p-4 rounded-xl transition-colors',
                isItemOverStock(item) 
                  ? 'bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800' 
                  : 'bg-slate-50 dark:bg-slate-800/50'
              ]"
            >
              <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-slate-500 mb-1">Produto</label>
                <SearchableSelect
                  :modelValue="item.id"
                  @update:modelValue="(val) => { item.id = val as number; updatePrice(index); }"
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
                  :max="getItemStock(item)"
                  :class="[
                    'w-full bg-white dark:bg-slate-900 border rounded-lg px-3 py-2 text-sm text-slate-900 dark:text-white outline-none',
                    isItemOverStock(item) 
                      ? 'border-red-400 dark:border-red-600' 
                      : 'border-slate-200 dark:border-slate-700'
                  ]"
                >
                <p v-if="isItemOverStock(item)" class="text-xs text-red-500 mt-1">
                  Máx: {{ getItemStock(item) }}
                </p>
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

            <!-- Estimated Profit -->
            <div class="bg-green-50 dark:bg-green-900/10 rounded-xl p-4 space-y-2 border border-green-100 dark:border-green-900/30">
              <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-green-700 dark:text-green-400">Lucro Estimado</span>
                <span 
                  :class="[
                    'text-lg font-bold',
                    estimatedProfit >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
                  ]"
                >
                  {{ estimatedProfit >= 0 ? '+' : '' }}{{ formatCurrency(estimatedProfit) }}
                </span>
              </div>
              <div class="flex justify-between items-center text-xs text-green-600/70 dark:text-green-400/60">
                <span>Margem</span>
                <span>{{ profitMargin.toFixed(1) }}%</span>
              </div>
            </div>

            <!-- Stock warning -->
            <div v-if="hasStockIssues" class="bg-red-50 dark:bg-red-900/10 rounded-xl p-3 border border-red-200 dark:border-red-800">
              <p class="text-xs text-red-600 dark:text-red-400 font-medium">
                ⚠️ Existem itens com quantidade acima do estoque disponível.
              </p>
            </div>
          </div>

          <button 
            @click="submitSale"
            :disabled="loading || saleItems.length === 0 || !client || hasStockIssues"
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
