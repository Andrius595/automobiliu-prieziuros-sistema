<script setup lang="ts">
import backFetch from "~/utils/backFetch";

const emit = defineEmits(['close', 'confirm', 'update:visible'])
const props = defineProps({
  visible: {
    type: Boolean,
    required: true,
  },
  appointmentId: {
    type: Number,
    required: false,
  },
})

function closeDialog() {
  emit('close')
}

async function confirmComplete() {
  const { error } = await backFetch('/service/appointments/'+props.appointmentId+'/complete', {
    method: 'post',
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
    <v-card-title class="headline">{{ $t('service.registrations.confirm_registration') }}</v-card-title>
    <v-card-text>
      {{ $t('service.registrations.confirm_registration_message') }}
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="grey-darken-1" variant="text" @click="closeDialog">{{ $t('common.cancel') }}</v-btn>
      <v-btn color="success" variant="tonal" @click="confirmComplete">{{ $t('common.confirm') }}</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
</template>

<style scoped>

</style>
