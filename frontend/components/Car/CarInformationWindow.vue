<script setup lang="ts">
import { type Car } from '~/types/Car';
import TransferCarDialog from "~/components/Car/Dialogs/TransferCarDialog.vue";
import ShareCarDialog from "~/components/Car/Dialogs/ShareCarDialog.vue";
import ControlPublicHistoryDialog from "~/components/Car/Dialogs/ControlPublicHistoryDialog.vue";
import type {User} from "~/types/User";

const { successToast, errorToast } = useToast()
const { t } = useI18n()

const props = defineProps({
  car: {
    type: Object as () => Car & { owner: User },
    required: true,
  },
})

const showTransferDialog = ref(false)
const showShareDialog = ref(false)
const showShareHistoryDialog = ref(false)
const photo = ref([])

function carTransferred() {
  closeTransferDialog()
  navigateTo('/cars')
}

function closeTransferDialog() {
  showTransferDialog.value = false
}

function closeShareDialog() {
  showShareDialog.value = false
}
function carShared() {
  closeShareDialog()
}

function historyShared() {
  closeShareHistoryDialog()
}

function closeShareHistoryDialog() {
  showShareHistoryDialog.value = false
}

function changePhoto() {
  backAction(`/cars/${props.car.id}`, {
    method: 'PUT',
    body: {
      image: photo.value[0],
    },
    sendsFiles: true,
  }).then((response: any) => {
    successToast(t('car.photo_changed'))
    console.log(response)
    props.car.logo_path = response.logo_path
    photo.value = [];
  }).catch((error) => {
    errorToast(t('car.photo_change_failed'))
  })
}

function limitString(str: string) {
  // Check if the string is short enough that it doesn't need to be limited
  if (str.length <= 15) {
    return str;
  }

  // Extract the first 10 characters and the last 5 characters
  const firstPart = str.slice(0, 10);
  const lastPart = str.slice(-5);

  // Combine the parts with "..."
  return `${firstPart}...${lastPart}`;
}
</script>

<template>
  <div>
    <div class="d-flex justify-end flex-wrap ga-2">
      <v-btn class=" flex-grow-1" color="secondary" @click="showShareDialog = true">{{ $t('car.share_car') }}</v-btn>
      <v-btn class=" flex-grow-1" color="secondary" @click="showTransferDialog = true">{{ $t('car.transfer_car') }}</v-btn>
      <v-btn class=" flex-grow-1" color="secondary" @click="showShareHistoryDialog = true">{{ $t('car.control_public_urls') }}</v-btn>
    </div>
    <div class="d-flex mt-4 flex-wrap">
      <div class="flex-grow-1" style="min-width: 33%;">
        <v-img :src="imageUrl(car.logo_path)" />
        <v-file-input v-model="photo" :label="$t('car.change_photo')" class="mt-2" density="comfortable">
          <template v-if="photo.length" #append>
            <v-btn color="primary" @click="changePhoto">{{ $t('common.change') }}</v-btn>
          </template>
          <template #selection="{ fileNames }">
            <template v-for="fileName in fileNames" :key="fileName">
              <v-chip
                  class="me-2"
                  size="small"
              >
                {{ limitString(fileName) }}
              </v-chip>
            </template>
          </template>
        </v-file-input>
      </div>
      <v-list class="flex-grow-1" style="min-width: 40%">
        <v-list-item v-if="false"><strong>{{ $t('car.owner') }}: </strong>{{ car.owner.first_name }} {{ car.owner.last_name }}</v-list-item>
        <v-list-item><strong>{{ $t('car.plate_no') }}: </strong>{{ car.plate_no }}</v-list-item>
        <v-list-item><strong>{{ $t('car.vin') }}: </strong>{{ car.vin }}</v-list-item>
        <v-list-item><strong>{{ $t('car.make') }}: </strong>{{ car.make }}</v-list-item>
        <v-list-item><strong>{{ $t('car.model') }}: </strong>{{ car.model }}</v-list-item>
        <v-list-item><strong>{{ $t('car.year_of_manufacture') }}: </strong>{{ car.year_of_manufacture }}</v-list-item>
        <v-list-item v-if="false"><strong>{{ $t('car.color') }}: </strong>{{ car.color }}</v-list-item>
      </v-list>
    </div>
    <TransferCarDialog :car-id="car.id" :visible="showTransferDialog" @confirm="carTransferred" @close="closeTransferDialog" />
    <ShareCarDialog :car-id="car.id" :visible="showShareDialog" @confirm="carShared" @close="closeShareDialog" />
    <ControlPublicHistoryDialog :visible="showShareHistoryDialog" :car-id="car.id" @confirm="historyShared" @close="closeShareHistoryDialog" />
  </div>
</template>

<style scoped>

</style>