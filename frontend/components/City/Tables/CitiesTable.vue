<template>
  <div>
    <v-data-table-server
        v-model:items-per-page="itemsPerPage"
        :headers="headers"
        :items-length="totalItems"
        :items="serverItems"
        :loading="loading"
        class="elevation-1"
        item-value="name"
        @update:options="loadItems"
    >
      <template v-slot:top>
        <div class="pa-2 d-flex justify-space-between align-center bg-secondary">
          <h2>{{ $t('navigation.cities_list') }}</h2>
          <v-btn color="primary" @click="createItem">{{ $t('city.create_city') }}</v-btn>
        </div>
        <v-divider />
      </template>
      <template v-slot:item.actions="{ item }">
        <div class="d-flex flex-nowrap justify-end">
          <v-icon
              color="secondary"
              class="mr-2"
              small
              @click="editItem(item.id)"
          >
            mdi-pencil
          </v-icon>
          <v-icon
              color="red-darken-1"
              small
              @click="deleteItem(item.id)"
          >
            mdi-delete
          </v-icon>
        </div>
      </template>
    </v-data-table-server>
    <DeleteCityDialog :city-id="actionItemId" :visible="deleteDialogVisible" @close="deleteDialogVisible = false" @confirm="carDeleted" />
    <EditCityDialog :city-id="actionItemId" :visible="editDialogVisible" @close="editDialogVisible = false" @confirm="carEdited" />
    <CreateCityDialog :visible="createDialogVisible" @close="createDialogVisible = false" @confirm="carCreated" />
  </div>
</template>

<script setup lang="ts">
import {type PaginatedResponse} from "~/types/Responses";
import DeleteCarDialog from "~/components/Car/Dialogs/DeleteCarDialog.vue";
import EditCarDialog from "~/components/Car/Dialogs/EditCarDialog.vue";
import CreateCarDialog from "~/components/Car/Dialogs/CreateCarDialog.vue";
import type {Car} from "~/types/Car";
import DeleteCityDialog from "~/components/City/Dialogs/DeleteCityDialog.vue";
import EditCityDialog from "~/components/City/Dialogs/EditCityDialog.vue";
import CreateCityDialog from "~/components/City/Dialogs/CreateCityDialog.vue";
import type {City} from "~/types/City";

const { t } = useI18n()

const itemsPerPage = ref(5)
const headers = ref([
  {title: t('city.name'), key: 'name'},
  {title: 'Actions', key: 'actions', sortable: false, align: 'end' },
])
const totalItems = ref(0)
const serverItems = ref<City[]>([])
const loading = ref(true)

const deleteDialogVisible = ref(false)
const editDialogVisible = ref(false)
const createDialogVisible = ref(false)
const actionItemId = ref<number|undefined>(undefined)


async function loadItems({page, itemsPerPage, sortBy}: { page: number, itemsPerPage: number, sortBy: { key: string, order: 'asc'|'desc'}[] }) {
  loading.value = true
  const query = {
    perPage: itemsPerPage,
    page,
    sortBy: sortBy[0]?.key,
    sortDirection: sortBy[0]?.order,
  }

  const { data }  = await backFetch<PaginatedResponse<City>>('/cities', {
    method: 'GET',
    query,
    headers: {'Accept': 'application/json'},
  })

  serverItems.value = data.value?.data || []
  totalItems.value = data.value?.total || 0
  loading.value = false
}


function createItem() {
  createDialogVisible.value = true
}
function editItem(id: number) {
  actionItemId.value = id
  editDialogVisible.value = true
}

function deleteItem(id: number) {
  actionItemId.value = id
  deleteDialogVisible.value = true
}

async function carDeleted() {
  deleteDialogVisible.value = false
  await loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})
}

async function carEdited() {
  editDialogVisible.value = false
  await loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})
}

async function carCreated() {
  createDialogVisible.value = false
  await loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})
}

</script>

<style scoped>

</style>