<template>
  <div>
    <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        :headers="headers"
        :items-length="totalItems"
        :items="serverItems"
        :loading="loading"
        class="elevation-1"
        item-value="id"
        @update:options="loadItems"
    >
      <template #top>
        <div class="pa-2 d-flex justify-space-between align-center bg-secondary">
          <h2>{{ $t('navigation.services_list') }}</h2>
          <v-btn color="primary" @click="createItem">{{ $t('service.create_service') }}</v-btn>
        </div>
        <v-divider />
      </template>
      <template #item.actions="{ item }">
        <div class="d-flex justify-end gap-x-2">
          <NuxtLink :to="'/services/'+item.id+'/edit-information'"><v-icon icon="mdi-pencil text-secondary" /></NuxtLink>
          <NuxtLink :to="'/services/'+item.id"><v-icon icon="mdi-eye text-primary" /></NuxtLink>
        </div>
      </template>
    </v-data-table-server>
    <CreateServiceDialog :visible="showCreateDialog" @close="closeCreateDialog" @confirm="serviceCreated" />
  </div>
</template>

<script setup lang="ts">
import {type PaginatedResponse} from "~/types/Responses";
import backFetch from "~/utils/backFetch";
import type {Service} from "~/types/Service";
import type {DatatablesOptions} from "~/types/DataTable";
import CreateServiceDialog from "~/components/Service/Dialogs/CreateServiceDialog.vue";

const { t } = useI18n()
const itemsPerPage = ref(5)
const headers = ref([
  {title: t('service.title'), key: 'title'},
  {title: t('service.description'), key: 'description'},
  {title: t('tables.actions'), key: 'actions', sortable: false, align: 'end', width: '6%'},
])
const totalItems = ref(0)
const serverItems = ref<Service[]>([])
const loading = ref(true)

const showCreateDialog = ref(false)
const actionItemId = ref<number|undefined>(undefined)

async function loadItems({page, itemsPerPage, sortBy}: DatatablesOptions) {
  loading.value = true
  const query = {
    perPage: itemsPerPage,
    page,
    sortBy: sortBy?.length ? sortBy[0].key : null,
    sortDirection: sortBy?.length ? sortBy[0].order : null,
  }

  const { data }  = await backFetch<PaginatedResponse<Service>>('/services/', {
    method: 'get',
    query,
    headers: {'Accept': 'application/json'},
  })

  serverItems.value = data.value?.data || []
  totalItems.value = data.value?.total || 0
  loading.value = false
}

function createItem() {
  showCreateDialog.value = true
}

function closeCreateDialog() {
  showCreateDialog.value = false
}

function serviceCreated() {
  closeCreateDialog()
  loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})
}

</script>

<style scoped>
.gap-x-2 {
  column-gap: 1rem;
}
</style>