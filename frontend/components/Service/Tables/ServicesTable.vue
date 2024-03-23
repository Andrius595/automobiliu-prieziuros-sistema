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
          <v-btn color="primary" :to="'/services/'+item.id">
            <v-icon icon="mdi-eye text-white" size="x-large" />
          </v-btn>
        </div>
      </template>
    </v-data-table-server>

    <ConfirmRegistrationDialog :visible="showConfirmationDialog" :appointment-id="actionItemId" @confirm="appointmentConfirmed" @close="closeAppointmentConfirmDialog" />
  </div>
</template>

<script setup lang="ts">
import {type PaginatedResponse} from "~/types/Responses";
import backFetch from "~/utils/backFetch";
import type {Appointment} from "~/types/Appointment";
import ConfirmRegistrationDialog from "~/components/Service/Dialogs/ConfirmRegistrationDialog.vue";
import type {Service} from "~/types/Service";

const itemsPerPage = ref(5)
const headers = ref([
  {title: 'service.title', key: 'title'},
  {title: 'service.description', key: 'description'},
  {title: 'Actions', key: 'actions', sortable: false, align: 'end', width: '6%'},
])
const totalItems = ref(0)
const serverItems = ref<Appointment[]>([])
const loading = ref(true)

const showConfirmationDialog = ref(false)
const actionItemId = ref<number|undefined>(undefined)

async function loadItems({page, itemsPerPage, sortBy}: { page: number, itemsPerPage: number, sortBy: { key: string, order: 'asc'|'desc' }}) {
  loading.value = true
  const query = {
    perPage: itemsPerPage,
    page,
    sortBy: sortBy[0]?.key,
    sortDirection: sortBy[0]?.order,
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

function showConfirmAppointmentDialog(itemId: number) {
  actionItemId.value = itemId
  showConfirmationDialog.value = true
}

function appointmentConfirmed() {
  closeAppointmentConfirmDialog()
  loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: ''})
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