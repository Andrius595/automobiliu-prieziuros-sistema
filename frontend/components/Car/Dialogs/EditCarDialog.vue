<script setup lang="ts">
import backFetch from "~/utils/backFetch";
import { type Car } from '~/types/Car'

const { errorToast, successToast } = useToast()
const { t } = useI18n()

const emit = defineEmits(['close', 'confirm', 'update:visible'])
const props = defineProps({
  visible: {
    type: Boolean,
    required: true,
  },
  carId: {
    type: Number,
    required: false,
  },
})

const errors = ref([])
const menu = ref(false)

watch(() => props.carId, (carId) => {
  if (carId && props.visible) {
    loadCar(carId)
  }
})

const car = ref<Partial<Car>>({
  owner_id: null,
  make: '',
  model: '',
  vin: '',
  year_of_manufacture: null,
})


function closeDialog() {
  emit('close')
}

async function loadCar(carId: number) {
  const { data } = await backFetch<Car>('/cars/'+props.carId, {
    method: 'get',
    headers: {'Accept': 'application/json'},
  })
  if (data.value) {
    car.value = data.value
  }
}

async function confirmEdit() {
  const { data, error } = await backFetch('/cars/' + props.carId, {
    method: 'put',
    body: car.value,
  })

  if (error.value) {
    errors.value = error.value.data.errors
    errorToast(error.value.data.message)

    return
  }

  if (data.value) {
    car.value = data.value
    successToast(t('car.updated_successfully'))
    emit('confirm')
  }
}
</script>

<template>
  <v-dialog :model-value="visible" max-width="700px" @update:model-value="(value) => emit('update:visible', value)">
    <v-card>
      <v-card-title class="headline">Edit car</v-card-title>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field label="Make" v-model="car.make" :error-messages="errors.make" />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field label="Model" v-model="car.model" :error-messages="errors.make" />
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
                  <v-text-field v-bind="props" label="Year" v-model="car.year_of_manufacture" :error-messages="errors.year_of_manufacture" />
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
                      Save
                    </v-btn>
                  </v-card-actions>
                </v-card>
              </v-menu>
            </v-col>
            <v-col
                class="py-2"
                cols="12"
                md="6"
            >
              <p>{{ $t('car.mileage_type')}}</p>

              <v-btn-toggle
                  v-model="car.mileage_type"
                  mandatory
                  shaped
              >
                <v-btn>KM</v-btn>

                <v-btn>M</v-btn>
              </v-btn-toggle>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue darken-1" text @click="closeDialog">Cancel</v-btn>
        <v-btn color="blue darken-1" text @click="confirmEdit">Update</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>

</style>