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

const emptyForm = {
  title: '',
  description: '',
  interval: 0,
  type: 0,
  last_reminded_at: new Date(),
}
const menu = ref(false)

const formattedDate = computed(() => {
  if (!form.value.last_reminded_at) {
    return ''
  }

  return new Date(form.value.last_reminded_at).toISOString().split('T')[0]
})

const intervalTypes = [
  {title: 'reminder.interval_types.days', value: 0},
  {title: 'reminder.interval_types.weeks', value: 1},
  {title: 'reminder.interval_types.months', value: 2},
  {title: 'reminder.interval_types.years', value: 3},
  {title: 'reminder.interval_types.mileage', value: 4},
]

const form = ref({...emptyForm})

function closeDialog() {
  emit('close')
}

function resetForm() {
  form.value = {...emptyForm}
}

function confirmCreate() {
  const data = {
    ...form.value,
    last_reminded_at: new Date(form.value.last_reminded_at).toISOString().split('T')[0] ?? '',
  }
  backFetch('/cars/'+props.carId+'/reminders', {
    method: 'post',
    body: data,
    headers: {'Accept': 'application/json'},
  }).then(() => {
    emit('confirm')
    resetForm()
  }).catch((e) => {
    console.error(e)
  })
}
</script>

<template>
<v-dialog :model-value="visible" max-width="500px" @update:model-value="(value) => emit('update:visible', value)">
  <v-card>
    <v-card-title class="headline">{{ $t('reminder.new_reminder') }}</v-card-title>
    <v-card-text>
      <v-row>
        <v-col cols="12">
          <v-text-field v-model="form.title" :label="$t('reminder.title')" />
        </v-col>
        <v-col cols="12">
          <v-textarea v-model="form.description" :label="$t('record.description')" />
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field type="number" v-model="form.interval" :label="$t('reminder.interval')" />
        </v-col>
        <v-col cols="12" md="6">
          <v-select v-model="form.type" :label="$t('reminder.interval_type')" :items="intervalTypes">
            <template v-slot:item="{ item, index, props}">
              <v-list-item v-bind="props">
                <template #title>{{ $t(item.title) }}</template>
              </v-list-item>
            </template>
            <template v-slot:selection="{ item, index }">
              {{ $t(item.props.title) }}
            </template>
          </v-select>
        </v-col>
        <template v-if="form.type === 4">
          <v-col cols="12">
            <v-alert type="warning">
              Priminimas gali būti netikslus, jei automobilis turi mažai aptarnavimų.
            </v-alert>
          </v-col>
        </template>
        <v-col cols="12">
          <v-menu
              v-model="menu"
              :close-on-content-click="false"
              location="end"
          >
            <template v-slot:activator="{ props }">
              <v-text-field v-bind="props" label="Data, nuo kurios skaičiuojamas intervalas" readonly :model-value="formattedDate" />
            </template>

            <v-card min-width="300">
              <v-date-picker :hide-header="true" v-model="form.last_reminded_at"></v-date-picker>

              <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn
                    color="primary"
                    variant="text"
                    @click="menu = false"
                >
                  {{  $t('common.save') }}
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-menu>
        </v-col>
      </v-row>
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="grey darken-1" text @click="closeDialog">{{ $t('common.cancel') }}</v-btn>
      <v-btn color="success darken-1" text @click="confirmCreate">{{ $t('common.save') }}</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
</template>

<style scoped>

</style>
