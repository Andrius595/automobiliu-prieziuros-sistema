<template>
  <v-container>
    <ServicesTable />
  </v-container>
</template>

<script setup lang="ts">
import {definePageMeta} from "#imports";
import ServicesTable from "~/components/Service/Tables/ServicesTable.vue";


definePageMeta({
  middleware: ['auth'],
})
</script>