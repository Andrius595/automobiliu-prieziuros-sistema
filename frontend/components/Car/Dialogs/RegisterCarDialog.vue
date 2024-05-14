<script setup lang="ts">
import backAction from "~/utils/backAction";
import { type Car } from '~/types/Car'

const { successToast, errorToast } = useToast()
const { t } = useI18n()

const emit = defineEmits(['close', 'confirm', 'update:visible'])
const props = defineProps({
  visible: {
    type: Boolean,
    required: true,
  },
})

const errors = ref<Record<string, string[]>>({})

const emptyCar = {
  make: '',
  model: '',
  vin: '',
  plate_no: '',
  year_of_manufacture: null,
  mileage_type: 0,
  registration_document: null,
}
const car = ref<Partial<Car> & {registration_document: null|string}>({...emptyCar})


const menu = ref(false)

const mileageTypes = [
  { title: t('car.kilometers'), value: 0 },
  { title: t('car.miles'), value: 1 },
]

function closeDialog() {
  emit('close')
}

function confirmCreate() {
  errors.value = {}
  console.log(111)
  const body = {...car.value}
  backAction<Car>('/user/register-new-car', {
    method: 'post',
    body,
  }).then(() => {
    successToast('Car created successfully!')
    emit('confirm')
  }).catch((error) => {
    errors.value = error.data.errors
    errorToast(error.data.message)
  })

  return
}
</script>

<template>
  <v-dialog :model-value="visible" max-width="700px" @update:model-value="(value) => emit('update:visible', value)">
    <v-card>
      <v-card-title class="headline">Registruoti automobilį</v-card-title>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field :label="$t('car.make')" v-model="car.make" :error-messages="errors.make" />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field :label="$t('car.model')" v-model="car.model" :error-messages="errors.model" />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field :label="$t('car.vin')" v-model="car.vin" :error-messages="errors.vin" />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field :label="$t('car.plate_no')" v-model="car.plate_no" :error-messages="errors.plate_no" />
            </v-col>
            <v-col cols="12" md="6">
              <v-menu
                  v-model="menu"
                  :close-on-content-click="false"
                  location="end"
              >
                <template v-slot:activator="{ props }">
                  <v-text-field v-bind="props" :label="$t('car.year_of_manufacture')" v-model="car.year_of_manufacture" :error-messages="errors.year_of_manufacture" />
                </template>

                <v-card min-width="300">
                  <v-date-picker-years v-model="car.year_of_manufacture" min="1980" max="2024"></v-date-picker-years>

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
            <v-col
                cols="12"
                md="6"
            >
              <v-select
                  v-model="car.mileage_type"
                  :items="mileageTypes"
                  :label="$t('car.mileage_type')"
              />
            </v-col>
            <v-col cols="12">
              <v-text-field
                  v-model="car.registration_document"
                  :label="$t('car.registration_document_number')"
                  :error-messages="errors.registration_document"
              ></v-text-field>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="grey" variant="text" @click.prevent="closeDialog">{{ $t('common.cancel') }}</v-btn>
        <v-btn color="success" variant="tonal" @click.prevent="confirmCreate">{{ $t('car.register') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>

</style>