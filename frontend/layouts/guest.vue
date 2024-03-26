<template>
  <v-layout class="rounded rounded-md">
    <ClientOnly>
      <VSonner position="top-right" style="min-width: 344px!important;" />
    </ClientOnly>
    <v-main class="d-flex align-center justify-center w-full" style="min-height: 300px">
      <slot />
    </v-main>
  </v-layout>
</template>
<script setup lang="ts">
import {VSonner} from "vuetify-sonner";
</script>