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
          <h2>{{ $t('navigation.services.completed_appointments') }}</h2>
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
          <v-btn color="primary" :to="'/services/appointments/'+item.id">
            <v-icon icon="mdi-eye text-white" size="x-large" />
          </v-btn>
        </div>
      </template>
    </v-data-table-server>
  </div>
</template>

<script setup lang="ts">
import {type PaginatedResponse} from "~/types/Responses";
import backFetch from "~/utils/backFetch";
import type {Appointment} from "~/types/Appointment";
import ConfirmRegistrationDialog from "~/components/Service/Dialogs/ConfirmRegistrationDialog.vue";
import type {DatatablesOptions, DatatableSortBy} from "~/types/DataTable";

const itemsPerPage = ref(5)
const headers = ref([
  {title: 'car.make', key: 'car.make'},
  {title: 'car.model', key: 'car.model'},
  {title: 'car.vin', key: 'car.vin'},
  {title: 'appointment.completed_at', key: 'completed_at'},
  {title: 'tables.actions', key: 'actions', sortable: false, align: 'end'},
])
const totalItems = ref(0)
const serverItems = ref<Appointment[]>([])
const loading = ref(true)

const showConfirmationDialog = ref(false)
const actionItemId = ref<number|undefined>(undefined)

async function loadItems({page, itemsPerPage, sortBy}: DatatablesOptions) {
  loading.value = true
  const query = {
    perPage: itemsPerPage,
    page,
    sortBy: sortBy?.length ? sortBy[0].key : null,
    sortDirection: sortBy?.length ? sortBy[0].order : null,
  }

  const { data }  = await backFetch<PaginatedResponse<Appointment>>('/service/appointments/completed', {
    method: 'get',
    query,
    headers: {'Accept': 'application/json'},
  })

  serverItems.value = data.value?.data || []
  totalItems.value = data.value?.total || 0
  loading.value = false
}

function showConfirmAppointmentDialog(itemId: number) {
  actionItemId.value = itemId
  showConfirmationDialog.value = true
}

function appointmentConfirmed() {
  closeAppointmentConfirmDialog()
  loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})
}

function closeAppointmentConfirmDialog() {
  showConfirmationDialog.value = false
  actionItemId.value = undefined
}

</script>

<style scoped>
.gap-x-2 {
  column-gap: 1rem;
}
</style>