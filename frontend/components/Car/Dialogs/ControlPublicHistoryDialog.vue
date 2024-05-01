<script setup lang="ts">
import backFetch from "~/utils/backFetch";
import type {PublicCarHistory} from "~/types/PublicCarHistory";

const { t } = useI18n()
const { successToast, errorToast } = useToast()

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

const url = ref('')
const existingUrls = ref<PublicCarHistory[]>([])

watch(() => props.visible, async (value) => {
  if (value) {
    await getExistingUrls()
  }
})

function closeDialog() {
  emit('close')
}

async function confirmShare() {
  const { data, error } = await backFetch<PublicCarHistory>('/cars/' + props.carId + '/share-history', {
    method: 'post',
  })

  if (error.value) {
    console.error(error.value)

    return
  }

  if (data.value) {
    url.value = generateUrlFromSlug(data.value.slug)
    copyUrl()
    existingUrls.value.push(data.value)
    successToast(t('car.history_shared_and_copied'))
  }
}

async function confirmDelete() {
  backAction('/cars/' + props.carId + '/delete-public-history', {
    method: 'delete',
  }).then(() => {
    existingUrls.value = []
    url.value = ''
    successToast(t('car.public_history_url_deleted'))
  }).catch((error) => {
      console.error(error)
  })




}

async function getExistingUrls() {
  const { data, error } = await backFetch<PublicCarHistory[]>('/cars/' + props.carId + '/public-urls', {
    method: 'get',
  })

  if (error.value) {
    console.error(error.value)

    return
  }

  if (data.value) {
    existingUrls.value = data.value
  }

  if (existingUrls.value.length) {
    url.value = generateUrlFromSlug(existingUrls.value[0].slug)
  }
}

function generateUrlFromSlug(slug: string) {
  return useRuntimeConfig().public.appUrl + '/history/' + slug
}

function copyUrl() {
  navigator.clipboard.writeText(url.value)
}
</script>

<template>
  <v-dialog :model-value="visible" max-width="500px" @update:model-value="(value) => emit('update:visible', value)">
    <v-card>
      <v-card-title class="headline">{{ $t('car.control_public_urls') }}</v-card-title>
      <v-card-text>
        <v-row>
          <v-col>
            <v-text-field readonly v-model="url" :label="$t('car.history_url')">
              <template #append-inner>
                <v-icon @click="copyUrl">mdi-content-copy</v-icon>
              </template>
            </v-text-field>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="grey-darken-1" variant="text" @click="closeDialog">{{ $t('common.close') }}</v-btn>
        <v-btn v-if="!existingUrls.length" color="primary" variant="tonal" @click="confirmShare">{{ $t('common.share') }}</v-btn>
        <v-btn v-else color="error" variant="tonal" @click="confirmDelete">{{ $t('common.delete') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>

</style>
