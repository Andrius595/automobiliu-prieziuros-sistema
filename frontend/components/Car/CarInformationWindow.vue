<script setup lang="ts">
import { type Car } from '~/types/Car';
import TransferCarDialog from "~/components/Car/Dialogs/TransferCarDialog.vue";
import ShareCarDialog from "~/components/Car/Dialogs/ShareCarDialog.vue";
import ControlPublicHistoryDialog from "~/components/Car/Dialogs/ControlPublicHistoryDialog.vue";

const props = defineProps({
  car: {
    type: Object as () => Partial<Car>,
    required: true,
  },
})

const showTransferDialog = ref(false)
const showShareDialog = ref(false)
const showShareHistoryDialog = ref(false)

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
</script>

<template>
  <div>
    <div class="d-flex justify-end">
      <v-btn color="primary" @click="showShareDialog = true">{{ $t('car.share_car') }}</v-btn>
      <v-btn color="primary" class="ml-4" @click="showTransferDialog = true">{{ $t('car.transfer_car') }}</v-btn>
      <v-btn color="primary" class="ml-4" @click="showShareHistoryDialog = true">{{ $t('car.control_public_urls') }}</v-btn>
    </div>
    <div>
      <v-list>
        <v-list-item v-if="false"><strong>{{ $t('car.owner') }}: </strong>{{ car.owner.first_name }} {{ car.owner.last_name }}</v-list-item>
        <v-list-item><strong>{{ $t('car.plate_no') }}: </strong>{{ car.plate_no }}</v-list-item>
        <v-list-item><strong>{{ $t('car.vin') }}: </strong>{{ car.vin }}</v-list-item>
        <v-list-item><strong>{{ $t('car.make') }}: </strong>{{ car.make }}</v-list-item>
        <v-list-item><strong>{{ $t('car.model') }}: </strong>{{ car.model }}</v-list-item>
        <v-list-item><strong>{{ $t('car.year_of_manufacture') }}: </strong>{{ car.year_of_manufacture }}</v-list-item>
        <v-list-item v-if="false"><strong>{{ $t('car.color') }}: </strong>{{ car.color }}</v-list-item>

      </v-list>
      <TransferCarDialog :car-id="car.id" :visible="showTransferDialog" @confirm="carTransferred" @close="closeTransferDialog" />
      <ShareCarDialog :car-id="car.id" :visible="showShareDialog" @confirm="carShared" @close="closeShareDialog" />
      <ControlPublicHistoryDialog :visible="showShareHistoryDialog" :car-id="car.id" @confirm="historyShared" @close="closeShareHistoryDialog" />
    </div>
  </div>
</template>

<style scoped>

</style>