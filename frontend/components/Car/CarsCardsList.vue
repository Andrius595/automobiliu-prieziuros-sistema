<template>
  <div>
    <div class="mb-2 d-flex justify-end align-center">
      <v-btn color="primary" @click="createItem">{{ $t('car.register_car') }}</v-btn>
    </div>
    <v-card>
      <v-data-iterator
          :items="serverItems"
          :items-per-page="itemsPerPage"
          :search="search"
          :loading="loading"
      >
        <template v-slot:header>
          <v-toolbar color="secondary" class="px-2">
            <div class="d-flex justify-space-between w-100 align-center">
              <h2>{{ $t('navigation.cars_list') }}</h2>
              <v-text-field
                  v-model="search"
                  density="comfortable"
                  :placeholder="$t('common.search')"
                  prepend-inner-icon="mdi-magnify"
                  style="max-width: 300px;"
                  variant="solo"
                  clearable
                  hide-details
              ></v-text-field>
            </div>

          </v-toolbar>
        </template>

        <template v-slot:default="{ items }">
          <v-container class="pa-2" fluid>
            <v-row dense>
              <v-col
                  v-for="item in items"
                  :key="item.id"
                  cols="12"
                  md="4"
              >
                <v-card class="d-flex flex-column pb-3 flex-grow-1 h-100"  border flat>
                  <v-img style="max-height: 150px" :src="imageUrl('')"></v-img>

                  <v-list-item :subtitle="item.raw.vin" class="mb-2">
                    <template v-slot:title>
                      <strong class="text-h6 mb-2">{{ item.raw.make }} {{ item.raw.model}} {{item.raw.year_of_manufacture}} ({{ item.raw.plate_no}})</strong>
                    </template>
                  </v-list-item>
                  <div class="d-flex flex-column align-center">

                  </div>

                  <div class="d-flex px-4 mt-auto align-center align-stretch">
                    <v-btn color="primary" class="flex-grow-1" :to="'/cars/'+item.raw.id">
                      Peržiūrėti
                    </v-btn>
                    <v-btn color="secondary" size="custom" class="px-3 ml-2">
                      <v-icon
                          color="white"
                          small
                          @click="editItem(item.id)"
                      >
                        mdi-pencil
                      </v-icon>
                    </v-btn>
                    <v-btn color="error" size="custom" class="px-3 ml-2">
                      <v-icon
                          color="white"
                          small
                          @click="removeCar(item.id)"
                      >
                        mdi-delete
                      </v-icon>
                    </v-btn>
                  </div>
                </v-card>
              </v-col>
            </v-row>
          </v-container>
        </template>

        <template v-slot:footer="{ page, pageCount, prevPage, nextPage }">
          <div class="d-flex align-center justify-center pa-4">
            <v-btn
                :disabled="page === 1"
                density="comfortable"
                icon="mdi-arrow-left"
                variant="tonal"
                rounded
                @click="prevPage"
            ></v-btn>

            <div class="mx-2 text-caption">
              Page {{ page }} of {{ pageCount }}
            </div>

            <v-btn
                :disabled="page >= pageCount"
                density="comfortable"
                icon="mdi-arrow-right"
                variant="tonal"
                rounded
                @click="nextPage"
            ></v-btn>
          </div>
        </template>
      </v-data-iterator>
    </v-card>
    <UserRemoveCarDialog :car-id="carId" :visible="deleteDialogVisible" @close="deleteDialogVisible = false" @confirm="carRemoved" />
    <EditCarDialog :car-id="carId" :visible="editDialogVisible" @close="editDialogVisible = false" @confirm="carEdited" />
    <RegisterCarDialog :visible="createDialogVisible" @close="createDialogVisible = false" @confirm="carCreated" />
  </div>
</template>

<script setup lang="ts">
import {type PaginatedResponse} from "~/types/Responses";
import {type Car} from "~/types/Car";
import EditCarDialog from "~/components/Car/Dialogs/EditCarDialog.vue";
import RegisterCarDialog from "~/components/Car/Dialogs/RegisterCarDialog.vue";
import UserRemoveCarDialog from "~/components/Car/Dialogs/UserRemoveCarDialog.vue";
import type {DatatablesOptions} from "~/types/DataTable";
import backFetch from "~/utils/backFetch";

const props = defineProps({
  userId: {
    type: Number,
    required: true,
  }
})

const search = ref('')

const itemsPerPage = ref(3)
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

await loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})

async function loadItems({page, itemsPerPage, sortBy}: DatatablesOptions) {
  loading.value = true
  const query = {
    perPage: -1,
    page,
    sortBy: sortBy?.length ? sortBy[0].key : null,
    sortDirection: sortBy?.length ? sortBy[0].order : null,
  }

  const { data }  = await backFetch<PaginatedResponse<Car>>('/user/my-cars', {
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