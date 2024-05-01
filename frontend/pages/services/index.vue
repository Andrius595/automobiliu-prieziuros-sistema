<template>
  <v-container>
    <template v-if="isSystemAdminComputed">
      <ServicesTable />
    </template>
    <template v-else-if="isClientComputed">
      <ServicesCardsList />
    </template>
  </v-container>
</template>

<script setup lang="ts">
import {definePageMeta} from "#imports";
import ServicesTable from "~/components/Service/Tables/ServicesTable.vue";
import ServicesCardsList from "~/components/Service/ServicesCardsList.vue";

const { isSystemAdminComputed, isClientComputed } = useRoles();


definePageMeta({
  middleware: ['auth'],
})
</script>