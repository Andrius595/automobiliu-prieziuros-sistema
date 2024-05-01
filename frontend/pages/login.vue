<template>
  <v-container class="d-flex flex-column justify-center align-center flex-grow-1">
    <h1 class="mb-4 site-name text-h2 text-primary">{{ $t('common.aps_full')}}</h1>
    <v-form style="width: 50%" @submit.prevent="login">
      <div>
        <v-row>
          <v-col
              cols="12"
          >
            <v-text-field
                v-model="email"
                :label="$t('user.email')"
                hide-details
                required
                variant="solo-filled"
                color="primary"
            ></v-text-field>
          </v-col>
          <v-col
              cols="12"
          >
            <v-text-field
                v-model="password"
                type="password"
                :counter="10"
                :label="$t('user.password')"
                hide-details
                required
                variant="solo-filled"
                color="primary"
            ></v-text-field>
          </v-col>
        </v-row>
        <div class="d-flex mt-4 align-center">
          <v-spacer />
          <div class="d-flex flex-column align-end">
            <span class="text-subtitle-2">{{ $t('auth.dont_have_an_account') }} <NuxtLink  to="/register"><span class="text-primary text-subtitle-2">{{ $t('auth.register') }}!</span></NuxtLink></span>
            <NuxtLink to="/forgot-password"><span class="text-primary text-subtitle-2">{{ $t('auth.forgot_password') }}</span></NuxtLink>
          </div>
          <v-btn variant="elevated" type="submit" color="primary" class="ml-2">{{ $t('auth.login') }}</v-btn>
        </div>
      </div>
    </v-form>
  </v-container>
</template>

<script setup lang="ts">
const email = ref('')
const password = ref('')

const {errorToast} = useToast()


definePageMeta({
  layout: 'guest',
  middleware: ['guest'],
})

async function login() {
    $fetch('/api/auth/login', {
      method: 'POST',
      body: {
        email: email.value,
        password: password.value,
      },
    }).then(async (res) => {
      await navigateTo('/cars')
    }).catch((error) => {
        const errorMessage = error.data?.data?.message ?? 'Įvyko klaida'
        errorToast(errorMessage)
    })
}
</script>

<style scoped>
.site-name {
  font-family: Afacad, Helvetica, sans-serif;
  font-weight: 500
}
</style>