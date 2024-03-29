<template>
  <div>
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
              <h2>{{ $t('navigation.services_list') }}</h2>
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
                  :key="item.title"
                  cols="12"
                  md="4"
                  lg="3"
              >
                <v-card class="d-flex flex-column pb-3 flex-grow-1 h-100"  border flat>
                  <v-img style="max-height: 150px" :src="imageUrl(item.raw.logo_path)"></v-img>

                  <v-list-item :subtitle="item.raw.description" class="mb-2">
                    <template v-slot:title>
                      <strong class="text-h6 mb-2">{{ item.raw.title }}</strong>
                    </template>
                  </v-list-item>
                  <div class="d-flex flex-column align-center">
                    <v-rating :model-value="item.raw.rating/2" color="yellow-darken-3" half-increments readonly />

                    <template v-if="item.raw.rating === 0">
                      <v-list-item-title class="text-caption">{{ $t('service.no_reviews') }}</v-list-item-title>
                    </template>
                    <template v-else>
                      <div class="text-caption">{{ item.raw.rating/2 }} ({{ item.raw.reviews_count }})</div>
                    </template>
                  </div>

                  <div class="d-flex justify-end px-4 mt-auto">
                    <v-btn color="primary" :to="'/services/'+item.raw.id">
                      <v-icon icon="mdi-eye text-white" size="x-large" />
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
<!--          <template v-slot:footer>
          <v-pagination
              :v-model="page"
              :length="Math.ceil(totalItems/itemsPerPage)"
              @input="loadItems({page, itemsPerPage, sortBy: []})"
          ></v-pagination>
        </template>-->
        </template>
      </v-data-iterator>
    </v-card>
    <CreateServiceDialog :visible="showCreateDialog" @close="closeCreateDialog" @confirm="serviceCreated" />
  </div>
</template>

<script setup lang="ts">
import {type PaginatedResponse} from "~/types/Responses";
import backFetch from "~/utils/backFetch";
import type {Appointment} from "~/types/Appointment";
import ConfirmRegistrationDialog from "~/components/Service/Dialogs/ConfirmRegistrationDialog.vue";
import type {Service} from "~/types/Service";
import type {DatatablesOptions} from "~/types/DataTable";
import CreateServiceDialog from "~/components/Service/Dialogs/CreateServiceDialog.vue";

const itemsPerPage = ref(12)
const totalItems = ref(0)
const serverItems = ref<Service[]>([])
const loading = ref(true)
const search = ref('')

const showCreateDialog = ref(false)
const actionItemId = ref<number|undefined>(undefined)

await loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})

async function loadItems({page, itemsPerPage, sortBy}: DatatablesOptions) {
  console.log('loadItems', page, itemsPerPage, sortBy)
  loading.value = true
  const query = {
    perPage: -1,
    page,
    sortBy: sortBy?.length ? sortBy[0].key : null,
    sortDirection: sortBy?.length ? sortBy[0].order : null,
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

function createItem() {
  showCreateDialog.value = true
}

function closeCreateDialog() {
  showCreateDialog.value = false
}

function serviceCreated() {
  closeCreateDialog()
  loadItems({page: 1, itemsPerPage: itemsPerPage.value, sortBy: []})
}

</script>

<style scoped lang="scss">
//:deep(.v-toolbar__content) {
//  justify-content: flex-end;
//}
</style>