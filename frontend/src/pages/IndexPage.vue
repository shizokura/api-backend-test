<template>
  <div style="display: flex; place-items: center; height: calc(100vh - 50px)">
    <div class="q-pa-md" style="width: 400px; margin: auto; text-align: center;">

      <q-form
        @submit="onSubmit"
        class="q-gutter-md"
      >
        <q-input
          filled
          v-model="formData.email"
          type="email"
          label="Email Address"
        />

        <q-input
          filled
          v-model="formData.password"
          type="password"
          label="Password"
        />

        <div>
          <q-btn :label="isRegister ? 'Register' : 'Login'" unelevated style="width: 100%;" type="submit" color="primary"/>
        </div>

        <div>{{ isRegister ? 'Already have an account?' : 'Not registered?' }} <a @click="toggleRegister" href="javascript:" style="text-decoration: none;">{{ isRegister ? 'Login an account' : 'Create an account' }}</a></div>
      </q-form>

    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import { api } from '../boot/axios';
import { Loading, Dialog } from 'quasar';
import { useRouter } from 'vue-router';

export default defineComponent({
  name: 'IndexPage',
  components: {  },
  setup () {
    const router = useRouter();

    const formData = ref({
      email: '',
      password: ''
    });

    const isRegister = ref(false);

    const toggleRegister = async () => {
      isRegister.value = !isRegister.value;
      formData.value.email = '';
      formData.value.password = '';
    }

    const onSubmit = async () => {
      Loading.show();

      const result = await api.post(`/${ isRegister.value ? 'register' : 'login' }`, formData.value)
        .catch(err => {
          if (err?.response?.data?.messages) {
            Dialog.create({
              title: 'Error',
              message: err?.response?.data?.messages?.email || err?.response?.data?.messages?.password
            });
          } else {
            Dialog.create({
              title: 'Error',
              message: err?.response?.data?.message
            });
          }
        });

      if (result) {
        console.log(result);
        router.push('/bookings');
      }

      Loading.hide();
    };

    return {
      onSubmit,
      formData,
      isRegister,
      toggleRegister
    };
  }
});
</script>
