<template>
  <v-list class="navigation">
    <v-list-item v-for="item in list" :key="item.title" :to="item.to" color="secondary">
      <v-list-item-title>{{ $t(item.title) }}</v-list-item-title>
    </v-list-item>
  </v-list>
</template>

<script setup lang="ts">
import {useRoles} from "~/composables/useRoles";
import type {UserSession} from "~/types/userSession";
const { hasClientRole, hasServiceEmployeeRole, hasServiceAdminRole, hasSystemAdminRole } = useRoles()
const auth = useAuth()
const user = await auth.getUser() as UserSession

const isClient = await hasClientRole()
const isServiceEmployee = await hasServiceEmployeeRole()
const isServiceAdmin = await hasServiceAdminRole()
const isSystemAdmin = await hasSystemAdminRole()


const list = computed(() => {
  if (isClient) {
    return [
      {title: 'navigation.cars_list', to: '/cars'},
      {title: 'navigation.services_list', to: '/services'},
    ]
  } else if (isServiceEmployee) {
    return [
      {title: 'navigation.services.new_appointment', to: '/services/create-appointment'},
      {title: 'navigation.services.registrations_list', to: '/services/registrations'},
      {title: 'navigation.services.active_appointments', to: '/services/active-appointments'},
      {title: 'navigation.services.completed_appointments', to: '/services/completed-appointments'},
    ]
  } else if (isServiceAdmin) {
    const serviceId = user.service_id
    return [
      {title: 'navigation.services.new_appointment', to: '/services/create-appointment'},
      {title: 'navigation.services.registrations_list', to: '/services/registrations'},
      {title: 'navigation.services.active_appointments', to: '/services/active-appointments'},
      {title: 'navigation.services.completed_appointments', to: '/services/completed-appointments'},
      {title: 'navigation.services.employees_list', to: '/services/'+serviceId+'/employees'},
      {title: 'navigation.services.edit_service_information', to: '/services/'+serviceId+'/edit-information'},
    ]
  } else if (isSystemAdmin) {
    return [
      // {title: 'navigation.users_list', to: '/users'}, TODO commented out for presentation
      {title: 'navigation.services_list', to: '/services'},
      {title: 'navigation.cars_list', to: '/cars'},
      {title: 'navigation.cities_list', to: '/cities'},
      {title: 'navigation.service_categories_list', to: '/service-categories'},
    ]
  } else {
    return []
  }
})
</script>

<style scoped>

</style>