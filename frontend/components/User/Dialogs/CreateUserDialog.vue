<script setup lang="ts">
import backFetch from "~/utils/backFetch";
import type {User} from "~/types/User";
import {Roles} from "~/enums/roles";

const { errorToast, successToast } = useToast()
const { t } = useI18n()

const emit = defineEmits(['close', 'confirm', 'update:visible'])
const props = defineProps({
  visible: {
    type: Boolean,
    required: true,
  },
})

const errors = ref([])

const emptyUser: Pick<User, 'first_name' | 'last_name' | 'email'> & {role: string} = {
  first_name: '',
  last_name: '',
  email: '',
  role: Roles.CLIENT,
}
const form = ref({...emptyUser})

const rolesList = [
  {value: Roles.CLIENT, title: t('roles.client')},
  {value: Roles.SYSTEM_ADMIN, title: t('roles.system_admin')},
]

function resetForm() {
  form.value = {...emptyUser}
}
function closeDialog() {
  emit('close')
}

async function confirmCreate() {
  const { data, error } = await backFetch<User>('/users', {
    method: 'post',
    body: form.value,
  })

  if (error.value) {
    errors.value = error.value.data.errors
    errorToast(error.value.data.message)
    return
  }

  successToast(t('user.created_successfully'))
  emit('confirm')
  resetForm()
}
</script>

<template>
  <v-dialog :model-value="visible" max-width="700px" @update:model-value="(value) => emit('update:visible', value)">
    <v-card>
      <v-card-title class="headline">{{ $t('user.create_user') }}</v-card-title>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col
                cols="12"
                md="6"
            >
              <v-text-field
                  v-model="form.first_name"
                  :label="$t('user.first_name')"
                  hide-details
                  required
              ></v-text-field>
            </v-col>
            <v-col
                cols="12"
                md="6"
            >
              <v-text-field
                  v-model="form.last_name"
                  :label="$t('user.last_name')"
                  hide-details
                  required
              ></v-text-field>
            </v-col>
            <v-col
                cols="12" md="6"
            >
              <v-text-field
                  v-model="form.email"
                  :label="$t('user.email')"
                  hide-details
                  required
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-select v-model="form.role" :items="rolesList" :label="$t('user.role')" required />
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue darken-1" text @click.prevent="closeDialog">{{ $t('common.cancel') }}</v-btn>
        <v-btn color="success" variant="tonal" @click.prevent="confirmCreate">{{ $t('common.create') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>

</style>