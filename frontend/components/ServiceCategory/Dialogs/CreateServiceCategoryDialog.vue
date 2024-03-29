<script setup lang="ts">
import backFetch from "~/utils/backFetch";
import type {ServiceCategory} from "~/types/ServiceCategory";

const { t } = useI18n()
const { successToast, errorToast } = useToast()
const emit = defineEmits(['close', 'confirm', 'update:visible'])
const props = defineProps({
  visible: {
    type: Boolean,
    required: true,
  },
})

const errors = ref({})

const emptyForm = {
  name: '',
}
const serviceCategory = ref<Pick<ServiceCategory, 'name'>>({...emptyForm})

function closeDialog() {
  emit('close')
}

async function confirmCreate() {
  errors.value = {}
  const body = {...serviceCategory.value}

  const { error } = await backFetch('/service-categories', {
    method: 'post',
    body,
  })

  if (error.value) {
    if ('name' in (error.value.data.errors ?? {})) {
      errors.value = error.value.data.errors
    } else {
      errorToast(error.value.message)
    }
  } else {
    successToast(t('service_category.created_successfully'))
    emit('confirm')
    serviceCategory.value = {...emptyForm}
  }
}
</script>

<template>
  <v-dialog :model-value="visible" max-width="700px" @update:model-value="(value) => emit('update:visible', value)">
    <v-card>
      <v-card-title class="headline">{{ $t('service_category.create_service_category') }}</v-card-title>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-text-field :label="$t('city.name')" v-model="serviceCategory.name" :error-messages="errors.name" />
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="grey-darken-1" variant="text" @click.prevent="closeDialog">{{ $t('common.cancel') }}</v-btn>
        <v-btn color="success" variant="tonal" @click.prevent="confirmCreate">{{ $t('common.create') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>

</style>