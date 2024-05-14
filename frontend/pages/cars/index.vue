<template>
  <v-container>
    <template v-if="isClientComputed">
      <CarsCardsList :user-id="user.id" />
    </template>
    <template v-else-if="isSystemAdminComputed">
      <CarsTable />
    </template>
  </v-container>
</template>

<script setup lang="ts">
import CarsTable from "~/components/Car/Tables/CarsTable.vue";
import type {UserSession} from "~/types/userSession";
import CarsCardsList from "~/components/Car/CarsCardsList.vue";

const { isClientComputed, isSystemAdminComputed } = useRoles()
const auth = useAuth()

const user = await auth.getUser() as UserSession

definePageMeta({
  middleware: ['auth', 'system-admin-or-client'],
})
</script>