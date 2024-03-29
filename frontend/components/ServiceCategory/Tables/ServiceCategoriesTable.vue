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
          <h2>{{ $t('navigation.service_categories_list') }}</h2>
          <v-btn color="primary" @click="createItem">{{ $t('service_category.create_service_category') }}</v-btn>
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
    <DeleteServiceCategoryDialog :service-category-id="actionItemId" :visible="deleteDialogVisible" @close="deleteDialogVisible = false" @confirm="itemDeleted" />
    <EditServiceCategoryDialog :service-category-id="actionItemId" :visible="editDialogVisible" @close="editDialogVisible = false" @confirm="itemEdited" />
    <CreateServiceCategoryDialog :visible="createDialogVisible" @close="createDialogVisible = false" @confirm="itemCreated" />
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
import DeleteServiceCategoryDialog from "~/components/ServiceCategory/Dialogs/DeleteServiceCategoryDialog.vue";
import EditServiceCategoryDialog from "~/components/ServiceCategory/Dialogs/EditServiceCategoryDialog.vue";
import CreateServiceCategoryDialog from "~/components/ServiceCategory/Dialogs/CreateServiceCategoryDialog.vue";
import type {DatatablesOptions} from "~/types/DataTable";
import type {ServiceCategory} from "~/types/ServiceCategory";

const { t } = useI18n()

const itemsPerPage = ref(5)
const headers = ref([
  {title: t('service_category.name'), key: 'name'},
  {title: t('tables.actions'), key: 'actions', sortable: false, align: 'end' },
])
const totalItems = ref(0)
const serverItems = ref<ServiceCategory[]>([])
const loading = ref(true)

const deleteDialogVisible = ref(false)
const editDialogVisible = ref(false)
const createDialogVisible = ref(false)
const actionItemId = ref<number|undefined>(undefined)


async function loadItems({page, itemsPerPage, sortBy}: DatatablesOptions) {
  loading.value = true
  const query = {
    perPage: itemsPerPage,
    page,
    sortBy: sortBy?.length ? sortBy[0]?.key : null,
    sortDirection: sortBy?.length ? sortBy[0]?.order : null,
  }

  const { data }  = await backFetch<PaginatedResponse<ServiceCategory>>('/service-categories', {
    method: 'get',
    query,
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

async function itemDeleted() {
  deleteDialogVisible.value = false
  await loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})
}

async function itemEdited() {
  editDialogVisible.value = false
  await loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})
}

async function itemCreated() {
  createDialogVisible.value = false
  await loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})
}

</script>

<style scoped>

</style>