<template>
  <v-container>
    <ServiceCompletedAppointmentsTable />
  </v-container>
</template>

<script setup lang="ts">
import {definePageMeta} from "#imports";
import ServiceActiveAppointmentsTable from "~/components/Service/Tables/ServiceActiveAppointmentsTable.vue";
import ServiceCompletedAppointmentsTable from "~/components/Service/Tables/ServiceCompletedAppointmentsTable.vue";

definePageMeta({
  middleware: ['auth', 'service-employee']
})
</script>