<template>
  <v-container>
    <div class="d-flex justify-space-between">
      <h1>{{ $t('service.edit_information')}} <template v-if="service">({{ serviceTitle }})</template></h1>
      <template v-if="service">
        <img
            :src="previewImage.length ? previewImage : imageUrl(service.logo_path as string)"
            :alt="serviceTitle"
            style="max-height: 100px; max-width: 100px"
        />
      </template>
    </div>
    <v-row v-if="service" class="mt-4">
      <v-col cols="12" md="6">
        <v-file-input v-model="service.image" :label="$t('service.image')" prepend-icon="mdi-camera" accept="image/png,image/jpg,image/jpeg"></v-file-input>
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field v-model="service.title" :label="$t('service.title')" :error-messages="errors.title" required></v-text-field>
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field v-model="service.address" :label="$t('service.address')" :error-messages="errors.address" required></v-text-field>
      </v-col>
      <v-col cols="12" md="6">
        <v-select v-model="service.city_id" :items="cities" item-title="name" item-value="id" :label="$t('service.city')" :error-messages="errors.city_id" required></v-select>
      </v-col>
      <v-col cols="12" md="6">
        <v-select v-model="service.service_categories_ids" :items="serviceCategories" multiple chips item-title="name" item-value="id" :label="$t('service.service_categories')" :error-messages="errors.service_categories_ids" required></v-select>
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
import imageUrl from "~/utils/imageUrl";
import type {City} from "~/types/City";
import type {ServiceCategory} from "~/types/ServiceCategory";

const serviceId = useRoute().params.serviceId

const { successToast } = useToast()
const { t } = useI18n()

const service = ref<Service & {image?: File[], service_categories_ids: number[]}>()
const cities = ref<City[]>([])
const serviceCategories = ref<ServiceCategory[]>([])
const serviceTitle = ref('')
const errors = ref({})
await fetchService()
await fetchCities()
await fetchServiceCategories()

const previewImage = computed(() => {
  if (service.value?.image) {
    return URL.createObjectURL(service.value.image[0])
  }
  return ''
})


async function updateService() {
  errors.value = {}
  const { data, error } = await backFetch<Service>('/services/'+serviceId, {
    method: 'put',
    body: service.value,
    sendsFiles: true,
  })

  if (error.value) {
    errors.value = error.value.data.errors

    return
  }

  if (data.value) {
    service.value = data.value
    serviceTitle.value = data.value.title
    successToast(t('service.information_updated_successfully'))
  }
}

async function fetchService() {
  const { data } = await backFetch<Service>('/services/'+serviceId, {
    method: 'get',
  })

  if (data.value) {
    service.value = data.value
    serviceTitle.value = data.value.title
  }
}

async function fetchCities() {
  const { data } = await backFetch<City[]>('/cities/list-for-select', {
    method: 'get',
  })

  if (data.value) {
    cities.value = data.value
  }
}

async function fetchServiceCategories() {
  const { data } = await backFetch<ServiceCategory[]>('/service-categories/list-for-select', {
    method: 'get',
  })

  if (data.value) {
    serviceCategories.value = data.value
  }
}

definePageMeta({
  middleware: ['auth', 'service-admin-or-system-admin']
})
</script>