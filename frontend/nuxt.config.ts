// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: {
    enabled: true,

    timeline: {
      enabled: true
    }
  },
  ssr: false,
  build: {
    transpile: ['vuetify', 'vue-sonner', 'vue-chartjs'],
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
    vueI18n: './I18n.config.ts',
  },
  pwa: {},
  runtimeConfig: {
    jwtSecret: process.env.JWT_SECRET,
    public: {
      appUrl: process.env.AUTH_ORIGIN || 'http://localhost:3000',
      apiURL: process.env.BACKEND_API_URL || 'http://localhost/api',
      storageURL: process.env.STORAGE_URL || 'http://localhost/storage/',
      jwtTTL: ((process.env.JWT_TTL as number|undefined) || 60) * 60,
      jwtRefreshTTL: ((process.env.JWT_REFRESH_TTL as number|undefined) || 60*24*7) * 60,
    }
  }
})