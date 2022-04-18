<template>
  <div class="flex bg-white h-full">
    <div class="w-full md:w-2/3 md:mx-auto md:max-w-md  self-center my-20">
      <card :title="$t('login')" class="shadow-lg">
        <form @submit.prevent="login" @keydown="form.onKeydown($event)">
          <!-- Email -->
          <text-input name="email" :form="form" :label="$t('email')" :required="true" />

          <!-- Password -->
          <text-input class="mt-8" native-type="password"
                      name="password" :form="form" :label="$t('password')" :required="true"
          />
          <br>
          <!-- Remember Me -->
          <!-- <div class="hidden relative items-center justify-between mt-8 mb-6">
            <div> &nbsp;</div>
             <checkbox v-model="remember" class="w-full md:w-1/2" name="remember">
              {{ $t('remember_me') }}
            </checkbox>
          </div> -->

          <!-- Submit Button -->
          <v-button class="w-full bg-primary text-white" :loading="form.busy">
            <span class="text-white">{{ $t('login') }}</span>
          </v-button>
        </form>
      </card>
    </div>
  </div>
</template>

<script>
import Form from 'vform'
import Cookies from 'js-cookie'

export default {
  components: {

  },

  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('login') }
  },

  data: () => ({
    form: new Form({
      email: '',
      password: ''
    }),
    remember: false
  }),

  methods: {
    async login () {
      // Submit the form.
      const { data } = await this.form.post('/login')

      // Save the token.
      this.$store.dispatch('auth/saveToken', {
        token: data.token,
        remember: this.remember
      })

      // Fetch the user.
      await this.$store.dispatch('auth/fetchUser')

      // Redirect home.
      this.redirect()
    },

    redirect () {
      const intendedUrl = Cookies.get('intended_url')

      if (intendedUrl) {
        Cookies.remove('intended_url')
        this.$router.push({ path: intendedUrl })
      } else {
        this.$router.push({ name: 'home' })
      }
    }
  }
}
</script>
