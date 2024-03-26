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
      <template #top>
        <div class="pa-2 d-flex justify-space-between align-center bg-secondary">
          <h2>{{ $t('navigation.users_list') }}</h2>
          <v-btn color="primary" @click="createItem">{{ $t('user.create_user') }}</v-btn>
        </div>
        <v-divider />
      </template>
      <template #item.roles="{item}">
        <v-chip v-for="role in item.roles" :key="role.name" color="primary" class="mr-2">{{ $t('roles.'+role.name) }}</v-chip>
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
    <DeleteUserDialog :user-id="actionItemId" :visible="deleteDialogVisible" @close="deleteDialogVisible = false" @confirm="userDeleted" />
    <EditUserDialog :user-id="actionItemId" :visible="editDialogVisible" @close="editDialogVisible = false" @confirm="userEdited" />
    <CreateUserDialog :visible="createDialogVisible" @close="createDialogVisible = false" @confirm="userCreated" />
  </div>
</template>

<script setup lang="ts">
import {type PaginatedResponse} from "~/types/Responses";
import DeleteCarDialog from "~/components/Car/Dialogs/DeleteCarDialog.vue";
import EditCarDialog from "~/components/Car/Dialogs/EditCarDialog.vue";
import type {Car} from "~/types/Car";
import CreateUserDialog from "~/components/User/Dialogs/CreateUserDialog.vue";
import DeleteUserDialog from "~/components/User/Dialogs/DeleteUserDialog.vue";
import EditUserDialog from "~/components/User/Dialogs/EditUserDialog.vue";
const { t } = useI18n()

const itemsPerPage = ref(5)
const headers = ref([
  {title: t('user.first_name'), key: 'first_name'},
  {title: t('user.last_name'), key: 'last_name'},
  {title: t('user.email'), key: 'email'},
  {title: t('user.role'), key: 'roles'},
  {title: t('tables.actions'), key: 'actions', sortable: false, align: 'end' },
])
const totalItems = ref(0)
const serverItems = ref<Car[]>([])
const loading = ref(true)

const deleteDialogVisible = ref(false)
const editDialogVisible = ref(false)
const createDialogVisible = ref(false)
const actionItemId = ref<number>()


async function loadItems({page, itemsPerPage, sortBy}: { page: number, itemsPerPage: number, sortBy: { key: string, order: 'asc'|'desc'}[] }) {
  loading.value = true
  const query = {
    perPage: itemsPerPage,
    page,
    sortBy: sortBy[0]?.key,
    sortDirection: sortBy[0]?.order,
  }

  const { data }  = await backFetch<PaginatedResponse<Car>>('/users', {
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

async function userDeleted() {
  deleteDialogVisible.value = false
  await loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})
}

async function userEdited() {
  editDialogVisible.value = false
  await loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})
}

async function userCreated() {
  createDialogVisible.value = false
  await loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})
}

</script>

<style scoped>

</style>