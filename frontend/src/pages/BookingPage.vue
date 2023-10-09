<template>
  <q-page padding>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
      <h4 style="margin: 0;">List Page</h4>
      <div v-if="isLoggedIn">
        <q-btn @click="add" unelevated color="primary" label="Add +" />
      </div>
    </div>
    <q-markup-table>
      <thead>
        <tr>
          <th class="text-left">Room Name</th>
          <th class="text-left">Booked By</th>
          <th class="text-left">Date</th>
          <th class="text-left">From</th>
          <th class="text-left">To</th>
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
                <q-item clickable v-close-popup @click="remove">
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
    <booking-dialog ref="dialog" @load="loadBookings" />
  </q-page>
</template>

<script>
import { defineComponent, onMounted, ref, watch } from 'vue';
import { api } from '../boot/axios';
import BookingDialog from 'src/components/BookingDialog.vue';

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

    const loadBookings = () => {
      api
        .get(`/bookings?page=${bookings.value.current_page}`)
        .then((response) => {
          bookings.value = response.data.bookings;
          console.log(bookings.value);
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

    const remove = () => {
      console.log('Remove');
    };

    onMounted(() => {
      loadBookings();
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
      isLoggedIn
    }
  }
})
</script>
