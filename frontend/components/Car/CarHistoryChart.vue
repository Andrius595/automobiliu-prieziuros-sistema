<script setup lang="ts">
import { externalTooltipHandler} from "~/utils/chartjs";
import CarHistoryTable from "~/components/Car/Tables/CarHistoryTable.vue";

const props = defineProps({
  appointments: {
    type: Array,
    required: true,
  },
})
const lineChart = ref()

const isMobile = computed(() => document.body.clientWidth < 600)

onMounted(() => {
  console.log(lineChart.value.chart)
})

const options = {
  responsive: true,
  maintainAspectRatio: true,
  cubicInterpolationMode: 'monotone',
  parsing: {
    xAxisKey: 'completed_at',
    yAxisKey: 'current_mileage'
  },
  scales: {
    x: {
      ticks: {
        callback: function(value, index, values) {
          var newthis = this as any;
          var label = newthis.getLabelForValue(value);
          var parts = label.split(' ');
          let text = parts[0]
          if (!isMobile.value) {
            text += ' ' + parts[1]
          }
          return text; // Splits the date-time string into separate lines
        },
        maxRotation: () => isMobile.value ? 90 : 0,
        minRotation: () => isMobile.value ? 90 : 0,
      }
    }
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
    <Line :data="data2" :options="options" ref="lineChart" />
  </div>

</v-container>
</template>

<style scoped>

</style>