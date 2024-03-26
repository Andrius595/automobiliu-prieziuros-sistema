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
        <div class="d-flex justify-items-end pa-4 bg-secondary">
          <v-spacer />
          <v-btn color="primary" @click="showCreateDialog = true">{{ $t('service.employees.create_employee') }}</v-btn>
        </div>
      </template>
      <template #item.roles="{item}">
        <v-chip v-for="role in item.roles" :key="role.name" color="primary" class="mr-2">{{ $t('roles.'+role.name) }}</v-chip>
      </template>
      <template #item.actions="{ item }">
        <div class="d-flex justify-end gap-x-2">
          <v-btn color="primary" class="px-0" @click="openEditDialog(item.id)">
            <v-icon icon="mdi-pencil text-white" size="x-large" />
          </v-btn>
          <v-btn color="error" @click="openDeleteDialog(item.id)">
            <v-icon icon="mdi-delete text-white" size="x-large" />
          </v-btn>
        </div>
      </template>
    </v-data-table-server>
    <CreateEmployeeDialog :visible="showCreateDialog" :service-id="serviceId" @close="closeCreateDialog" @confirm="employeeCreated" />
    <EditEmployeeDialog :visible="showEditDialog" :service-id="serviceId" :user-id="actionItemId as number" @close="closeEditDialog" @confirm="employeeEdited" />
    <DeleteEmployeeDialog :visible="showDeleteDialog" :service-id="serviceId" :user-id="actionItemId as number" @close="closeDeleteDialog" @confirm="employeeDeleted" />
  </div>
</template>

<script setup lang="ts">
import {type PaginatedResponse} from "~/types/Responses";
import backFetch from "~/utils/backFetch";
import type {User} from "~/types/User";
import type {DatatablesOptions, ReadonlyHeaders} from "~/types/DataTable";
import CreateEmployeeDialog from "~/components/Service/Dialogs/CreateEmployeeDialog.vue";
import EditEmployeeDialog from "~/components/Service/Dialogs/EditEmployeeDialog.vue";
import DeleteEmployeeDialog from "~/components/Service/Dialogs/DeleteEmployeeDialog.vue";

const { t } = useI18n()

const props = defineProps({
  serviceId: {
    type: Number,
    required: true,
  },
})

const itemsPerPage = ref(5)
const headers = ref<ReadonlyHeaders>([
  {title: t('user.first_name'), key: 'first_name'},
  {title: t('user.last_name'), key: 'last_name'},
  {title: t('user.email'), key: 'email'},
  {title: t('user.role'), key: 'roles'},
  {title: t('tables.actions'), key: 'actions', sortable: false, align: 'end', width: '6%'},
])
const totalItems = ref(0)
const serverItems = ref<User[]>([])
const loading = ref(true)

const showCreateDialog = ref(false)
const showEditDialog = ref(false)
const showDeleteDialog = ref(false)
const actionItemId = ref<number>()

async function loadItems({page, itemsPerPage, sortBy}: DatatablesOptions) {
  console.log('fetching')
  loading.value = true
  const query = {
    perPage: itemsPerPage,
    page,
    sortBy: sortBy?.length ? sortBy[0].key : null,
    sortDirection: sortBy?.length ? sortBy[0].order : null,
  }

  const { data }  = await backFetch<PaginatedResponse<User>>('/services/'+props.serviceId+'/employees', {
    method: 'get',
    query,
    headers: {'Accept': 'application/json'},
  })


  serverItems.value = data.value?.data || []
  totalItems.value = data.value?.total || 0
  loading.value = false
}

function closeCreateDialog() {
  showCreateDialog.value = false
}

function employeeCreated() {
  closeCreateDialog()
  loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})
}

function openEditDialog(id: number) {
  actionItemId.value = id
  showEditDialog.value = true
}

function closeEditDialog() {
  showEditDialog.value = false
  actionItemId.value = undefined
}

function employeeEdited() {
  closeEditDialog()
  loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})
}

function openDeleteDialog(id: number) {
  actionItemId.value = id
  showDeleteDialog.value = true
}

function closeDeleteDialog() {
  showDeleteDialog.value = false
  actionItemId.value = undefined
}

function employeeDeleted() {
  closeDeleteDialog()
  loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})
}
</script>

<style scoped>
.gap-x-2 {
  column-gap: 1rem;
}
</style>