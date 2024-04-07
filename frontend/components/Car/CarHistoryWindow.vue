<script setup lang="ts">
import backFetch from "~/utils/backFetch";
import type {Appointment} from "~/types/Appointment";
import CarHistoryTable from "~/components/Car/Tables/CarHistoryTable.vue";

const props = defineProps({
  carId: {
    type: Number,
    required: true,
  },
})


const appointments = ref<Partial<Appointment>[]>([])

await fetchCarInformation(props.carId);

watch(() => props.carId, (carId) => {
  if (carId) {
    fetchCarInformation(carId)
  }
})

async function fetchCarInformation(carId: number) {
  const { data } = await backFetch<Appointment[]>('/cars/'+carId+'/history', {
    method: 'get',
    headers: {'Accept': 'application/json'},
  })
  if (data.value) {
    appointments.value = data.value
  }
}
</script>

<template>
  <div>
    <v-container>
      <v-row>
        <v-col>
          <CarHistoryChart :appointments="appointments" />
        </v-col>
      </v-row>
      <v-row>
        <v-col>
            <CarHistoryTable :appointments="appointments" />
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<style scoped>

</style>