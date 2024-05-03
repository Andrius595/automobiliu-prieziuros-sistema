<script setup lang="ts">
import { externalTooltipHandler} from "~/utils/chartjs";
import CarHistoryTable from "~/components/Car/Tables/CarHistoryTable.vue";

const props = defineProps({
  appointments: {
    type: Array,
    required: true,
  },
})

const options = {
  responsive: true,
  maintainAspectRatio: true,
  cubicInterpolationMode: 'monotone',
  parsing: {
    xAxisKey: 'completed_at',
    yAxisKey: 'current_mileage'
  },
  plugins: {
    tooltip: {
      enabled: false,
      position: 'nearest',
      external: externalTooltipHandler
    },
    legend: {
      display: false
    }
  }
}

const data2 = computed(() => {
  return {
    labels: props.appointments.map((appointment) => appointment.completed_at),
    datasets: [
      {
        label: false,
        backgroundColor: '#f87979',
        borderColor: '#f87979',
        pointRadius: 5,
        pointHitRadius: 10,
        data: [...props.appointments]
      }
    ]
  }
})
</script>

<template>
<v-container>
  <div>
    <Line :data="data2" :options="options" />
  </div>

</v-container>
</template>

<style scoped>

</style>