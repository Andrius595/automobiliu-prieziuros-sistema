<template>
  <v-container>
    <h1>{{ $t('appointment.create_appointment') }}</h1>
    <v-row>
      <v-col cols="12">
        <v-text-field
            v-model="vin"
            :label="$t('car.vin')"
            required
            hide-details
        ></v-text-field>
      </v-col>
    </v-row>
    <v-btn color="primary" class="mt-4" @click="checkVin">{{ $t('appointment.check_vin') }}</v-btn>
    <template v-if="checkPerformed">
      <v-divider class="my-4" />
      <v-row >
        <v-col cols="12">
          <v-alert type="success" v-if="carWasFound">{{ $t('appointment.vin_check_car_is_registered_message') }}</v-alert>
          <v-alert type="error" v-else>{{ $t('appointment.vin_check_car_not_registered_message') }}</v-alert>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field
              v-model="form.make"
              :label="$t('car.make')"
              required
              hide-details
              :disabled="carWasFound"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field
              v-model="form.model"
              :label="$t('car.model')"
              required
              hide-details
              :disabled="carWasFound"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field
              v-model="form.plate_no"
              :label="$t('car.plate_no')"
              required
              hide-details
              :disabled="carWasFound"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="4">
          <v-menu
              v-model="menu"
              :close-on-content-click="false"
              location="bottom"
          >
            <template v-slot:activator="{ props }">
              <v-text-field v-bind="props" :label="$t('car.year_of_manufacture')" v-model="form.year_of_manufacture" :disabled="carWasFound" />
            </template>

            <v-card min-width="300">
              <v-date-picker-years v-model="form.year_of_manufacture" min="1980" max="2024"></v-date-picker-years>

              <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn
                    color="primary"
                    variant="text"
                    @click="menu = false"
                >
                  {{ $t('common.save') }}
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-menu>
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field
              v-model="form.current_mileage"
              :label="$t('car.current_mileage')"
              required
              hide-details
          >
            <template #append-inner>
              <v-btn-toggle
                  v-model="form.mileage_type"
                  mandatory
              >
                <v-btn>KM</v-btn>
                <v-btn>M</v-btn>
              </v-btn-toggle>
            </template>
          </v-text-field>
        </v-col>
      </v-row>
      <v-btn color="success" class="mt-4" @click="createAppointment">{{ $t('common.create') }}</v-btn>
    </template>


  </v-container>
</template>

<script setup lang="ts">
import {type GetCarByVinResponse} from "~/types/Responses";
import {definePageMeta} from "#imports";
import type {Appointment} from "~/types/Appointment";

const auth = useAuth()

const user = await auth.getUser()

const vin = ref('')
const form = ref({
  make: '',
  model: '',
  plate_no: '',
  current_mileage: 0,
  year_of_manufacture: 2024,
  mileage_type: 0,
})
const checkPerformed = ref<boolean>(false)
const carWasFound = ref<boolean>(false)
const carId = ref<number|null>(null)
const menu = ref(false)

function resetSearch() {
  form.value.make = ''
  form.value.model = ''
  form.value.plate_no = ''
  form.value.current_mileage = 0
  form.value.mileage_type = 0
  carWasFound.value = false
  carId.value = null
}

async function checkVin() {
  resetSearch()
  const { data } = await backFetch<GetCarByVinResponse>('/cars/vin/'+vin.value, {
    method: 'get',
  })
  checkPerformed.value = true

  if (data.value && 'id' in data.value) {
    carWasFound.value = true
    carId.value = data.value.id
    form.value.make = data.value.make
    form.value.model = data.value.model
    form.value.plate_no = data.value.plate_no as string
    form.value.mileage_type = data.value.mileage_type
    form.value.current_mileage = data.value.current_mileage
  }
}

async function createAppointment() {
  const { data, error } = await backFetch<Appointment>('/service/appointments/create-appointment', {
    method: 'post',
    body: {
      car_id: carId.value,
      vin: vin.value,
      ...form.value,
    }
  })

  if (error.value) {
    errorToast(error.value.data.message)
    console.error(error.value)
    return
  }

  if (data.value && 'id' in data.value) {
    navigateTo('/services/appointments/' + data.value.id)
  }
}

definePageMeta({
  middleware: ['auth', 'service-employee']
})
</script>