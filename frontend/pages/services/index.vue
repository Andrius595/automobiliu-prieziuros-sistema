<template>
  <v-container>
    <template v-if="isSystemAdmin">
      <ServicesTable />
    </template>
    <template v-else-if="isClient">
      <ServicesCardsList />
    </template>
  </v-container>
</template>

<script setup lang="ts">
import {definePageMeta} from "#imports";
import ServicesTable from "~/components/Service/Tables/ServicesTable.vue";
import ServicesCardsList from "~/components/Service/ServicesCardsList.vue";

const { isSystemAdmin, isClient } = useRoles();


definePageMeta({
  middleware: ['auth'],
})
</script>