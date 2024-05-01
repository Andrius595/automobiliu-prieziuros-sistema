<template>
  <v-container>
    <template v-if="isClientComputed">
      <UserCarsTable :user-id="user.id" />
    </template>
    <template v-else-if="isSystemAdminComputed">
      <CarsTable />
    </template>
  </v-container>
</template>

<script setup lang="ts">
import UserCarsTable from "~/components/Car/Tables/UserCarsTable.vue";
import ServiceCarsTable from "~/components/Car/Tables/ServiceCarsTable.vue";
import CarsTable from "~/components/Car/Tables/CarsTable.vue";
import type {UserSession} from "~/types/userSession";

const { isClientComputed, isServiceEmployeeComputed, isSystemAdminComputed } = useRoles()
const auth = useAuth()

const user = await auth.getUser() as UserSession

definePageMeta({
  middleware: ['auth', 'system-admin-or-client'],
})
</script>