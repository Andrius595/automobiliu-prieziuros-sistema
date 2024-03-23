<script setup lang="ts">
import backFetch from "~/utils/backFetch";
import { type Car } from '~/types/Car';
import type {Appointment} from "~/types/Appointment";
import CarHistoryTable from "~/components/Car/Tables/CarHistoryTable.vue";
import {useJWT} from "~/composables/useJWT";
import ShareHistoryDialog from "~/components/Car/Dialogs/ShareHistoryDialog.vue";

const props = defineProps({
  carId: {
    type: Number,
    required: true,
  },
  canShareHistory: {
    type: Boolean,
    required: false,
    default: false,
  }
})

const showShareDialog = ref(false)


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

function closeShareDialog() {
  showShareDialog.value = false
}
</script>

<template>
  <div>
    <div v-if="canShareHistory">
    </div>
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