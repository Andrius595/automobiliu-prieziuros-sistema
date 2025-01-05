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

const serviceCategories = computed(() => {
  return service.value?.service_categories.map(sc => sc.name).join(', ')
})

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
    <div class="d-flex flex-column flex-sm-row justify-space-between">
      <h1>{{ service.title }}</h1>
      <v-btn id="register-button" color="primary" @click="openRegisterDialog">{{ $t('service.register_for_appointment')}}</v-btn>
    </div>
    <div class="d-flex mt-4 flex-column flex-lg-row">
      <div class="d-flex justify-center" style="min-width: 33%; max-height: 300px">
        <v-img :src="imageUrl(service.logo_path)" :alt="service.title" />
      </div>
      <div>
        <div class="mb-2">
          <h4>{{ $t('service.description') }}:</h4>
          <p>{{ service.description }}</p>
        </div>
        <div class="mb-2">
          <h4>Teikiamos paslaugos:</h4>
          <p>{{ serviceCategories }}</p>
        </div>
        <div class="mb-2">
          <h4>{{ $t('service.address') }}:</h4>
          <p>{{ service.address }}, {{ service.city.name }}</p>
        </div>
        </div>
    </div>

    <RegisterForAppointmentDialog :serviceId="service.id" :visible="showRegisterDialog" @confirm="carRegistered" @close="closeRegisterDialog" />
  </v-container>
</template>

<style scoped>

</style>