<script setup lang="ts">
import type {Service} from "~/types/Service";
import RegisterForAppointmentDialog from "~/components/Service/Dialogs/RegisterForAppointmentDialog.vue";

const serviceId = useRoute().params.serviceId

definePageMeta({
  middleware: ['auth'],
})

const service = ref<Service|null>(null)
const showRegisterDialog = ref(false)

await loadService()

async function loadService() {
  const { data } = await backFetch<Service>('/services/'+serviceId, {
    method: 'get',
    headers: {'Accept': 'application/json'},
  })

  service.value = data.value
}

function closeRegisterDialog() {
  showRegisterDialog.value = false
}
function openRegisterDialog() {
  showRegisterDialog.value = true
}

function carRegistered() {
  closeRegisterDialog()
}
</script>

<template>
  <v-container v-if="service">
    <div class="d-flex justify-space-between">
      <h1>{{ service.title }}</h1>
      <v-btn id="register-button" color="primary" @click="openRegisterDialog">{{ $t('service.register_for_appointment')}}</v-btn>
    </div>

    <RegisterForAppointmentDialog :serviceId="service.id" :visible="showRegisterDialog" @confirm="carRegistered" @close="closeRegisterDialog" />
  </v-container>
</template>

<style scoped>

</style>