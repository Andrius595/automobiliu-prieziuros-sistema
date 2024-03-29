<template>
  <div>
    <v-data-table
        :headers="headers"
        :items="items"
        class="elevation-1"
    >
      <template v-slot:item.current_mileage="{ item }">
        <span>{{ item.current_mileage}} {{ item.mileage_type ? 'm' : 'km'}}</span>
      </template>
      <template v-slot:item.records_short_description="{ item }">
        <ol>
          <li v-for="record in item.records">
            {{ record.short_description }}
          </li>
        </ol>
      </template>
      <template v-slot:item.records_full_description="{ item }">
        <ol>
          <li v-for="record in item.records">
            {{ record.description || '-' }}
          </li>
        </ol>
      </template>
      <template v-slot:item.transaction_hash="{ item }">
        <template v-if="item.transaction_hash">
          <a :href="generateHashUrl(item.transaction_hash)" target="_blank">
            <v-icon color="primary">mdi-open-in-new</v-icon>
          </a>
        </template>
        <span v-else>-</span>
      </template>
      <template v-slot:item.transaction_check_string="{ item }">
        <v-tooltip bottom>
          <template v-slot:activator="{ props }">
            <v-icon v-bind="props" @click="(event) => copyCheckString(item, event)">mdi-content-copy</v-icon>
          </template>
          <span>{{ generateCheckHashString(item) }}</span>
        </v-tooltip>
      </template>
      <template v-if="!isPublic" v-slot:item.actions="{ item }">
        <div class="d-flex flex-nowrap justify-end">
          <v-icon
              color="secondary"
              class="mr-2"
              small
              @click="writeReview(item.id)"
          >
            mdi-message-draw
          </v-icon>
        </div>
      </template>
    </v-data-table>
    <WriteReviewDialog :visible="showReviewDialog" :appointment-id="actionItemId" @close="closeReviewDialog" @confirm="closeReviewDialog" />
  </div>
</template>

<script setup lang="ts">
import type {Appointment} from "~/types/Appointment";
import WriteReviewDialog from "~/components/Appointment/Dialogs/WriteReviewDialog.vue";
import type {Record} from "~/types/Record";
import type {Car} from "~/types/Car";

const { t } = useI18n();

const props = defineProps({
  appointments: {
    type: Array,
    required: true,
  },
  isPublic: {
    type: Boolean,
    required: false,
    default: false,
  }
})

const showReviewDialog = ref(false)
const actionItemId = ref<number>()

const items = ref([...props.appointments])

const headers = ref([
  {title: t('service.service_title'), key: 'service.title', sortable: false},
  {title: t('appointment.current_mileage'), key: 'current_mileage', sortable: false},
  {title: t('appointment.completed_at'), key: 'completed_at', sortable: false},
  {title: t('appointment.records'), key: 'records_short_description', sortable: false},
  {title: t('appointment.records_full_description'), key: 'records_full_description', sortable: false},
  {title: t('appointment.blockchain_transaction'), key: 'transaction_hash', sortable: false},
  {title: t('appointment.transaction_check_string'), key: 'transaction_check_string', sortable: false},
  {title: t('tables.actions'), key: 'actions', sortable: false, align: 'end' },
])

function generateCheckHashString(appointment: Appointment & {records: Record[], car: Car}) {
  const countRecords = appointment.records.length;
  let hash = `${appointment.car.vin}_${appointment.current_mileage}_${appointment.completed_at}_${countRecords}`;
  appointment.records.forEach((record) => {
    hash += '_' + record.short_description;
  });

  return hash;
}

function generateHashUrl(hash: string) {
  return `https://sepolia.etherscan.io/tx/${hash}`;
}

function copyCheckString(item: Partial<Appointment>, event: any) {
  const tooltipId = event.target.getAttribute('aria-describedby')
  const tooltip = document.getElementById(tooltipId) as HTMLElement
  const tooltipSpan = tooltip.querySelector('span') as HTMLSpanElement
  const tooltipText = tooltipSpan.innerText
  const checkString = generateCheckHashString(item);
  navigator.clipboard.writeText(checkString);
  // change tooltip text to Copied for 3 seconds
  tooltipSpan.innerText = 'Nukopijuota!';
  setTimeout(() => {
    tooltipSpan.innerText = tooltipText;
  }, 2000);
}

function writeReview(appointmentId: number) {
  actionItemId.value = appointmentId
  showReviewDialog.value = true
}

function closeReviewDialog() {
  showReviewDialog.value = false
}

</script>

<style scoped>

</style>