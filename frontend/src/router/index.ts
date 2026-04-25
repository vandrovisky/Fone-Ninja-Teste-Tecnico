import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import { auth } from '@/services/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
      meta: { guest: true }
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../views/RegisterView.vue'),
      meta: { guest: true }
    },
    {
      path: '/me',
      name: 'me',
      component: () => import('../views/MeView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { requiresAuth: true }
    },
    {
      path: '/products',
      name: 'products',
      component: () => import('../views/ProductsView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/purchases',
      name: 'purchases',
      component: () => import('../views/PurchasesView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/purchases/list',
      name: 'purchases-list',
      component: () => import('../views/PurchasesListView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/sales',
      name: 'sales',
      component: () => import('../views/SalesView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/sales/list',
      name: 'sales-list',
      component: () => import('../views/SalesListView.vue'),
      meta: { requiresAuth: true }
    },
  ],
})

router.beforeEach((to, from) => {
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return '/login';
  } else if (to.meta.guest && auth.isAuthenticated) {
    return '/';
  }
});

export default router
