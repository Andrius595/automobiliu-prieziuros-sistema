<script setup lang="ts">
import backFetch from "~/utils/backFetch";

const { successToast } = useToast()

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
  mileage: {
    type: Number,
    required: false,
  },
})

const mileage = ref<number>(0)


watch(() => props.visible, (value) => {
  if (value && props.mileage !== null && props.mileage !== undefined) {
    mileage.value = props.mileage
  }
})

function closeDialog() {
  emit('close')
}

async function confirmComplete() {
  const { error } = await backFetch('/service/appointments/'+props.appointmentId+'/complete', {
    method: 'post',
    body: {
      current_mileage: mileage,
    }
  })

  if (error.value) {
    console.error(error.value)

    return
  }

  successToast('Vizitas užbaigtas sėkmingai')
  emit('confirm')
}
</script>

<template>
<v-dialog :model-value="visible" max-width="500px" @update:model-value="(value) => emit('update:visible', value)">
  <v-card>
    <v-card-title class="headline">Užbaigti vizitą</v-card-title>
    <v-card-text>
      Ar tikrai norite užbaigti šį vizitą? Po užbaigimo duomenys tampa nekeičiami.
      <v-row class="mt-2">
        <v-col>
          <v-text-field v-model="mileage" label="Dabartinis kilometražas" type="number" variant="outlined" required />
        </v-col>
      </v-row>
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="grey-darken-1" variant="text" @click="closeDialog">{{ $t('common.cancel') }}</v-btn>
      <v-btn color="success" variant="tonal" @click="confirmComplete">{{ $t('common.confirm') }}</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
</template>

<style scoped>

</style>
