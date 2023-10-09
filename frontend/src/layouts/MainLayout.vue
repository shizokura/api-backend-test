<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-toolbar-title>
          Meeting Room Booking
        </q-toolbar-title>
        <div>
          <q-btn v-if="route.path === '/bookings'" @click="logout" label="Logout" color="Logout" unelevated />
        </div>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { api } from '../boot/axios';

export default defineComponent({
  name: 'MainLayout',
  components: {

  },
  setup () {
    const router = useRouter();
    const route = useRoute();

    const logout = async () => {
      await api.post('/logout', {}, { headers: {'Authorization' : `Bearer ${localStorage.getItem('token')}`} }).catch(console.log);
      localStorage.removeItem('token');
      return router.push('/');
    };

    return {
      logout,
      route
    }
  }
});
</script>
