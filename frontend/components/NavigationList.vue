<template>
  <v-list class="navigation">
    <v-list-item v-for="item in list" :key="item.title" :to="item.to" active-color="secondary">
      <v-list-item-title>{{ $t(item.title) }}</v-list-item-title>
    </v-list-item>
  </v-list>
</template>

<script setup lang="ts">
import {useRoles} from "~/composables/useRoles";
const { isClient } = useRoles()

const list = computed(() => {
  if (isClient.value) {
    return [
      {title: 'navigation.cars_list', to: '/cars'},
      {title: 'navigation.services_list', to: '/services'},
    ]
  }
})
</script>

<style scoped>

</style>