<template>
  <q-dialog v-model="dialog">
    <q-card style="width: 500px;">
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6" style="text-transform: capitalize;">{{ type }} Booking</div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>

      <q-card-section>
        <q-form @submit="onSubmit" style="display: flex; flex-direction: column; gap: 15px;">
          <q-input label="Room Name" v-model="formData.room_name" type="text" />
          <q-input label="Booking Date" v-model="formData.booking_date" type="date" />
          <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 15px;">
            <q-input label="From Time" v-model="formData.booking_from" type="time" />
            <q-input label="To Time" v-model="formData.booking_to" type="time" />
          </div>
          <q-btn type="submit" label="Submit" unelevated style="width: 100%;" color="primary" />
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import { defineComponent, ref } from 'vue';
import { Loading, Dialog } from 'quasar';
import { api } from 'src/boot/axios';

function convertTimeToInputFormat(timeString) {
  // Create a Date object with a fixed date and the provided time
  const date = new Date('January 1, 2020 ' + timeString);

  // Get the hours and minutes
  const hours = date.getHours().toString().padStart(2, '0');
  const minutes = date.getMinutes().toString().padStart(2, '0');

  // Format as "HH:mm"
  return hours + ':' + minutes;
}

function convertDateToInputFormat(dateString) {
    // Create a Date object with the provided date string
    const date = new Date(dateString);

    // Get the year, month, and day
    const year = date.getFullYear();
    const month = (date.getMonth() + 1).toString().padStart(2, '0'); // Months are zero-based
    const day = date.getDate().toString().padStart(2, '0');

    // Format as "YYYY-MM-DD"
    return `${year}-${month}-${day}`;
}

export default defineComponent({
  name: 'BookingDialog',
  setup(_, { emit }) {
    const dialog = ref(false);
    const type = ref(null);
    const id = ref(null);
    const formData = ref({
      room_name: '',
      booking_date: '',
      booking_from: '',
      booking_to: ''
    });

    const show = async (showType, showId = null) => {
      id.value = null;

      if (showId) {
        Loading.show();

        id.value = showId;

        const result = await api.get(`/bookings/${ showId }`, { headers: {'Authorization' : `Bearer ${localStorage.getItem('token')}`} }).catch(console.log);

        if (result) {
          formData.value.room_name = result?.data?.booking?.room_name;
          formData.value.booking_date = convertDateToInputFormat(result?.data?.booking?.booking_date);
          formData.value.booking_from = convertTimeToInputFormat(result?.data?.booking?.booking_from);
          formData.value.booking_to = convertTimeToInputFormat(result?.data?.booking?.booking_to);
        } else {
          alert('Some error occurred');
        }

        Loading.hide();
      } else {
        formData.value = {
          room_name: '',
          booking_date: '',
          booking_from: '',
          booking_to: ''
        };
      }

      type.value = showType;
      dialog.value = true;
    };

    const onSubmit = async () => {
      Loading.show();

      const submitData = Object.assign({}, formData.value);

      if (submitData.booking_from) submitData.booking_from = `${submitData.booking_from}:00`;
      if (submitData.booking_to) submitData.booking_to = `${submitData.booking_to}:00`;

      const response = await api[`${ id.value ? 'patch' : 'put' }`](`/bookings/${ id.value || '' }`, submitData, { headers: {'Authorization' : `Bearer ${localStorage.getItem('token')}`} })
        .catch(err => {
          if (err?.response?.data?.messages) {
            Dialog.create({
              title: 'Error',
              message: Object.values(err?.response?.data?.messages).flat()[0]
            });
          } else {
            Dialog.create({
              title: 'Error',
              message: err?.response?.data?.message
            });
          }
        });

      if (response) {
        dialog.value = false;
        emit('load');
      }

      Loading.hide();
    };

    return {
      dialog,
      show,
      type,
      onSubmit,
      formData
    }
  }
})
</script>
