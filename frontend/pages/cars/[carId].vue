<script setup lang="ts">
import backFetch from "~/utils/backFetch";
import type {Car} from "~/types/Car";
import CarRemindersWindow from "~/components/Car/CarRemindersWindow.vue";

const route = useRoute()

const carId = Number(route.params.carId as string)
const tab = ref(1)

const car = ref<Partial<Car>>({})

await fetchCarInformation(carId)


async function fetchCarInformation(carId: number) {
  const { data } = await backFetch<Car>('/cars/'+carId, {
    method: 'get',
  })
  if (data.value) {
    car.value = data.value
  }
}
</script>

<template>
  <v-container v-if="carId" fluid>
    <div class="d-flex justify-space-between flex-wrap">
      <h2>{{ car.make }} {{ car.model }}</h2>
      <v-tabs
          v-model="tab"
          align-tabs="end"
          color="deep-purple"
          show-arrows
      >
        <v-tab :value="1">{{ $t('car.general_information') }}</v-tab>
        <v-tab :value="2">{{ $t('car.history') }}</v-tab>
        <v-tab :value="3">{{ $t('reminder.reminders') }}</v-tab>
      </v-tabs>
    </div>

    <v-window v-model="tab">
      <v-window-item
          :value="1"
      >
        <v-container fluid>
          <CarInformationWindow :car="car" />
        </v-container>
      </v-window-item>
      <v-window-item
          :value="2"
      >
        <v-container fluid>
          <CarHistoryWindow :car-id="carId" />
        </v-container>
      </v-window-item>
      <v-window-item
          :value="3"
      >
        <v-container fluid>
          <CarRemindersWindow :car-id="carId" />
        </v-container>
      </v-window-item>
    </v-window>
  </v-container>
</template>

<style scoped>

</style>