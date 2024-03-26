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

const url = ref('')

function closeDialog() {
  emit('close')
}

// TODO show error message
function confirmShare() {
  backFetch('/user/my-cars/' + props.carId + '/share-history', {
    method: 'post',
    headers: {'Accept': 'application/json'},
  }).then(({ data, error}) => {
    url.value = useRuntimeConfig().public.appUrl + '/history/' + data.value.slug
  }).catch((e) => {
    console.error(e)
  })
}

function copyUrl() {
  navigator.clipboard.writeText(url.value)
}
</script>

<template>
  <v-dialog :model-value="visible" max-width="500px" @update:model-value="(value) => emit('update:visible', value)">
    <v-card>
      <v-card-title class="headline">{{ $t('car.share_history') }}</v-card-title>
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
        <v-btn color="blue darken-1" text @click="closeDialog">{{ $t('common.cancel') }}</v-btn>
        <v-btn color="blue darken-1" text @click="confirmShare">{{ $t('common.share') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>

</style>
