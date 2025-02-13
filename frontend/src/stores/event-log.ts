import { defineStore } from 'pinia';
import { api } from 'boot/axios';

interface EventLog {
  id: number;
  event_time: string;
  user_id: number | null;
  event_type: string;
  event_data: any;
  hotel_timezone: string;
  created_at: string;
  updated_at: string;
}

interface EventLogState {
  eventLogs: {
    data: EventLog[];
    current_page: number;
    from: number;
    last_page: number;
    per_page: number;
    to: number;
    total: number;
  };
}

interface FetchEventLogsParams {
  page: number;
  rowsPerPage: number;
  sortBy: string;
  sortDirection: string;
}


export const useEventLogStore = defineStore('eventLog', {
  state: (): EventLogState => ({
    eventLogs: {
      data: [],
      current_page: 1,
      from: null,
      last_page: 1,
      per_page: 16,
      to: null,
      total: 0,
    },
  }),

  getters: {
    getEventLogs: (state) => state.eventLogs,
  },

  actions: {
    async fetchEventLogs(params: FetchEventLogsParams) {
      try {
        const { page, rowsPerPage, sortBy, sortDirection } = params;
        const response = await api.get('/event-logs', {
          params: {
            page,
            per_page: rowsPerPage,
            sortBy,
            sortDirection,
          },
        });

        this.eventLogs = response.data;
      } catch (error) {
        console.error('Error fetching event logs:', error);
        throw error;
      }
    },
  },
});