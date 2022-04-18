<template>
  <div class="flex ">
    <div class="w-full md:w-2/3 md:mx-auto md:max-w-md">
      <card :title="$t('register')">
        <form @submit.prevent="register" @keydown="form.onKeydown($event)">
          <!-- Name -->
          <text-input name="name" :form="form" :label="'Név'" :required="true" />

          <!-- Email -->
          <text-input name="email" :form="form" :label="'E-mail'" :required="true" />

          <!-- Password -->
          <text-input class="mt-8" native-type="password"
                      name="password" :form="form" :label="'Jelszó'" :required="true"
          />

          <!-- Password Confirmation-->
          <text-input class="mt-8" native-type="password"
                      name="password_confirmation" :form="form" :label="'Jelszó újra'" :required="true"
          />

          <!-- Submit Button -->
          <v-button class="w-full bg-primary" :loading="form.busy">
            <span class="text-white">Regisztrálok</span>
          </v-button>

          <!-- GitHub Register Button -->
          <login-with-github />
        </form>
      </card>
    </div>
  </div>
</template>

<script>
import Form from 'vform'
import LoginWithGithub from '~/components/LoginWithGithub'

export default {
  components: {
    LoginWithGithub
  },

  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('register') }
  },

  data: () => ({
    form: new Form({
      name: '',
      email: '',
      password: '',
      password_confirmation: ''
    }),
    mustVerifyEmail: false
  }),

  methods: {
    async register () {
      // Register the user.
      const { data } = await this.form.post('/register')

      // Must verify email fist.
      if (data.status) {
        this.mustVerifyEmail = true
      } else {
        // Log in the user.
        const { data: { token } } = await this.form.post('/login')

        // Save the token.
        this.$store.dispatch('auth/saveToken', { token })

        // Update the user.
        await this.$store.dispatch('auth/updateUser', { user: data })

        // Redirect home.
        this.$router.push({ name: 'home' })
      }
    }
  }
}
</script>
