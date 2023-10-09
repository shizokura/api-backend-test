<template>
  <q-page padding>
    <q-markup-table>
      <thead>
        <tr>
          <th class="text-left">Room Name</th>
          <th class="text-left">Booked By</th>
          <th class="text-left">Date</th>
          <th class="text-left">From</th>
          <th class="text-left">To</th>
          <th class="text-center"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="booking in bookings.data" :key="booking.id">
          <td class="text-left">{{ booking.room_name }}</td>
          <td class="text-left">{{ booking.booked_by }}</td>
          <td class="text-left">{{ booking.booking_date }}</td>
          <td class="text-left">{{ booking.booking_from }}</td>
          <td class="text-left">{{ booking.booking_to }}</td>
          <td class="text-center">
            <q-btn-dropdown color="primary" label="Actions">
              <q-list>
                <q-item clickable v-close-popup @click="edit">
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
  </q-page>
</template>

<script>
import { defineComponent, onMounted, ref } from 'vue';
import { api } from '../boot/axios';

export default defineComponent({
  name: 'BookingPage',
  setup() {
    const bookings = ref({
      data: [],
      current_page: 1,
      last_page: 1,
    });

    const page = ref(1);

    const loadBookings = () => {
      api
        .get(`/bookings?page=${page.value}`)
        .then((response) => {
          bookings.value = response.data.bookings;
        })
        .catch((error) => {
          console.error('Error loading bookings:', error);
        });
    }

    const edit = () => {
      console.log('Edit');
    };

    const remove = () => {
      console.log('Remove');
    };

    onMounted(() => {
      loadBookings();
    });

    return {
      bookings,
      loadBookings
    }
  }
})
</script>
