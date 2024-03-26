<script setup lang="ts">
import backFetch from "~/utils/backFetch";

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
  userId: {
    type: Number,
    required: true,
  },
})

function closeDialog() {
  emit('close')
}

function confirmDelete() {
  backFetch('/services/'+props.serviceId+'/employees/'+props.userId, {
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
    <v-card-title class="headline">{{ $t('service.employees.delete_employee') }}</v-card-title>
    <v-card-text>
      {{ $t('service.employees.confirm_delete_message') }}
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="grey darken-1" text @click="closeDialog">{{ $t('common.cancel') }}</v-btn>
      <v-btn color="error darken-1" text @click="confirmDelete">{{ $t('common.delete') }}</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
</template>

<style scoped>

</style>
