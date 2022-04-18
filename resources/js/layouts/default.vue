<template>
  <div class="main-layout mx-auto bg-white min-h-screen shadow-lg">
    <navbar />
    <v-app>
      <child />
    </v-app>
  </div>
</template>

<script>
import Navbar from '~/components/Navbar'

export default {
  name: 'MainLayout',

  components: {
    Navbar
  }
}
</script>
