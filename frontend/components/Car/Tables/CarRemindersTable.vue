<template>
  <div>
    <v-data-table
        v-model:items-per-page="itemsPerPage"
        :headers="headers"
        :items="serverItems"
        :items-length="totalItems"
        :loading="loading"
        class="elevation-1"
        @update:options="loadItems"
    >
      <template #top>
        <div class="d-flex pa-2 bg-secondary justify-space-between">
          <h2>{{ $t('reminder.reminders') }}</h2>
          <v-btn color="primary" @click="showCreateDialog = true">{{ $t('reminder.new_reminder') }}</v-btn>
        </div>
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
      <template v-slot:item.last_reminded_at="{ item }">
        {{ timestampToDate(item.last_reminded_at) }}
      </template>
      <template v-slot:item.actions="{ item }">
        <div class="d-flex flex-nowrap justify-end">
          <v-icon
              color="primary"
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
    </v-data-table>

    <CreateReminderDialog :visible="showCreateDialog" :car-id="carId" @close="closeCreateDialog" @confirm="reminderCreated" />
    <EditReminderDialog :visible="showEditDialog" :reminder-id="actionItemId" @close="closeEditDialog" @confirm="reminderEdited" />
    <DeleteReminderDialog :visible="showDeleteDialog" :reminder-id="actionItemId" @close="closeDeleteDialog" @confirm="reminderDeleted" />
  </div>
</template>

<script setup lang="ts">
import type {Reminder} from "~/types/Reminder";
import type {PaginatedResponse} from "~/types/Responses";
import CreateReminderDialog from "~/components/Reminder/Dialogs/CreateReminderDialog.vue";
import {timestampToDate} from "~/utils/general";
import EditReminderDialog from "~/components/Reminder/Dialogs/EditReminderDialog.vue";
import DeleteReminderDialog from "~/components/Reminder/Dialogs/DeleteReminderDialog.vue";

const props = defineProps({
  carId: {
    type: Number,
    required: true,
  },
})
const itemsPerPage = ref(5)

const serverItems = ref<Reminder[]>([])
const totalItems = ref(0)
const loading = ref(true)
const headers = ref([
  {title: 'reminder.title', key: 'title', sortable: false},
  {title: 'reminder.description', key: 'description', sortable: false},
  {title: 'reminder.interval', key: 'interval', sortable: false},
  {title: 'reminder.last_reminded_at', key: 'last_reminded_at', sortable: false},
  {title: 'tables.actions', key: 'actions', sortable: false},
])

const showCreateDialog = ref(false)
const showEditDialog = ref(false)
const showDeleteDialog = ref(false)

const actionItemId = ref<number|undefined>()

async function loadItems({page, itemsPerPage, sortBy}: { page: number, itemsPerPage: number, sortBy: { key: string, order: 'asc'|'desc'}[] }) {
  const query = {
    perPage: itemsPerPage,
    page,
    sortBy: sortBy[0]?.key,
    sortDirection: sortBy[0]?.order,
  }
  const { data } = await backFetch<PaginatedResponse<Reminder>>('/cars/'+props.carId+'/reminders', {
    method: 'get',
    query,
    headers: {'Accept': 'application/json'},
  })

  if (data.value) {
    serverItems.value = data.value.data
    loading.value = false
  }
}

function editItem(id: number) {
  actionItemId.value = id
  showEditDialog.value = true
}

function deleteItem(id: number) {
  actionItemId.value = id
  showDeleteDialog.value = true
}

function closeCreateDialog() {
  showCreateDialog.value = false
}

function reminderCreated() {
  closeCreateDialog()
  loadItems({page: 1, itemsPerPage: 5, sortBy: []})
}

function closeEditDialog() {
  showEditDialog.value = false
  actionItemId.value = undefined
}

function closeDeleteDialog() {
  showDeleteDialog.value = false
  actionItemId.value = undefined
}

function reminderEdited() {
  closeEditDialog()
  loadItems({page: 1, itemsPerPage: 5, sortBy: []})
}

function reminderDeleted() {
  closeDeleteDialog()
  loadItems({page: 1, itemsPerPage: 5, sortBy: []})
}

</script>

<style scoped>

</style>