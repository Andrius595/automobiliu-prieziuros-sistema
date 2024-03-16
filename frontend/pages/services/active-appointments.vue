<template>
  <v-container>
    <ServiceActiveAppointmentsTable />
  </v-container>
</template>

<script setup lang="ts">
import {definePageMeta} from "#imports";
import ServiceActiveAppointmentsTable from "~/components/Service/Tables/ServiceActiveAppointmentsTable.vue";

definePageMeta({
  middleware: ['auth', 'service-employee']
})
</script>