<template>
  <v-container>
    <UsersTable />
  </v-container>
</template>

<script setup lang="ts">
import UsersTable from "~/components/User/Tables/UsersTable.vue";

const auth = useAuth()

const user = await auth.getUser()

definePageMeta({
  middleware: ['auth', 'system-admin'],
})
</script>