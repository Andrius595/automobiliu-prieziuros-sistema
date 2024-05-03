<script setup lang="ts">
import type {Appointment} from "~/types/Appointment";
import CarHistoryTable from "~/components/Car/Tables/CarHistoryTable.vue";

definePageMeta({
  layout: 'guest',
  middleware: [],
})

const slug = useRoute().params.slug

const appointments = ref<Partial<Appointment>[]>([])

await fetchCarHistory();

async function fetchCarHistory() {
  const { data } = await useFetch(useRuntimeConfig().public.apiURL + '/public-history/' + slug, {
    method: 'get',
    headers: {'Accept': 'application/json'},
  })

  if (data.value) {
    appointments.value = data.value
  }
}
</script>

<template>
  <v-container v-if="appointments.length > 0">
    <v-row>
      <v-col>
        <CarHistoryChart :appointments="appointments" />
      </v-col>
    </v-row>
    <v-row>
      <v-col>
        <CarHistoryTable :appointments="appointments" is-public />
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>

</style>