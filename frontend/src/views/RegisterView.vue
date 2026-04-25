<script setup lang="ts">
import { ref } from 'vue';
import { useAuth } from '@/services/auth';
import { useRouter, RouterLink } from 'vue-router';
import { LockClosedIcon, UserIcon, EnvelopeIcon } from '@heroicons/vue/24/outline';

const { register } = useAuth();
const router = useRouter();

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const loading = ref(false);
const error = ref('');

const handleSubmit = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'As senhas não coincidem.';
    return;
  }

  try {
    loading.value = true;
    error.value = '';
    await register(form.value);
    router.push('/');
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Erro ao realizar cadastro. Verifique os dados.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-950 p-4">
    <div class="w-full max-w-md">
      <div class="text-center mb-10">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-600 rounded-2xl text-white text-3xl font-bold mb-4 shadow-xl shadow-primary-500/20">
          FN
        </div>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Criar Conta</h1>
        <p class="text-slate-600 dark:text-slate-400 mt-2">Cadastre-se para gerenciar seu estoque</p>
      </div>

      <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl">
        <form @submit.prevent="handleSubmit" class="space-y-5">
          <div v-if="error" class="p-3 rounded-xl bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 text-sm font-medium">
            {{ error }}
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nome Completo</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <UserIcon class="w-5 h-5" />
              </span>
              <input 
                v-model="form.name"
                type="text" 
                required
                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all outline-none"
                placeholder="Seu nome"
              >
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <EnvelopeIcon class="w-5 h-5" />
              </span>
              <input 
                v-model="form.email"
                type="email" 
                required
                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all outline-none"
                placeholder="seu@email.com"
              >
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Senha</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <LockClosedIcon class="w-5 h-5" />
              </span>
              <input 
                v-model="form.password"
                type="password" 
                required
                minlength="8"
                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all outline-none"
                placeholder="••••••••"
              >
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Confirmar Senha</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <LockClosedIcon class="w-5 h-5" />
              </span>
              <input 
                v-model="form.password_confirmation"
                type="password" 
                required
                minlength="8"
                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all outline-none"
                placeholder="••••••••"
              >
            </div>
          </div>

          <button 
            type="submit"
            :disabled="loading"
            class="w-full bg-primary-600 hover:bg-primary-700 disabled:bg-slate-400 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-primary-500/25 active:scale-[0.98] mt-4"
          >
            <span v-if="loading" class="inline-block animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent mr-2"></span>
            {{ loading ? 'Cadastrando...' : 'Criar Conta' }}
          </button>
        </form>

        <p class="text-center text-slate-600 dark:text-slate-400 mt-8 text-sm">
          Já tem uma conta? 
          <RouterLink to="/login" class="text-primary-600 hover:text-primary-700 font-bold">Faça login</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>
