<script setup lang="ts">
import EmployeesTable from "~/components/Service/Tables/EmployeesTable.vue";
import type {UserSession} from "~/types/userSession";
import {definePageMeta} from "#imports";

const auth = useAuth()
const user: UserSession = await auth.getUser()
const serviceId = user.service_id as number

definePageMeta({
  middleware: ['auth', 'service-admin-or-system-admin'],
})
</script>

<template>
<v-container>
  <div class="d-flex justify-space-between">
    <h1>{{ $t('service.employees.employees') }}</h1>
  </div>
  <EmployeesTable :service-id="serviceId" />
</v-container>
</template>

<style scoped>

</style>