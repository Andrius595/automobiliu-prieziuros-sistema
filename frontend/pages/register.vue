<template>
  <v-container class="d-flex flex-column justify-center align-center flex-grow-1">
    <h1 class="mb-4 site-name text-h2 text-primary">CarHorizontal</h1>
    <v-form v-model="valid" style="width: 50%" @submit.prevent="register">
      <div>
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
              cols="12"
          >
            <v-text-field
                v-model="form.email"
                :label="$t('user.email')"
                hide-details
                required
            ></v-text-field>
          </v-col>
          <v-col
              cols="12"
              md="6"
          >
            <v-text-field
                v-model="form.password"
                type="password"
                :counter="10"
                :label="$t('user.password')"
                hide-details
                required
            ></v-text-field>
          </v-col>
          <v-col
              md="6"
              cols="12"
          >
            <v-text-field
                v-model="form.password_confirmation"
                type="password"
                :counter="10"
                :label="$t('user.password_confirmation')"
                hide-details
                required
            ></v-text-field>
          </v-col>
        </v-row>
        <div class="d-flex align-center mt-4">
          <v-spacer />

          <span class="text-subtitle-2 text-uppercase">{{ $t('auth.already_have_an_account') }} <NuxtLink to="/login"><span class="text-primary">{{ $t('auth.login') }}!</span></NuxtLink></span>
          <v-btn variant="elevated" type="submit" color="primary" class="ml-2">{{ $t('auth.register') }}</v-btn>
        </div>
      </div>
    </v-form>
  </v-container>
</template>

<script setup lang="ts">
const valid = ref(false)
const email = ref('')
const password = ref('')

const {errorToast} = useToast()

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  password_confirmation: '',
})


definePageMeta({
  layout: 'guest',
  middleware: ['guest'],
})

async function register() {
  if (valid.value) {
    const {data, error} = await useFetch('/api/auth/register', {
      method: 'POST',
      body: form.value,
      watch: false,
    })

    if (error.value) {
      const errorMessage = error.value.data?.data?.message ?? 'Įvyko klaida'
      errorToast(errorMessage)

      return
    }

    await navigateTo('/cars')
  }
}
</script>

<style scoped>
.site-name {
  font-family: Afacad, Helvetica, sans-serif;
  font-weight: 500
}
</style>