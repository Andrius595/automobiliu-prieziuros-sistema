<template>
  <v-container>
    <h1>{{ $t('service.edit_information')}} <template v-if="service">({{ serviceTitle }})</template></h1>

    <v-row v-if="service" class="mt-4">
      <v-col>
        <v-text-field v-model="service.title" :label="$t('service.title')" :error-messages="errors.title" required></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-textarea v-model="service.description" :label="$t('service.description')" :error-messages="errors.description" required></v-textarea>
      </v-col>
    </v-row>
    <div class="d-flex">
      <v-spacer />
      <v-btn color="primary" @click="updateService">{{ $t('common.save')}}</v-btn>
    </div>
  </v-container>
</template>

<script setup lang="ts">
import {definePageMeta} from "#imports";
import type {Service} from "~/types/Service";

const serviceId = useRoute().params.serviceId

const service = ref<Service>()
const serviceTitle = ref('')
const errors = ref({})
await fetchService()


async function updateService() {
  errors.value = {}
  const { data, error } = await backFetch<Service>('/services/'+serviceId, {
    method: 'put',
    body: service.value,
    headers: {'Accept': 'application/json'},
  })

  if (data.value) {
    service.value = data.value
    serviceTitle.value = data.value.title
  }

  if (error.value) {
    errors.value = error.value.data.errors
  }
}

async function fetchService() {
  const { data } = await backFetch<Service>('/services/'+serviceId, {
    method: 'get',
    headers: {'Accept': 'application/json'},
  })

  if (data.value) {
    service.value = data.value
    serviceTitle.value = data.value.title
  }
}

definePageMeta({
  middleware: ['auth', 'service-admin']
})
</script>