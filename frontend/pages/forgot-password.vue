<script setup lang="ts">
import {definePageMeta} from "#imports";
import { toast } from 'vuetify-sonner';
const { t } = useI18n()

const form = ref({
  email: '',
})

const errors = ref({
  email: '',
})

async function sendResetLink() {
  const { data, error } = await useFetch(useRuntimeConfig().public.apiURL + '/auth/forgot-password', {
    method: 'post',
    body: form.value,
    watch: false,
  })

  if (data.value) {
    toast.success(data.value.status)
  } else if (error.value) {
    errors.value = error.value.data.errors
  }
}

definePageMeta({
  layout: 'guest',
  middleware: ['guest'],
})
</script>

<template>
<v-container class="d-flex flex-column justify-center align-center flex-grow-1">
  <div class="w-50">
    <h1 class="text-center text-primary">{{ $t('common.aps_full') }}</h1>
    <h2 class="text-center text-secondary">{{ $t('auth.remind_password') }}</h2>
    <v-row>
      <v-col
          cols="12"
      >
        <v-text-field
            v-model="form.email"
            :label="$t('user.email')"
            :error-messages="errors.email"
            required
            variant="solo-filled"
            color="primary"
        ></v-text-field>
      </v-col>
      <v-col
          cols="12"
      >
      </v-col>
    </v-row>
    <div class="d-flex mt-4 align-center">
      <v-spacer />
      <span class="text-subtitle-2"><NuxtLink to="/login"><span class="text-primary">{{ $t('auth.login') }}!</span></NuxtLink></span>
      <v-btn variant="elevated" type="submit" color="primary" class="ml-2" @click="sendResetLink">{{ $t('auth.send_reset_link') }}</v-btn>
    </div>
  </div>
</v-container>
</template>

<style scoped>

</style>