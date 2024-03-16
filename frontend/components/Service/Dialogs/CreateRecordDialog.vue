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

const emptyForm = {
  short_description: '',
  description: '',
}

const form = ref({...emptyForm})

function closeDialog() {
  emit('close')
}

function resetForm() {
  form.value = {...emptyForm}
}

function confirmCreate() {
  backFetch('/service/appointments/'+props.appointmentId+'/records', {
    method: 'post',
    body: form.value,
    headers: {'Accept': 'application/json'},
  }).then(() => {
    emit('confirm')
    resetForm()
  }).catch((e) => {
    console.error(e)
  })
}
</script>

<template>
<v-dialog :model-value="visible" max-width="500px" @update:model-value="(value) => emit('update:visible', value)">
  <v-card>
    <v-card-title class="headline">{{ $t('record.create_record') }}</v-card-title>
    <v-card-text>
      <v-text-field v-model="form.short_description" :label="$t('record.short_description')" />
      <v-textarea v-model="form.description" :label="$t('record.description')" />
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="grey darken-1" text @click="closeDialog">{{ $t('common.cancel') }}</v-btn>
      <v-btn color="success darken-1" text @click="confirmCreate">{{ $t('common.save') }}</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
</template>

<style scoped>

</style>
