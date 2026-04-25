<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useAuth } from '@/services/auth';
import api from '@/services/api';
import { useToast } from '@/composables/useToast';
import { useConfirm } from '@/composables/useConfirm';
import { 
  UserCircleIcon, 
  EnvelopeIcon, 
  IdentificationIcon,
  LockClosedIcon,
  TrashIcon,
  PencilSquareIcon,
  CheckIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline';

const { auth, logout } = useAuth();
const toast = useToast();
const confirm = useConfirm();
const loading = ref(false);
const saving = ref(false);

const userDetails = ref<any>(null);
const isEditing = ref(false);
const editForm = ref({
  name: '',
  email: ''
});

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
});

const fetchProfile = async () => {
  try {
    loading.value = true;
    const response = await api.get('/me');
    userDetails.value = response.data;
    editForm.value.name = response.data.name;
    editForm.value.email = response.data.email;
  } catch (e) {
    toast.error('Erro ao carregar os dados do perfil.');
  } finally {
    loading.value = false;
  }
};

const handleUpdateProfile = async () => {
  try {
    saving.value = true;
    const response = await api.put('/me', editForm.value);
    userDetails.value = response.data.user;
    auth.user = response.data.user;
    isEditing.value = false;
    toast.success('Perfil atualizado com sucesso!');
  } catch (e: any) {
    toast.error(e.response?.data?.message || 'Erro ao atualizar perfil.');
  } finally {
    saving.value = false;
  }
};

const handleUpdatePassword = async () => {
  try {
    saving.value = true;
    await api.put('/me/password', passwordForm.value);
    toast.success('Senha alterada com sucesso!');
    passwordForm.value = {
      current_password: '',
      password: '',
      password_confirmation: ''
    };
  } catch (e: any) {
    toast.error(e.response?.data?.message || 'Erro ao alterar senha.');
  } finally {
    saving.value = false;
  }
};

const handleDeleteAccount = async () => {
  const confirmed = await confirm.confirm({
    title: 'Excluir Conta',
    message: 'Tem certeza que deseja excluir sua conta? Esta ação é permanente e você perderá acesso ao sistema.',
    confirmText: 'Sim, Excluir Minha Conta',
    variant: 'danger'
  });

  if (!confirmed) return;

  try {
    loading.value = true;
    await api.delete('/me');
    toast.success('Conta excluída. Sentiremos sua falta!');
    logout();
  } catch (e: any) {
    toast.error('Erro ao excluir conta.');
    loading.value = false;
  }
};

onMounted(() => {
  fetchProfile();
});
</script>

<template>
  <div class="space-y-6 max-w-4xl mx-auto pb-12">
    <div>
      <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Meu Perfil</h2>
      <p class="text-slate-600 dark:text-slate-400">Gerencie suas informações pessoais e segurança.</p>
    </div>

    <!-- Perfil Card -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
      <div class="bg-primary-600 px-6 py-10 flex flex-col sm:flex-row items-center gap-6">
        <div class="h-24 w-24 bg-white rounded-full flex items-center justify-center text-primary-600 shadow-xl shrink-0 ring-4 ring-white/20">
          <UserCircleIcon class="h-20 w-20" />
        </div>
        <div class="text-center sm:text-left text-white flex-1">
          <h3 class="text-2xl font-bold">{{ userDetails?.name || 'Carregando...' }}</h3>
          <p class="text-primary-100 opacity-80">{{ userDetails?.email }}</p>
        </div>
        <button 
          v-if="!isEditing"
          @click="isEditing = true"
          class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl backdrop-blur-md transition-colors flex items-center gap-2 text-sm font-bold"
        >
          <PencilSquareIcon class="w-4 h-4" />
          Editar Perfil
        </button>
      </div>

      <div class="p-6 sm:p-10">
        <form @submit.prevent="handleUpdateProfile" class="space-y-6">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Nome Completo</label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                  <IdentificationIcon class="w-5 h-5" />
                </span>
                <input 
                  v-model="editForm.name"
                  :disabled="!isEditing"
                  type="text"
                  class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all disabled:opacity-60"
                >
              </div>
            </div>

            <div class="space-y-2">
              <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Endereço de Email</label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                  <EnvelopeIcon class="w-5 h-5" />
                </span>
                <input 
                  v-model="editForm.email"
                  :disabled="!isEditing"
                  type="email"
                  class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all disabled:opacity-60"
                >
              </div>
            </div>
          </div>

          <div v-if="isEditing" class="flex justify-end gap-3 pt-4">
            <button 
              type="button"
              @click="isEditing = false"
              class="px-4 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors font-bold"
            >
              Cancelar
            </button>
            <button 
              type="submit"
              :disabled="saving"
              class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl shadow-lg shadow-primary-500/20 transition-all flex items-center gap-2 font-bold"
            >
              <CheckIcon v-if="!saving" class="w-5 h-5" />
              <span v-else class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
              {{ saving ? 'Salvando...' : 'Salvar Alterações' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Alterar Senha -->
      <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8">
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
          <LockClosedIcon class="w-6 h-6 text-primary-600" />
          Segurança
        </h3>
        
        <form @submit.prevent="handleUpdatePassword" class="space-y-4">
          <div class="space-y-1.5">
            <label class="text-sm font-medium text-slate-600 dark:text-slate-400">Senha Atual</label>
            <input 
              v-model="passwordForm.current_password"
              type="password"
              class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none"
              placeholder="••••••••"
            >
          </div>
          <div class="space-y-1.5">
            <label class="text-sm font-medium text-slate-600 dark:text-slate-400">Nova Senha</label>
            <input 
              v-model="passwordForm.password"
              type="password"
              class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none"
              placeholder="Mínimo 6 caracteres"
            >
          </div>
          <div class="space-y-1.5">
            <label class="text-sm font-medium text-slate-600 dark:text-slate-400">Confirmar Nova Senha</label>
            <input 
              v-model="passwordForm.password_confirmation"
              type="password"
              class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none"
              placeholder="Repita a nova senha"
            >
          </div>
          
          <button 
            type="submit"
            :disabled="saving"
            class="w-full mt-4 bg-slate-900 dark:bg-slate-700 hover:bg-black dark:hover:bg-slate-600 text-white font-bold py-3 rounded-xl transition-all disabled:opacity-50"
          >
            {{ saving ? 'Atualizando...' : 'Atualizar Senha' }}
          </button>
        </form>
      </div>

      <!-- Zona de Perigo -->
      <div class="bg-red-50 dark:bg-red-950/20 rounded-3xl border border-red-100 dark:border-red-900/30 p-6 sm:p-8 flex flex-col">
        <h3 class="text-xl font-bold text-red-700 dark:text-red-400 mb-2 flex items-center gap-2">
          <TrashIcon class="w-6 h-6" />
          Zona de Perigo
        </h3>
        <p class="text-sm text-red-600 dark:text-red-400/80 mb-8">
          Ao excluir sua conta, todos os seus dados de acesso serão removidos permanentemente. Esta ação não pode ser desfeita.
        </p>
        
        <div class="mt-auto space-y-4">
          <button 
            @click="handleDeleteAccount"
            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-red-500/20"
          >
            Excluir Minha Conta
          </button>

          <button 
            @click="logout"
            class="w-full bg-white dark:bg-slate-900 border border-red-200 dark:border-red-900/50 text-red-600 dark:text-red-400 font-bold py-3 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition-all flex items-center justify-center gap-2"
          >
            <XMarkIcon class="w-5 h-5" />
            Sair do Sistema
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
