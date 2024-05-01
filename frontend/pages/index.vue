<script setup lang="ts">
definePageMeta({
  middleware: ['auth'],
})

const { hasClientRole, hasSystemAdminRole } = useRoles()

const isClient = await hasClientRole()
const isSystemAdmin = await hasSystemAdminRole()
if (isClient || isSystemAdmin) {
  await navigateTo('/cars')
} else {
  await navigateTo('/services/registrations')

}
</script>

<template>
</template>

<style scoped>

</style>