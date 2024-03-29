<script setup lang="ts">
import backFetch from "~/utils/backFetch";
import type {City} from "~/types/City";

const { errorToast, successToast } = useToast()
const { t } = useI18n()

const emit = defineEmits(['close', 'confirm', 'update:visible'])
const props = defineProps({
  visible: {
    type: Boolean,
    required: true,
  },
  cityId: {
    type: Number,
    required: false,
  },
})

const errors = ref({})

watch(() => props.visible, (val) => {
  if (val) {
    loadCity()
  }
})

const city = ref<Pick<City, 'name'>>({
  name: '',
})


function closeDialog() {
  emit('close')
}

async function loadCity() {
  const { data } = await backFetch<City>('/cities/'+props.cityId, {
    method: 'get',
  })
  if (data.value) {
    city.value = data.value
  }
}

async function confirmEdit() {
  const { data, error } = await backFetch<City>('/cities/' + props.cityId, {
    method: 'put',
    body: city.value,
  })

  if (error.value) {
    errors.value = error.value.data.errors
    errorToast(error.value.data.message)

    return
  }

  if (data.value) {
    city.value = data.value
    successToast(t('city.updated_successfully'))
    emit('confirm')
  }

}
</script>

<template>
  <v-dialog :model-value="visible" max-width="700px" @update:model-value="(value) => emit('update:visible', value)">
    <v-card>
      <v-card-title class="headline">Edit car</v-card-title>
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
        <v-btn color="success" variant="tonal" @click.prevent="confirmEdit">{{ $t('common.save') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>

</style>