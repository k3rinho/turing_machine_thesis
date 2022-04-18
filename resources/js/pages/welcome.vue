<template>
  <div class="container p-10">
    <div class="text-center">
      <!-- <div class="text-6xl font-thin text-gray-500 mt-16 mb-8" dusk="title">
        {{ title }}
      </div> -->

      <div>
        <!--
            login button, blue tailwind
           -->
        <a v-if="!user" href="/login" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
          Login
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  layout: 'default',
  middleware: 'guest',
  metaInfo () {
    return { title: this.$t('home') }
  },

  data: () => ({
    title: window.config.appName
  }),

  computed: mapGetters({
    authenticated: 'auth/check'
  }),
  mounted () {
    this.$router.push('/home')
  }
}
</script>

<style scoped>
.top-right {
  position: absolute;
  right: 10px;
  top: 18px;
}

</style>
