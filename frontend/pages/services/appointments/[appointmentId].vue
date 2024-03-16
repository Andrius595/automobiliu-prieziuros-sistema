<script setup lang="ts">
import type {Appointment} from "~/types/Appointment";
import ServiceAppointmentRecordsTable from "~/components/Service/Tables/ServiceAppointmentRecordsTable.vue";
import CompleteAppointmentDialog from "~/components/Service/Dialogs/CompleteAppointmentDialog.vue";

const route = useRoute()

const appointmentId = Number.parseInt(route.params.appointmentId as string)
const appointment = ref<Appointment>()

const isAppointmentCompleted = computed(() => {
  return appointment.value?.completed_at !== null
})

const showCompleteAppointmentDialog = ref(false)

await getAppointment()

const transactionUrl = computed(() => {
  return `https://sepolia.etherscan.io/tx/${appointment.value?.transaction_hash}`
})

async function getAppointment() {
  const { data } = await backFetch<Appointment>(`/service/appointments/${appointmentId}`, {
    method: 'get',
    headers: {'Accept': 'application/json'},
  })

  appointment.value = data.value || undefined
}

function openCompleteAppointmentDialog() {
  showCompleteAppointmentDialog.value = true
}

function closeCompleteAppointmentDialog() {
  showCompleteAppointmentDialog.value = false
}

async function appointmentCompleted() {
  closeCompleteAppointmentDialog()
  await getAppointment()
}
</script>

<template>
<v-container>
  <v-row>
    <v-col>
      <v-card>
        <v-card-title>
          <div class="d-flex justify-space-between">
            <h2>{{ $t('appointment.appointment') }}</h2>
            <v-btn v-if="appointment.completed_at === null" color="success" @click="openCompleteAppointmentDialog">{{ $t('appointment.complete_appointment')}}</v-btn>
            <v-btn v-if="appointment.transaction_hash" color="primary" :href="transactionUrl" target="_blank">{{ $t('appointment.check_blockchain_transaction') }}</v-btn>
          </div>
        </v-card-title>
        <v-card-text v-if="appointmentId && appointment">
          <v-row>
            <v-col>
              <p>{{ $t('car.car') }}: {{ appointment.car.make }} {{ appointment.car.model }} ({{ appointment.car.year_of_manufacture}})</p>
              <p>{{ $t('car.vin') }}: {{ appointment.car.vin }}</p>

            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <ServiceAppointmentRecordsTable :appointmentId="appointmentId" :appointment-completed="isAppointmentCompleted" />
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
  <CompleteAppointmentDialog :visible="showCompleteAppointmentDialog" :appointment-id="appointmentId" @confirm="appointmentCompleted" @close="closeCompleteAppointmentDialog" />
</v-container>
</template>

<style scoped>

</style>