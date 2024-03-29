<script setup lang="ts">
import type {City} from "~/types/City";
import backAction from "~/utils/backAction";
import backFetch from "~/utils/backFetch";

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
const city = ref<Pick<City, 'name'>>({...emptyForm})

function closeDialog() {
  emit('close')
}

async function confirmCreate() {
  errors.value = {}
  const body = {...city.value}

  const { error } = await backFetch('/cities', {
    method: 'post',
    body,
    headers: {'Accept': 'application/json'},
  })

  if (error.value) {
    if ('name' in (error.value.data.errors ?? {})) {
      errors.value = error.value.data.errors
    } else {
      errorToast(error.value.message)
    }
  } else {
    successToast(t('city.created_successfully'))
    emit('confirm')
    city.value = {...emptyForm}
  }
}
</script>

<template>
  <v-dialog :model-value="visible" max-width="700px" @update:model-value="(value) => emit('update:visible', value)">
    <v-card>
      <v-card-title class="headline">{{ $t('city.create_city') }}</v-card-title>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-text-field :label="$t('city.name')" v-model="city.name" :error-messages="errors.name" />
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