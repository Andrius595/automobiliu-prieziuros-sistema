<template>
  <div>
    <v-data-table
        :headers="headers"
        :items="items"
        class="elevation-1"
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
    </v-data-table>
  </div>
</template>

<script setup lang="ts">
import type {Appointment} from "~/types/Appointment";

const props = defineProps({
  appointments: {
    type: Array,
    required: true,
  },
})

const items = ref([...props.appointments])

const headers = ref([
  {title: 'service.service_title', key: 'service.title', sortable: false},
  {title: 'appointment.current_mileage', key: 'current_mileage', sortable: false},
  {title: 'appointment.completed_at', key: 'completed_at', sortable: false},
  {title: 'appointment.records', key: 'records_short_description', sortable: false},
  {title: 'appointment.records_full_description', key: 'records_full_description', sortable: false},
  {title: 'appointment.blockchain_transaction', key: 'transaction_hash', sortable: false},
  {title: 'appointment.transaction_check_string', key: 'transaction_check_string', sortable: false},
])

function generateCheckHashString(appointment) {
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

</script>

<style scoped>

</style>