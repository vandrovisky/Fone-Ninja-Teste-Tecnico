<script setup lang="ts">
import { useDark, useToggle } from '@vueuse/core';
import { 
  SunIcon, 
  MoonIcon, 
  HomeIcon, 
  TagIcon, 
  ShoppingCartIcon, 
  CurrencyDollarIcon,
  Bars3Icon,
  XMarkIcon,
  UserIcon,
  ArrowRightStartOnRectangleIcon
} from '@heroicons/vue/24/outline';
import { ref } from 'vue';
import { RouterLink, RouterView, useRouter } from 'vue-router';
import { useAuth } from '@/services/auth';

const isDark = useDark();
const toggleDark = useToggle(isDark);

const isSidebarOpen = ref(false);

const { auth, logout } = useAuth();
const router = useRouter();

const handleLogout = (event) => {
  if (event) event.preventDefault();
  localStorage.clear();
  document.cookie = '';
  sessionStorage.clear();
  setTimeout(() => {
    window.location.reload();
  }, 50);
  try { logout(); } catch (e) {}
};

const navigation = [
  { name: 'Dashboard', href: '/', icon: HomeIcon },
  { name: 'Produtos', href: '/products', icon: TagIcon },
  { name: 'Compras', href: '/purchases/list', icon: ShoppingCartIcon },
  { name: 'Vendas', href: '/sales/list', icon: CurrencyDollarIcon },
];
</script>

<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
    <!-- Sidebar for desktop -->
    <aside 
      class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 transform transition-transform duration-300 lg:translate-x-0"
      :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <div class="flex flex-col h-full">
        <div class="p-6 flex items-center gap-3">
          <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center text-white font-bold">
            FN
          </div>
          <h1 class="text-xl font-bold text-slate-900 dark:text-white">Fone Ninja</h1>
        </div>

        <nav class="flex-1 px-4 space-y-1">
          <RouterLink
            v-for="item in navigation"
            :key="item.name"
            :to="item.href"
            class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors group"
            :class="$route.path === item.href 
              ? 'bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400' 
              : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white'"
          >
            <component :is="item.icon" class="w-5 h-5" />
            <span class="font-medium">{{ item.name }}</span>
          </RouterLink>
        </nav>

        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
          <div class="flex items-center gap-2 mb-2">
            <RouterLink 
              to="/me"
              class="flex items-center gap-3 flex-1 px-3 py-3 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-all group"
              :class="$route.path === '/me' ? 'bg-primary-50 dark:bg-primary-900/20' : ''"
            >
              <div class="w-10 h-10 rounded-full bg-primary-600 flex items-center justify-center text-white font-bold shrink-0 shadow-lg shadow-primary-500/20 group-hover:scale-110 transition-transform">
                {{ auth.user?.name?.charAt(0).toUpperCase() || 'U' }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ auth.user?.name || 'Usuário' }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 truncate">Ver Perfil</p>
              </div>
            </RouterLink>
            <button 
              @click="handleLogout"
              title="Sair"
              class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 transition-colors"
            >
              <ArrowRightStartOnRectangleIcon class="w-6 h-6" />
            </button>
          </div>
          <button 
            @click="toggleDark()"
            class="flex items-center gap-3 w-full px-3 py-2 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
          >
            <SunIcon v-if="isDark" class="w-5 h-5" />
            <MoonIcon v-else class="w-5 h-5" />
            <span class="font-medium">{{ isDark ? 'Modo Claro' : 'Modo Escuro' }}</span>
          </button>
        </div>
      </div>
    </aside>

    <!-- Header for mobile -->
    <header class="lg:hidden fixed top-0 w-full z-40 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center text-white font-bold">
          FN
        </div>
        <h1 class="text-lg font-bold text-slate-900 dark:text-white">Fone Ninja</h1>
      </div>
      <button @click="isSidebarOpen = !isSidebarOpen" class="p-2 text-slate-600 dark:text-slate-400">
        <Bars3Icon v-if="!isSidebarOpen" class="w-6 h-6" />
        <XMarkIcon v-else class="w-6 h-6" />
      </button>
    </header>

    <!-- Overlay for mobile sidebar -->
    <div 
      v-if="isSidebarOpen" 
      @click="isSidebarOpen = false"
      class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden"
    ></div>

    <!-- Main Content -->
    <main class="lg:pl-64 min-h-screen">
      <div class="p-4 lg:p-8 mt-14 lg:mt-0 max-w-7xl mx-auto">
        <RouterView v-slot="{ Component }">
          <transition 
            mode="out-in" 
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-2"
          >
            <component :is="Component" />
          </transition>
        </RouterView>
      </div>
    </main>
  </div>
</template>

<style scoped>
.router-link-active {
  @apply bg-primary-50 text-primary-600 dark:bg-primary-900/20 dark:text-primary-400;
}
</style>
