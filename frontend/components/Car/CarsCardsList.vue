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
          <h2>{{ $t('navigation.cars_list') }}</h2>
          <v-btn color="primary" @click="createItem">{{ $t('car.register_car') }}</v-btn>
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
      <template v-slot:item.actions="{ item }">
        <div class="d-flex flex-nowrap justify-end">
          <v-icon
              color="secondary"
              class="mr-2"
              small
              @click="editItem(item.id)"
          >
            mdi-eye
          </v-icon>
          <v-icon
              color="red-darken-1"
              small
              @click="removeCar(item.id)"
          >
            mdi-delete
          </v-icon>
        </div>
      </template>
    </v-data-table-server>
    <UserRemoveCarDialog :car-id="carId" :visible="deleteDialogVisible" @close="deleteDialogVisible = false" @confirm="carRemoved" />
    <EditCarDialog :car-id="carId" :visible="editDialogVisible" @close="editDialogVisible = false" @confirm="carEdited" />
    <RegisterCarDialog :visible="createDialogVisible" @close="createDialogVisible = false" @confirm="carCreated" />
  </div>
</template>

<script setup lang="ts">
import {type PaginatedResponse} from "~/types/Responses";
import {type Car} from "~/types/Car";
import DeleteCarDialog from "~/components/Car/Dialogs/DeleteCarDialog.vue";
import EditCarDialog from "~/components/Car/Dialogs/EditCarDialog.vue";
import RegisterCarDialog from "~/components/Car/Dialogs/RegisterCarDialog.vue";
import UserRemoveCarDialog from "~/components/Car/Dialogs/UserRemoveCarDialog.vue";

const props = defineProps({
  userId: {
    type: Number,
    required: true,
  }
})

const itemsPerPage = ref(5)
const headers = ref([
  {title: 'car.make', key: 'make'},
  {title: 'car.model', key: 'model'},
  {title: 'car.vin', key: 'vin'},
  {title: 'tables.actions', key: 'actions', sortable: false, align: 'end' },
])
const totalItems = ref(0)
const serverItems = ref<Car[]>([])
const loading = ref(true)

const deleteDialogVisible = ref(false)
const editDialogVisible = ref(false)
const createDialogVisible = ref(false)
const carId = ref<number|undefined>(undefined)

async function loadItems({page, itemsPerPage, sortBy}: { page: number, itemsPerPage: number, sortBy: { key: string, order: 'asc'|'desc'}[] }) {
  loading.value = true
  const query = {
    perPage: itemsPerPage,
    page,
    sortBy: sortBy[0]?.key,
    sortDirection: sortBy[0]?.order,
  }

  const { data }  = await backFetch<PaginatedResponse<Car>>('/user/my-cars', {
    method: 'get',
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
  navigateTo('/cars/'+id)
}

function removeCar(id: number) {
  carId.value = id
  deleteDialogVisible.value = true
}

async function carRemoved() {
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