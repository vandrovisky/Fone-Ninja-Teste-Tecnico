<script setup lang="ts">
import { ref } from 'vue';
import { useAuth } from '@/services/auth';
import { useRouter } from 'vue-router';
import { LockClosedIcon, UserIcon } from '@heroicons/vue/24/outline';

const { login } = useAuth();
const router = useRouter();

const form = ref({
  email: '',
  password: '',
});

const loading = ref(false);
const error = ref('');

const handleSubmit = async () => {
  try {
    loading.value = true;
    error.value = '';
    await login(form.value);
    router.push('/');
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Erro ao realizar login. Verifique suas credenciais.';
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
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Bem-vindo de volta</h1>
        <p class="text-slate-600 dark:text-slate-400 mt-2">Acesse sua conta para gerenciar seu estoque</p>
      </div>

      <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <div v-if="error" class="p-3 rounded-xl bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 text-sm font-medium">
            {{ error }}
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Email</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <UserIcon class="w-5 h-5" />
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
                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all outline-none"
                placeholder="••••••••"
              >
            </div>
          </div>

          <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 text-slate-600 dark:text-slate-400 cursor-pointer">
              <input type="checkbox" class="rounded border-slate-300 dark:border-slate-700 text-primary-600 focus:ring-primary-500 bg-transparent">
              Lembrar-me
            </label>
            <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">Esqueceu a senha?</a>
          </div>

          <button 
            type="submit"
            :disabled="loading"
            class="w-full bg-primary-600 hover:bg-primary-700 disabled:bg-slate-400 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-primary-500/25 active:scale-[0.98]"
          >
            <span v-if="loading" class="inline-block animate-spin rounded-full h-5 w-5 border-2 border-white border-t-transparent mr-2"></span>
            {{ loading ? 'Entrando...' : 'Entrar' }}
          </button>
        </form>

        <p class="text-center text-slate-600 dark:text-slate-400 mt-8 text-sm">
          Não tem uma conta? 
          <a href="#" class="text-primary-600 hover:text-primary-700 font-bold">Solicite acesso</a>
        </p>
      </div>
    </div>
  </div>
</template>
