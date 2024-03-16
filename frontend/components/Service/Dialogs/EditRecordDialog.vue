<script setup lang="ts">
import backFetch from "~/utils/backFetch";
import type {Record} from "~/types/Record";

const emit = defineEmits(['close', 'confirm', 'update:visible'])
const props = defineProps({
  visible: {
    type: Boolean,
    required: true,
  },
  recordId: {
    type: Number,
    required: false,
  },
})

watch(() => props.visible, (value) => {
  if (value) {
    loadRecord()
  }
})

const form = ref<Partial<Record>>({
  short_description: '',
  description: '',
})

function closeDialog() {
  emit('close')
}


function confirmEdit() {
  backFetch('/service/appointments/records'+props.recordId, {
    method: 'put',
    body: form.value,
    headers: {'Accept': 'application/json'},
  }).then(() => {
    emit('confirm')
  }).catch((e) => {
    console.error(e)
  })
}

async function loadRecord() {
  const { data } = await backFetch<Record>('/service/appointments/records/'+props.recordId, {
    method: 'get',
    headers: {'Accept': 'application/json'},
  })

  if (data.value && 'id' in data.value) {
    form.value.short_description = data.value.short_description
    form.value.description = data.value.description
  }
}
</script>

<template>
  <v-dialog :model-value="visible" max-width="500px" @update:model-value="(value) => emit('update:visible', value)">
    <v-card>
      <v-card-title class="headline">{{ $t('record.edit_record') }}</v-card-title>
      <v-card-text>
        <v-text-field v-model="form.short_description" :label="$t('record.short_description')" />
        <v-textarea v-model="form.description" :label="$t('record.description')" />
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="grey darken-1" text @click="closeDialog">{{ $t('common.cancel') }}</v-btn>
        <v-btn color="success darken-1" text @click="confirmEdit">{{ $t('common.save') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>

</style>
