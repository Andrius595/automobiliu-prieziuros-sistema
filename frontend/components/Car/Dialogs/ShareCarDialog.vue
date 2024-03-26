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

const form = ref({
  email: '',
})

function closeDialog() {
  emit('close')
}

// TODO show error message
function confirmShare() {
  backFetch('/user/my-cars/' + props.carId + '/share', {
    method: 'post',
    body: form.value,
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
      <v-card-title class="headline">{{ $t('car.share_car') }}</v-card-title>
      <v-card-text>
        <v-row>
          <v-col>
            <v-text-field v-model="form.email" :label="$t('user.email')" />
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
