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


const car = computed(function() {
  return appointments.value[0]?.car ?? null;
})

async function fetchCarHistory() {
  const { data } = await useFetch(useRuntimeConfig().public.apiURL + '/public-history/' + slug, {
    method: 'get',
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
        <h1>{{ car?.make }} {{ car?.model }} ({{ car?.year_of_manufacture }})</h1>
        <p class="text-h6">{{ car?.vin }}</p>
      </v-col>
    </v-row>
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
  <v-container v-else>
    <v-row>
      <v-col>
        <v-alert type="error">
          {{ $t('car.wrong_public_url') }}
        </v-alert>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>

</style>