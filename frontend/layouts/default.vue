<template>
  <v-layout class="rounded rounded-md">
    <ClientOnly>
      <v-snackbar
          :model-value="showSnackbar"
          absolute
          location="top right"
          @update:model-value="updateSnackbarVisibility"
      >
        {{ $t(snackbarMessage) }}
        <template v-slot:actions>
          <v-btn
              color="red"
              variant="text"
              @click="hideSnackbar"
          >
            {{ $t('common.close') }}
          </v-btn>
        </template>
      </v-snackbar>
      <VSonner position="top-right" />
    </ClientOnly>
    <v-navigation-drawer :model-value="isSidebarOpen" border="primary thick opacity-1" @update:model-value="(val) => setSidebarValue(val)">
      <template v-slot:prepend>
        <div class="bg-primary d-flex align-center justify-center" style="height:64px">
          <h1>{{ $t('common.aps') }}</h1>
        </div>
        <v-divider />
      </template>
      <NavigationList />
      <template v-slot:append>
        <div class="pa-2">
          <v-btn block color="primary" @click="auth.logout()">
            {{ $t('auth.logout') }}
          </v-btn>
        </div>
      </template>
    </v-navigation-drawer>

    <v-app-bar color="primary">
      <v-btn @click="toggleSidebar"><v-icon>mdi-menu</v-icon></v-btn>
    </v-app-bar>
    <v-main class="align-center justify-centersw" style="min-height: 300px">
      <slot />
    </v-main>
    <v-footer color="primary-darken-1" app>
      <div class="flex-grow-1 text-center white--text footer-site-name">&copy; {{ new Date().getFullYear() }} {{ $t('common.aps') }}</div>
    </v-footer>
  </v-layout>
</template>
<script setup lang="ts">
import NavigationList from '@/components/NavigationList.vue';
import { useSnackbar } from '@/composables/useSnackbar';
import {useAuth} from "~/composables/useAuth";
import {VSonner} from "vuetify-sonner";
import {useLayoutSidebar} from "~/composables/useLayoutSidebar";

const showSnackbar = computed(() => useSnackbar().isVisible())
const snackbarMessage = computed(() => useSnackbar().getMessage())
const { isSidebarOpen, toggleSidebar, setSidebarValue } = useLayoutSidebar()
function hideSnackbar() {
  useSnackbar().hide()
}

const auth = useAuth()

const drawer = ref(false); // should be stored in useState

function updateSnackbarVisibility(value: boolean) {
  useSnackbar().setVisibility(value)
}
</script>

<style lang="scss">
.card-snackbar {
  max-width:672px!important;
  min-width:344px!important;
  min-height:48px!important;
}

.rounded :deep(.v-navigation-drawer) {
  border-right-color: rgba(var(--v-theme-primary), 1) !important;
}

.rounded :deep(.v-navigation-drawer--left) {
  border-right-color: rgba(var(--v-theme-primary), 1) !important;
  border-right-width: thick !important;
}

.footer-site-name {
  font-family: Afacad, Helvetica, sans-serif;
}
</style>