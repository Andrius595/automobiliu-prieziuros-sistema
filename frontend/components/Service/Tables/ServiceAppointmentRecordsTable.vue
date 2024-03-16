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
          <h2>{{ $t('appointment.records') }}</h2>
          <v-btn v-if="!appointmentCompleted" color="primary" @click="openCreateRecordDialog">{{ $t('record.create_record') }}</v-btn>
        </div>
        <v-divider />
      </template>
      <template v-slot:headers="{ columns, isSorted, getSortIcon, toggleSort }">
        <tr>
          <template v-for="column in columns" :key="column.key">
            <th :class="{'text-end': column.align === 'end'}">
              <span class="mr-2" :class="{'cursor-pointer': column.sortable}" @click="() => column.sortable ? toggleSort(column) : false">{{ $t(column.title) }}</span>
              <template v-if="isSorted(column)">
                <v-icon :icon="getSortIcon(column)"></v-icon>
              </template>
            </th>
          </template>
        </tr>
      </template>
      <template #item.actions="{ item }">
        <div class="d-flex justify-end gap-x-2">
          <v-btn color="primary" @click="showEditRecordDialog(item.id)">
            <v-icon icon="mdi-pencil text-white" size="large" />
          </v-btn>
          <v-btn color="error" @click="showDeleteRecordDialog(item.id)">
            <v-icon icon="mdi-delete text-white" size="large" />
          </v-btn>
        </div>
      </template>
    </v-data-table-server>

    <DeleteRecordDialog :visible="showDeleteDialog" :record-id="actionItemId" @confirm="deleteConfirmed" @close="closeDeleteRecordDialog" />
    <EditRecordDialog :visible="showEditDialog" :record-id="actionItemId" @confirm="recordEdited" @close="closeEditRecordDialog" />
    <CreateRecordDialog :visible="showCreateRecordDialog" :appointment-id="appointmentId" @confirm="recordCreated" @close="closeCreateRecordDialog" />
  </div>
</template>

<script setup lang="ts">
import {type PaginatedResponse} from "~/types/Responses";
import backFetch from "~/utils/backFetch";
import type {Appointment} from "~/types/Appointment";
import ConfirmRegistrationDialog from "~/components/Service/Dialogs/ConfirmRegistrationDialog.vue";
import DeleteRecordDialog from "~/components/Service/Dialogs/DeleteRecordDialog.vue";
import EditRecordDialog from "~/components/Service/Dialogs/EditRecordDialog.vue";
import CreateRecordDialog from "~/components/Service/Dialogs/CreateRecordDialog.vue";

const itemsPerPage = ref(5)

const headers = computed(() => {
  let temp: any = [
    {title: 'record.short_description', key: 'short_description', width: '32%'},
    {title: 'record.description', key: 'description', width: '32%'},
  ]

  if (!props.appointmentCompleted) {
    temp.push({title: 'tables.actions', key: 'actions', sortable: false, align: 'end', width: '6%'})
  }

  return temp
})
const totalItems = ref(0)
const serverItems = ref<Appointment[]>([])
const loading = ref(true)

const showDeleteDialog = ref(false)
const showEditDialog = ref(false)
const showCreateRecordDialog = ref(false)
const actionItemId = ref<number|undefined>(undefined)

const props = defineProps({
  appointmentId: {
    type: Number,
    required: true,
  },
  appointmentCompleted: {
    type: Boolean,
    required: false,
    default: false,
  }
})

async function loadItems({page, itemsPerPage, sortBy}: { page: number, itemsPerPage: number, sortBy: string }) {
  loading.value = true
  const query = {
    perPage: itemsPerPage,
    page,
  }

  const { data }  = await backFetch<PaginatedResponse<Appointment>>('/service/appointments/'+props.appointmentId+'/records', {
    method: 'get',
    query,
    headers: {'Accept': 'application/json'},
  })


  serverItems.value = data.value?.data || []
  totalItems.value = data.value?.total || 0
  loading.value = false
}

function showDeleteRecordDialog(itemId: number) {
  actionItemId.value = itemId
  showDeleteDialog.value = true
}

function closeDeleteRecordDialog() {
  showDeleteDialog.value = false
  actionItemId.value = undefined
}

function deleteConfirmed() {
  closeDeleteRecordDialog()
  loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: ''})
}

function showEditRecordDialog(itemId: number) {
  actionItemId.value = itemId
  showEditDialog.value = true
}

function closeEditRecordDialog() {
  showEditDialog.value = false
  actionItemId.value = undefined
}

function openCreateRecordDialog() {
  showCreateRecordDialog.value = true
}

function closeCreateRecordDialog() {
  showCreateRecordDialog.value = false
}

function recordCreated() {
  closeCreateRecordDialog()
  loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: ''})
}

function recordEdited() {
  closeEditRecordDialog()
  loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: ''})
}

</script>

<style scoped>
.gap-x-2 {
  column-gap: 1rem;
}
</style>