<script setup lang="ts">
import backFetch from "~/utils/backFetch";
import {errorToast, successToast} from "~/utils/toast";
import {useSnackbar} from "~/composables/useSnackbar";

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

function confirmTransfer() {
  backFetch('/user/my-cars/' + props.carId + '/transfer', {
    method: 'post',
    body: form.value,
    headers: {'Accept': 'application/json'},
  }).then(({ data, error}) => {
    if (error.value) {
      useSnackbar().show(error.value.data.message)
      closeDialog()
      return
    }
    useSnackbar().show('Automobilis perleistas sėkmingai!')
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
      <v-btn color="blue darken-1" text @click="confirmTransfer">{{ $t('common.transfer') }}</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
</template>

<style scoped>

</style>
