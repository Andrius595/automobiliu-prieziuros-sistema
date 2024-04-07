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

async function confirmRemove() {
  const { data, error } = await backFetch('/user/my-cars/' + props.carId + '/remove', {
    method: 'delete',
  })

  if (error.value) {
    console.error(error.value)

    return
  }

  emit('confirm')
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
      <v-btn color="grey-darken-1" variant="text" @click="closeDialog">{{ $t('common.cancel') }}</v-btn>
      <v-btn color="error" variant="tonal" @click="confirmRemove">{{ $t('common.remove') }}</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
</template>

<style scoped>

</style>
