<script setup lang="ts">
import {definePageMeta} from "#imports";
import { toast } from "vuetify-sonner";
import { useFetch, useRoute, useRuntimeConfig } from "#app";

const resetToken = useRoute().params.token
const email = useRoute().query.email

const url = useRuntimeConfig().public.apiURL;


const form = ref({
  password: '',
  password_confirmation: '',
  email: email,
})

const errors = ref({})

async function resetPassword() {
  const body = {
    ...form.value,
    token: resetToken,
  }
  console.log('resetPassword', body)
  const { data, error } = await useFetch('/api/auth/reset-password', {
    method: 'post',
    body
  })

  console.log('data', data)
  console.log('error', error)

  if (data.value) {
    toast.success(data.value.status)
    navigateTo('/login')
  } else if (error.value) {
    errors.value = error.value.data.data.errors
  }
}


definePageMeta({
  layout: 'guest',
  middleware: ['guest'],
})
</script>

<template>
<v-container class="d-flex flex-column justify-center align-center flex-grow-1">
  <h1 class="mb-4 site-name text-primary">{{ $t('common.aps_full')}}</h1>
  <h2 class="mb-4 site-name text-secondary">{{ $t('auth.reset_password')}}</h2>

  <v-form class="w-50">
    <v-text-field
      v-model="form.email"
      :label="$t('user.email')"
      :error-messages="errors.email"
      readonly
    ></v-text-field>
    <v-text-field
      v-model="form.password"
      :label="$t('user.password')"
      :error-messages="errors.password"
      type="password"
    ></v-text-field>
    <v-text-field
      v-model="form.password_confirmation"
      :label="$t('user.password_confirmation')"
      :error-messages="errors.password_confirmation"
      type="password"
    ></v-text-field>
    <v-btn
      @click="resetPassword"
      color="primary"
    >
      {{ $t('auth.reset_password') }}
    </v-btn>
  </v-form>
</v-container>
</template>

<style scoped>

</style>