<script setup lang="ts">
import backFetch from "~/utils/backFetch";
import type {ServiceReview} from "~/types/ServiceReview";

const emit = defineEmits(['close', 'confirm', 'update:visible'])
const props = defineProps({
  visible: {
    type: Boolean,
    required: true,
  },
  appointmentId: {
    type: Number,
    required: false,
  }
})

const emptyForm: Pick<ServiceReview, 'rating' | 'comment'> = {
  rating: 0,
  comment: '',
}

const form = ref({...emptyForm})

watch(() => props.visible, async (value) => {
  if (value) {
    await fetchReview()
  }
})

function closeDialog() {
  emit('close')
}

function confirmReview() {
  backFetch('/appointments/'+props.appointmentId+'/write-review', {
    method: 'DELETE',
    headers: {'Accept': 'application/json'},
  }).then(() => {
    emit('confirm')
  }).catch((e) => {
    console.error(e)
  })
}

async function fetchReview() {
  const { data } = await backFetch<ServiceReview>('/appointments/' + props.appointmentId + '/review', {
    method: 'GET',
    headers: {'Accept': 'application/json'},
  })

  if (data.value) {
    form.value = data.value
  } else {
    form.value = {...emptyForm}
  }
}
</script>

<template>
<v-dialog :model-value="visible" max-width="500px" @update:model-value="(value) => emit('update:visible', value)">
  <v-card>
    <v-card-title class="headline">{{ $t('appointment.leave_review') }}</v-card-title>
    <v-card-text>
      <v-row>
        <v-col cols="12">
          <v-rating
              v-model="form.rating"
              color="yellow-darken-3"
              half-increments
              hover
              size="x-large"
              class="rating-class"
          ></v-rating>
        </v-col>
        <v-col cols="12">
          <v-textarea
              v-model="form.comment"
              label="Comment"
              variant="outlined"
              outlined
              rows="3"
          ></v-textarea>
        </v-col>
      </v-row>
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="grey-darken-1" variant="text" @click="closeDialog">{{ $t('common.cancel') }}</v-btn>
      <v-btn color="success" variant="tonal" @click="confirmReview">{{ $t('common.save') }}</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
</template>

<style scoped lang="scss">
:deep(.rating-class) {
  width: 100%;
  justify-content: space-between;

  & > label {
    display: none;
  }
}
</style>
