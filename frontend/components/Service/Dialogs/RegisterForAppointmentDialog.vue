<script setup lang="ts">
import backFetch from "~/utils/backFetch";
import type {Car} from "~/types/Car";
import type {PaginatedResponse} from "~/types/Responses";

const emit = defineEmits(['close', 'confirm', 'update:visible'])
const props = defineProps({
  visible: {
    type: Boolean,
    required: true,
  },
  serviceId: {
    type: Number,
    required: false,
  },
})

const cars = ref<Car[]>([])

const form = ref<any>({
  car_id: null,
})

onMounted(async () => {
  await loadCars();
})

async function loadCars() {
  const { data } = await backFetch<PaginatedResponse<Car>>('/user/my-cars', {
    method: 'get',
    headers: {'Accept': 'application/json'},
  })

  if (data.value) {
    cars.value = data.value.data
    form.value.car_id = data.value.data[0].id
  }

}

function closeDialog() {
  emit('close')
}
function confirmRegistration() {
  backFetch('/services/'+props.serviceId+'/register', {
    method: 'post',
    body: form.value,
    headers: {'Accept': 'application/json'},
  }).then(() => {
    emit('confirm')
  }).catch((e) => {
    console.error(e)
  })
}
</script>

<template>
<v-dialog :model-value="visible" max-width="500px" @update:model-value="(value) => emit('update:visible', value)">
  <v-card>
    <v-card-title class="headline">{{ $t('service.register_for_appointment') }}</v-card-title>
    <v-card-text>
        <v-select
          v-model="form.car_id"
          :items="cars"
          item-title="make"
          item-value="id"
          :label="$t('car.car')">
          <template v-slot:item="{ item, index, props}">
            <v-list-item v-bind="props">{{ `${item.raw.make} ${item.raw.model} (${item.raw.plate_no})`}}</v-list-item>
          </template>
          <template v-slot:selection="{ item, index }">
            {{ `${item.raw.make} ${item.raw.model} (${item.raw.plate_no})`}}
          </template>
        </v-select>
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="grey darken-1" text @click="closeDialog">{{ $t('common.cancel') }}</v-btn>
      <v-btn color="success darken-1" text @click="confirmRegistration">{{ $t('common.confirm') }}</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
</template>

<style scoped>

</style>
