<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import api from '@/services/api';
import { useToast } from '@/composables/useToast';
import { useConfirm } from '@/composables/useConfirm';
import AppModal from '@/components/ui/AppModal.vue';
import AppPagination from '@/components/ui/AppPagination.vue';
import CurrencyInput from '@/components/ui/CurrencyInput.vue';
import { PlusIcon, TrashIcon, PencilIcon } from '@heroicons/vue/24/outline';

interface Product {
  id: number;
  nome: string;
  custo_medio: number;
  preco_venda: number;
  estoque: number;
}

interface PaginationMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

const toast = useToast();
const { confirm } = useConfirm();

const products = ref<Product[]>([]);
const loading = ref(true);
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref<number | null>(null);
const saving = ref(false);
const pagination = ref<PaginationMeta>({ current_page: 1, last_page: 1, per_page: 15, total: 0 });

const form = ref({
  nome: '',
  preco_venda: 0,
});

const fetchProducts = async (page = 1) => {
  try {
    loading.value = true;
    const response = await api.get('/products', { params: { page, per_page: 15 } });
    products.value = response.data.data;
    pagination.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      per_page: response.data.per_page,
      total: response.data.total,
    };
  } catch (error: any) {
    toast.error('Erro ao carregar produtos.');
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  isEditing.value = false;
  editingId.value = null;
  form.value = { nome: '', preco_venda: 0 };
  showModal.value = true;
};

const openEditModal = (product: Product) => {
  isEditing.value = true;
  editingId.value = product.id;
  form.value = { nome: product.nome, preco_venda: product.preco_venda };
  showModal.value = true;
};

const saveProduct = async () => {
  if (!form.value.nome || form.value.nome.length < 3) {
    toast.warning('O nome deve ter pelo menos 3 caracteres.');
    return;
  }
  if (!form.value.preco_venda || form.value.preco_venda <= 0) {
    toast.warning('O preço de venda deve ser positivo.');
    return;
  }

  try {
    saving.value = true;

    if (isEditing.value && editingId.value) {
      await api.put(`/products/${editingId.value}`, form.value);
      toast.success('Produto atualizado com sucesso!');
    } else {
      await api.post('/products', form.value);
      toast.success('Produto cadastrado com sucesso!');
    }

    showModal.value = false;
    form.value = { nome: '', preco_venda: 0 };
    fetchProducts(pagination.value.current_page);
  } catch (error: any) {
    const message = error.response?.data?.errors
      ? Object.values(error.response.data.errors).flat().join(', ')
      : error.response?.data?.message || 'Erro ao salvar produto.';
    toast.error(message);
  } finally {
    saving.value = false;
  }
};

const deleteProduct = async (product: Product) => {
  const confirmed = await confirm({
    title: 'Excluir Produto',
    message: `Tem certeza que deseja excluir o produto "${product.nome}"? Esta ação não pode ser desfeita.`,
    confirmText: 'Excluir',
    cancelText: 'Cancelar',
    variant: 'danger',
  });

  if (!confirmed) return;

  try {
    await api.delete(`/products/${product.id}`);
    toast.success('Produto excluído com sucesso!');
    fetchProducts(pagination.value.current_page);
  } catch (error: any) {
    const message = error.response?.data?.message || 'Erro ao excluir produto.';
    toast.error(message);
  }
};

const handlePageChange = (page: number) => {
  fetchProducts(page);
};

onMounted(() => fetchProducts());

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
        @click="openCreateModal"
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
                  :class="product.estoque > 10 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : product.estoque > 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'"
                >
                  {{ product.estoque }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm text-center">
                <div class="flex items-center justify-center gap-3">
                  <button 
                    @click="openEditModal(product)"
                    class="text-slate-400 hover:text-primary-600 transition-colors" 
                    title="Editar"
                  >
                    <PencilIcon class="w-5 h-5" />
                  </button>
                  <button 
                    @click="deleteProduct(product)"
                    class="text-slate-400 hover:text-red-600 transition-colors"
                    title="Excluir"
                  >
                    <TrashIcon class="w-5 h-5" />
                  </button>
                </div>
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

    <!-- Modal (Create/Edit Product) -->
    <AppModal
      :show="showModal"
      :title="isEditing ? 'Editar Produto' : 'Cadastrar Produto'"
      @close="showModal = false"
    >
      <form @submit.prevent="saveProduct" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nome</label>
          <input 
            v-model="form.nome"
            type="text" 
            required
            minlength="3"
            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all outline-none"
            placeholder="Ex: iPhone 15 Pro Max"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Preço de Venda Sugerido</label>
          <CurrencyInput v-model="form.preco_venda" />
        </div>

        <template v-if="isEditing">
          <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-3 text-xs text-slate-500 dark:text-slate-400">
            <p>ℹ️ O custo médio e o estoque são calculados automaticamente pelas compras e vendas.</p>
          </div>
        </template>

        <div class="flex items-center gap-3 mt-6">
          <button 
            type="button"
            @click="showModal = false"
            class="flex-1 px-4 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors border border-slate-200 dark:border-slate-700"
          >
            Cancelar
          </button>
          <button 
            type="submit"
            :disabled="saving"
            class="flex-1 bg-primary-600 hover:bg-primary-700 disabled:bg-primary-400 text-white px-4 py-2.5 rounded-xl transition-colors font-medium flex items-center justify-center gap-2"
          >
            <span v-if="saving" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
            {{ isEditing ? 'Atualizar' : 'Salvar' }}
          </button>
        </div>
      </form>
    </AppModal>
  </div>
</template>
