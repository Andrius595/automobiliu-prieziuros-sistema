// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },
  ssr: false,
  build: {
    transpile: ['vuetify', 'vue-sonner'],
  },
  modules: [
    '@vite-pwa/nuxt',
    'vuetify-nuxt-module',
    '@nuxtjs/i18n',
    '@vueuse/nuxt',
  ],
  i18n: {
    locales: ['en', 'lt'],
    defaultLocale: 'lt',
  },
  pwa: {},
  runtimeConfig: {
    jwtSecret: process.env.JWT_SECRET,
    public: {
      apiURL: process.env.BACKEND_API_URL || 'http://localhost/api',
      jwtTTL: ((process.env.JWT_TTL as number|undefined) || 60) * 60,
      jwtRefreshTTL: ((process.env.JWT_REFRESH_TTL as number|undefined) || 60*24*7) * 60,
    }
  }
})
