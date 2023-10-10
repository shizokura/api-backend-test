<template>
  <q-page padding>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
      <h4 style="margin: 0;">List Page</h4>
      <div v-if="isLoggedIn">
        <q-btn @click="add" unelevated color="primary" label="Add +" />
      </div>
    </div>
    <div style="display: flex; gap: 15px; justify-content: right; margin-bottom: 15px; align-items: center;">
      <q-select v-if="isLoggedIn" v-model="filterBy.user" style="width: 150px;" label="User" :options="['All', 'My Bookings']" />
      <q-select v-model="filterBy.meeting_room" style="width: 150px;" label="Meeting Room" :options="meetingGroupOptions" />
      <q-input v-model="filterBy.from" type="date" label="From" />
      <q-input v-model="filterBy.to" type="date" label="To" />
      <q-input v-model="filterBy.search" type="text" label="Search" />
      <div>
        <q-btn label="Clear All Filter" color="primary" @click="clearFilter" unelevated />
      </div>
    </div>
    <q-markup-table>
      <thead>
        <tr>
          <th class="text-left" @click="toggleSort('room_name')">
            Room Name
            <span v-if="sortColumn === 'room_name'">
              <i class="fas" :class="sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down'"></i>
            </span>
          </th>
          <th class="text-left" @click="toggleSort('users.name')">
            Booked By
            <span v-if="sortColumn === 'users.name'">
              <i class="fas" :class="sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down'"></i>
            </span>
          </th>
          <th class="text-left" @click="toggleSort('booking_date')">
            Date
            <span v-if="sortColumn === 'booking_date'">
              <i class="fas" :class="sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down'"></i>
            </span>
          </th>
          <th class="text-left" @click="toggleSort('booking_from')">
            From
            <span v-if="sortColumn === 'booking_from'">
              <i class="fas" :class="sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down'"></i>
            </span>
          </th>
          <th class="text-left" @click="toggleSort('booking_to')">
            To
            <span v-if="sortColumn === 'booking_to'">
              <i class="fas" :class="sortOrder === 'asc' ? 'fa-sort-up' : 'fa-sort-down'"></i>
            </span>
          </th>
          <th v-if="isLoggedIn" class="text-center"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="booking in bookings.data" :key="booking.id">
          <td class="text-left">{{ booking.room_name }}</td>
          <td class="text-left">{{ booking.user.name }}</td>
          <td class="text-left">{{ booking.booking_date }}</td>
          <td class="text-left">{{ booking.booking_from }}</td>
          <td class="text-left">{{ booking.booking_to }}</td>
          <td v-if="isLoggedIn" class="text-center">
            <q-btn-dropdown unelevated color="primary" label="Actions">
              <q-list>
                <q-item clickable v-close-popup @click="edit(booking.id)">
                  <q-item-section>
                    <q-item-label>Edit</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
              <q-list>
                <q-item clickable v-close-popup @click="remove(booking.id)">
                  <q-item-section>
                    <q-item-label>Delete</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-btn-dropdown>
          </td>
        </tr>
      </tbody>
    </q-markup-table>
    <div style="text-align: center; margin: auto; margin-top: 25px; width: max-content;">
      <q-pagination
        v-model="bookings.current_page"
        :max="Math.ceil(bookings.total / bookings.per_page)"
        direction-links
      />
    </div>
    <booking-dialog ref="dialog" @load="() => { loadBookings(); loadGroupOptions(); }" />
  </q-page>
</template>

<style lang="scss" scoped>
th {
  span {
    i {
      height: 10px;
      width: 10px;
      font-size: 10px;
      padding-left: 5px;
    }
  }
}
</style>

<script>
import { defineComponent, onMounted, ref, watch } from 'vue';
import { api } from '../boot/axios';
import BookingDialog from 'src/components/BookingDialog.vue';
import { Loading, useQuasar } from 'quasar';
import { debounce } from 'lodash';

export default defineComponent({
  name: 'BookingPage',
  components: {
    BookingDialog
  },
  setup() {
    const bookings = ref({
      data: [],
      current_page: 1,
      last_page: 1,
      per_page: 10,
      total: 0
    });

    const dialog = ref(null);
    const isLoggedIn = localStorage.getItem('token') ? true : false;
    const $q = useQuasar();
    const sortColumn = ref('booking_date'); // Initial sorting column
    const sortOrder = ref('asc'); // Initial sorting order

    const filterBy = ref({
      from: null,
      to: null,
      meeting_room: null,
      user: 'All',
      search: null
    });

    const meetingGroupOptions = ref([]);

    watch(filterBy.value, debounce(() => {
      loadBookings();
    }, 300));

    const clearFilter = () => {
      filterBy.value.from = null;
      filterBy.value.to = null;
      filterBy.value.meeting_room = null;
      filterBy.value.user = 'All';
      filterBy.value.search = null;
    };

    const toggleSort = (column) => {
      if (sortColumn.value === column) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
      } else {
        sortColumn.value = column;
        sortOrder.value = 'asc';
      }
      loadBookings();
    };

    const loadBookings = () => {
      api
        .get('/bookings', {
          params: {
            page: bookings.value.current_page,
            sort_column: sortColumn.value,
            sort_order: sortOrder.value,
            ...filterBy.value
          },
          headers: {'Authorization' : `Bearer ${localStorage.getItem('token')}`}
        })
        .then((response) => {
          bookings.value = response.data.bookings;
        })
        .catch((error) => {
          console.error('Error loading bookings:', error);
        });
    };

    const loadGroupOptions = () => {
      api
        .get('/bookings/groups', { headers: {'Authorization' : `Bearer ${localStorage.getItem('token')}`} })
        .then((response) => {
          meetingGroupOptions.value = response.data.bookings.map(booking => booking.room_name);
        })
        .catch((error) => {
          console.error('Error loading bookings:', error);
        });
    }

    const add = () => {
      dialog.value.show('add')
    };

    const edit = (id) => {
      dialog.value.show('edit', id);
    };

    const remove = (id) => {
      $q.dialog({
        title: 'Confirm',
        message: 'Are you sure to delete this data?',
        cancel: true,
        persistent: true
      }).onOk(async () => {
        Loading.show();
        await api.delete(`/bookings/${id}`, { headers: {'Authorization' : `Bearer ${localStorage.getItem('token')}`} }).catch(console.log);
        loadBookings();
        Loading.hide();
      });
    };

    onMounted(() => {
      loadBookings();
      loadGroupOptions();
    });

    watch(() => bookings.value.current_page, () => {
      loadBookings();
    });

    return {
      bookings,
      loadBookings,
      dialog,
      add,
      edit,
      remove,
      isLoggedIn,
      toggleSort,
      sortColumn,
      sortOrder,
      filterBy,
      meetingGroupOptions,
      clearFilter,
      loadGroupOptions
    }
  }
})
</script>
