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
  serviceId: {
    type: Number,
    required: false,
  },
  userId: {
    type: Number,
    required: true,
  },
})


const emptyForm: Partial<User> & { role: string } = {
  first_name: '',
  last_name: '',
  email: '',
  service_id: props.serviceId,
  role: Roles.SERVICE_EMPLOYEE,
}

const rolesList = [
  {value: Roles.SERVICE_ADMIN, title: t('roles.service_admin')},
  {value: Roles.SERVICE_EMPLOYEE, title: t('roles.service_employee')},
]

const form = ref({...emptyForm})

watch(() => props.visible, (visibleValue) => {
  if (visibleValue && props.userId) {
    fetchUser(props.userId)
  }
})

function closeDialog() {
  emit('close')
}

function resetForm() {
  form.value = {...emptyForm}
}

function confirmEdit() {
  backFetch('/services/'+props.serviceId+'/employees/'+props.userId, {
    method: 'put',
    body: form.value,
    headers: {'Accept': 'application/json'},
  }).then(() => {
    emit('confirm')
    resetForm()
  }).catch((e) => {
    console.error(e)
  })
}

async function fetchUser(userId: number) {
  const { data } = await backFetch<User & {role: string}>('/users/'+userId, {
    method: 'get',
    headers: {'Accept': 'application/json'},
  })
  if (data.value) {
    form.value = data.value
  }
}
</script>

<template>
<v-dialog :model-value="visible" max-width="500px" @update:model-value="(value) => emit('update:visible', value)">
  <v-card>
    <v-card-title class="headline">{{ $t('service.employees.create_employee') }}</v-card-title>
    <v-card-text>
      <v-row>
        <v-col cols="12" md="6">
          <v-text-field v-model="form.first_name" :label="$t('user.first_name')" required></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field v-model="form.last_name" :label="$t('user.last_name')" required></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field v-model="form.email" :label="$t('user.email')" required></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-select v-model="form.role" :items="rolesList" :label="$t('user.role')" required />
        </v-col>
      </v-row>
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="grey darken-1" text @click="closeDialog">{{ $t('common.cancel') }}</v-btn>
      <v-btn color="success darken-1" text @click="confirmEdit">{{ $t('common.save') }}</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
</template>

<style scoped>

</style>
