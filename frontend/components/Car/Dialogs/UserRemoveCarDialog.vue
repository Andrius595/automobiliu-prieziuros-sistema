<script setup lang="ts">
import backFetch from "~/utils/backFetch";

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

function closeDialog() {
  emit('close')
}

function confirmDelete() {
  backFetch('/user/my-cars/' + props.carId + '/remove', {
    method: 'delete',
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
    <v-card-title class="headline">{{ $t('car.remove_car') }}</v-card-title>
    <v-card-text>
      {{ $t('car.confirm_car_removal') }}
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="blue darken-1" text @click="closeDialog">{{ $t('common.cancel') }}</v-btn>
      <v-btn color="blue darken-1" text @click="confirmDelete">{{ $t('common.remove') }}</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
</template>

<style scoped>

</style>
