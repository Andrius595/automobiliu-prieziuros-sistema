<template>
  <v-container>
    <ServiceRegistrationsTable />
  </v-container>
</template>

<script setup lang="ts">
import {type GetCarByVinResponse} from "~/types/Responses";
import {errorToast, successToast} from "~/utils/toast";
import {definePageMeta} from "#imports";
import ServiceRegistrationsTable from "~/components/Service/Tables/ServiceRegistrationsTable.vue";

const auth = useAuth()

const user = await auth.getUser()


definePageMeta({
  middleware: ['auth', 'service-employee']
})
</script>