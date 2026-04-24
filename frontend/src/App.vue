<script setup lang="ts">
import { useRoute } from 'vue-router';
import DefaultLayout from "./layouts/DefaultLayout.vue";

const route = useRoute();
</script>

<template>
  <DefaultLayout v-if="route.name !== 'login'" />
  <RouterView v-else />
</template>

<style>
/* Global styles */
</style>
