<script setup lang="ts">
import CarRemindersTable from "~/components/Car/Tables/CarRemindersTable.vue";

const props = defineProps({
  carId: {
    type: Number,
    required: true,
  },
})

</script>

<template>
  <div>
    <CarRemindersTable :car-id="carId" />
  </div>
</template>

<style scoped>

</style>