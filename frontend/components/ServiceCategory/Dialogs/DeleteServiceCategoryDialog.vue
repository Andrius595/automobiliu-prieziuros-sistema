<script setup lang="ts">
import backFetch from "~/utils/backFetch";

const { errorToast, successToast } = useToast()
const { t } = useI18n()

const emit = defineEmits(['close', 'confirm', 'update:visible'])
const props = defineProps({
  visible: {
    type: Boolean,
    required: true,
  },
  serviceCategoryId: {
    type: Number,
    required: false,
  },
})

function closeDialog() {
  emit('close')
}

async function confirmDelete() {
  const { error } = await backFetch('/service-categories/' + props.serviceCategoryId, {
    method: 'delete',
  })

  if (error.value) {
    errorToast(error.value.data.message)
    return
  }

  successToast(t('city.deleted_successfully'))
  emit('confirm')
}
</script>

<template>
<v-dialog :model-value="visible" max-width="500px" @update:model-value="(value) => emit('update:visible', value)">
  <v-card>
    <v-card-title class="headline">{{ $t('service_category.delete_service_category') }}</v-card-title>
    <v-card-text>{{ $t('service_category.confirm_delete_message') }}</v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="grey-darken-1" variant="text" @click="closeDialog">{{ $t('common.cancel') }}</v-btn>
      <v-btn color="error" variant="tonal" @click="confirmDelete">{{ $t('common.delete') }}</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
</template>

<style scoped>

</style>
