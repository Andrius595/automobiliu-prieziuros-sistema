<script setup lang="ts">
import backFetch from "~/utils/backFetch";
import type {User} from "~/types/User";
import {Roles} from "~/enums/roles";

const { t } = useI18n()

const emit = defineEmits(['close', 'confirm', 'update:visible'])
const props = defineProps({
  visible: {
    type: Boolean,
    required: true,
  },
})

const emptyForm: Partial<User> & { title: string } = {
  title: '',
  first_name: '',
  last_name: '',
  email: '',
}

const form = ref({...emptyForm})

const errors = ref({})

function closeDialog() {
  emit('close')
}

function resetForm() {
  form.value = {...emptyForm}
}

async function confirmCreate() {
  const body = {
    ...form.value
  }
  const { data, error } = await backFetch('/services/', {
    method: 'post',
    body,
  })

  if (error.value) {
    console.error(error.value)
    errors.value = error.value.data.errors
    return
  }

  resetForm()
  emit('confirm')
}
</script>

<template>
<v-dialog :model-value="visible" max-width="500px" @update:model-value="(value) => emit('update:visible', value)">
  <v-card>
    <v-card-title class="headline">{{ $t('service.create_service') }}</v-card-title>
    <v-card-text>
      <v-row>
        <v-col cols="12">
          <v-text-field v-model="form.title" :label="$t('service.title')" required></v-text-field>
        </v-col>
        <v-divider />
        <v-col cols="12">
          <h3 class="text-subtitle-1">{{ $t('roles.service_admin') }}</h3>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field v-model="form.first_name" :label="$t('user.first_name')" required></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field v-model="form.last_name" :label="$t('user.last_name')" required></v-text-field>
        </v-col>
        <v-col cols="12">
          <v-text-field v-model="form.email" :label="$t('user.email')" required></v-text-field>
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
