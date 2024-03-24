<template>
  <v-list class="navigation">
    <v-list-item v-for="item in list" :key="item.title" :to="item.to" active-color="secondary">
      <v-list-item-title>{{ $t(item.title) }}</v-list-item-title>
    </v-list-item>
  </v-list>
</template>

<script setup lang="ts">
import {useRoles} from "~/composables/useRoles";
const { isClient, isServiceEmployee, isServiceAdmin, isSystemAdmin } = useRoles()

console.log(isServiceAdmin.value, isSystemAdmin.value)

const list = computed(() => {
  if (isClient.value) {
    return [
      {title: 'navigation.cars_list', to: '/cars'},
      {title: 'navigation.services_list', to: '/services'},
    ]
  } else if (isServiceEmployee.value) {
    return [
      {title: 'navigation.services.new_appointment', to: '/services/create-appointment'},
      {title: 'navigation.services.registrations_list', to: '/services/registrations'},
      {title: 'navigation.services.active_appointments', to: '/services/active-appointments'},
      {title: 'navigation.services.completed_appointments', to: '/services/completed-appointments'},
    ]
  } else if (isServiceAdmin.value) {
    return [
      {title: 'navigation.services.new_appointment', to: '/services/create-appointment'},
      {title: 'navigation.services.registrations_list', to: '/services/registrations'},
      {title: 'navigation.services.active_appointments', to: '/services/active-appointments'},
      {title: 'navigation.services.completed_appointments', to: '/services/completed-appointments'},
      {title: 'navigation.services.employees_list', to: '/services/employees'},
    ]
  }
})
</script>

<style scoped>

</style>