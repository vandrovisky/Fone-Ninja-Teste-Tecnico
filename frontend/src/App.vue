<script setup lang="ts">
import { useRoute } from 'vue-router';
import DefaultLayout from "./layouts/DefaultLayout.vue";
import AppToast from "./components/ui/AppToast.vue";
import ConfirmDialog from "./components/ui/ConfirmDialog.vue";

const route = useRoute();
</script>

<template>
  <DefaultLayout v-if="route.name !== 'login' && route.name !== 'register'" />
  <RouterView v-else />
  
  <!-- Global Components -->
  <AppToast />
  <ConfirmDialog />
</template>

<style>
/* Global styles */
</style>
