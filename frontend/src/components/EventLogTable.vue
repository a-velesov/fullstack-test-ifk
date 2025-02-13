<template>
  <q-table
    title="Event Log"
    :rows="data"
    :columns="columns"
    row-key="id"
    :loading="loading"
    :pagination.sync="pagination"
    @request="onRequest"
  >
    <template v-slot:top-right>
      <q-input borderless dense debounce="500" v-model="filter" placeholder="Search">
        <template v-slot:append>
          <q-icon name="search" />
        </template>
      </q-input>
    </template>

    <template v-slot:body-cell-event_time="props">
      <q-td :props="props">
        {{ formatDate(props.row.event_time) }}
      </q-td>
    </template>

     <template v-slot:body-cell-user_id="props">
      <q-td :props="props">
        {{ getUserName(props.row.user_id) }}
      </q-td>
    </template>

    <template v-slot:body-cell-event_data="props">
      <q-td :props="props">
        <pre>{{ JSON.stringify(props.row.event_data, null, 2) }}</pre>
      </q-td>
    </template>

  </q-table>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useEventLogStore } from 'src/stores/event-log';
import { storeToRefs } from 'pinia';
import { QTableProps, useQuasar } from 'quasar';

const $q = useQuasar()

const eventLogStore = useEventLogStore();
const { eventLogs } = storeToRefs(eventLogStore);
const { fetchEventLogs } = eventLogStore;

const loading = ref(false);
const filter = ref('');
const data = computed(() => eventLogs.value.data)

const columns = [
  { name: 'event_time', required: true, label: 'Date/Time', align: 'left', field: 'event_time', sortable: true },
  { name: 'user_id', label: 'User', align: 'left', field: 'user_id', sortable: false },
  { name: 'event_type', label: 'Event', align: 'left', field: 'event_type', sortable: false },
  { name: 'event_data', label: 'Details', align: 'left', field: 'event_data', sortable: false }
];

const pagination = ref({
  page: 1,
  rowsPerPage: 16,
  sortBy: 'event_time',
  descending: true
});

const onRequest = async (props: QTableProps['requestProps']) => {
  loading.value = true;

  try {
    const { page, rowsPerPage, sortBy, descending } = props.pagination;
    const sortDirection = descending ? 'desc' : 'asc';

    await fetchEventLogs({ page, rowsPerPage, sortBy, sortDirection });
  } catch (error) {
    console.error('Error fetching data:', error);
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return date.toLocaleString();
};

const getUserName = (userId: number | null) => {
  if (userId === null) {
    return 'System';
  }

  // TODO: Fetch user name from API
  return `User ${userId}`;
};

onMounted(async () => {
  await fetchEventLogs({ page: 1, rowsPerPage: 16, sortBy: 'event_time', sortDirection: 'desc' });
});
</script>