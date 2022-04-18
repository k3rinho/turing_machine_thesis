<template>
  <nav
    v-if="user"
    class="py-2 mx-auto sm:max-w-xl md:max-w-full lg:max-w-screen-xl "
  >
    <div v-if="user">
      <div class="relative flex items-center justify-end text-right">
        <a
          href="/"
          aria-label="Company"
          title="Company"
          class="inline-flex items-right"
        >

          <!-- <img :src="'/img/logo-1.webp'" class="w-24" alt=""> -->

        </a>
        <ul class="flex items-center space-x-8 lg:flex justify-end">
          <li>
            <a
              class="font-medium tracking-wide text-gray-700 transition-colors duration-200 hover:text-primary cursor-pointer" @click="$router.push('/home')"
            >
              Kezdőlap </a>
          </li>
          <!-- <li>
            <a
              class="font-medium  cursor-pointer tracking-wide text-gray-700 transition-colors duration-200 hover:text-primary"
              @click="$router.push('/settings/profile')"
            >Profile settings</a>
          </li> -->
          <li>
            <a
              href="/"
              class="inline-flex items-center justify-center h-8 px-6 font-medium tracking-wide text-white transition duration-200 shadow-md bg-primary hover:bg-secondary focus:shadow-outline focus:outline-none "
              aria-label="Sign up"
              title="Sign up"
              @click.prevent="logout"
            >
              Kijelentkezés
            </a>
          </li>
        </ul>
        <div class="lg:hidden">
          <button
            aria-label="Open Menu"
            title="Open Menu"
            class="p-2 -mr-1 transition duration-200  focus:outline-none focus:shadow-outline hover:bg-deep-purple-50 focus:bg-deep-purple-50"
            @click="isMenuOpen = true"
          >
            <svg class="w-5 text-gray-600" viewBox="0 0 24 24">
              <path
                fill="currentColor"
                d="M23,13H1c-0.6,0-1-0.4-1-1s0.4-1,1-1h22c0.6,0,1,0.4,1,1S23.6,13,23,13z"
              />
              <path
                fill="currentColor"
                d="M23,6H1C0.4,6,0,5.6,0,5s0.4-1,1-1h22c0.6,0,1,0.4,1,1S23.6,6,23,6z"
              />
              <path
                fill="currentColor"
                d="M23,20H1c-0.6,0-1-0.4-1-1s0.4-1,1-1h22c0.6,0,1,0.4,1,1S23.6,20,23,20z"
              />
            </svg>
          </button>
          <div v-if="isMenuOpen" class="absolute top-0 left-0 w-full z-2">
            <div class="p-5 bg-white border shadow-sm">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <a
                    href="/"
                    aria-label="Company"
                    title="Company"
                    class="inline-flex items-center"
                  >
                    <svg
                      class="w-8 text-primary"
                      viewBox="0 0 24 24"
                      stroke-linejoin="round"
                      stroke-width="2"
                      stroke-linecap="round"
                      stroke-miterlimit="10"
                      stroke="currentColor"
                      fill="none"
                    >
                      <rect x="3" y="1" width="7" height="12" />
                      <rect x="3" y="17" width="7" height="6" />
                      <rect x="14" y="1" width="7" height="6" />
                      <rect x="14" y="11" width="7" height="12" />
                    </svg>
                    <span
                      class="ml-2 text-xl font-bold tracking-wide text-gray-800 uppercase"
                    >
                      Company</span>
                  </a>
                </div>
                <div>
                  <button
                    aria-label="Close Menu"
                    title="Close Menu"
                    class="p-2 -mt-2 -mr-2 transition duration-200 hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:shadow-outline"
                    @click="isMenuOpen = false"
                  >
                    <svg class="w-5 text-gray-600" viewBox="0 0 24 24">
                      <path
                        fill="currentColor"
                        d="M19.7,4.3c-0.4-0.4-1-0.4-1.4,0L12,10.6L5.7,4.3c-0.4-0.4-1-0.4-1.4,0s-0.4,1,0,1.4l6.3,6.3l-6.3,6.3 c-0.4,0.4-0.4,1,0,1.4C4.5,19.9,4.7,20,5,20s0.5-0.1,0.7-0.3l6.3-6.3l6.3,6.3c0.2,0.2,0.5,0.3,0.7,0.3s0.5-0.1,0.7-0.3 c0.4-0.4,0.4-1,0-1.4L13.4,12l6.3-6.3C20.1,5.3,20.1,4.7,19.7,4.3z"
                      />
                    </svg>
                  </button>
                </div>
              </div>
              <nav>
                <ul class="space-y-4">
                  <!-- <li>
                    <a href="/"
                       aria-label="Our product"
                       title="Our product"
                       class="font-medium tracking-wide text-gray-700 transition-colors duration-200 hover:text-primary"
                    >Product</a>
                  </li>
                  <li>
                    <a
                      href="/"
                      aria-label="Our product"
                      title="Our product"
                      class="font-medium tracking-wide text-gray-700 transition-colors duration-200 hover:text-primary"
                    >Features</a>
                  </li>
                  <li>
                    <a
                      href="/"
                      aria-label="Product pricing"
                      title="Product pricing"
                      class="font-medium tracking-wide text-gray-700 transition-colors duration-200 hover:text-primary"
                    >Pricing</a>
                  </li>
                  <li>
                    <a
                      href="/"
                      aria-label="About us"
                      title="About us"
                      class="font-medium tracking-wide text-gray-700 transition-colors duration-200 hover:text-primary"
                    >About us</a>
                  </li>
                  <li>
                    <a
                      href="/"
                      class="inline-flex items-center justify-center w-full h-12 px-6 font-medium tracking-wide text-white transition duration-200 shadow-md bg-primary hover:bg-primary focus:shadow-outline focus:outline-none"
                      aria-label="Sign up"
                      title="Sign up"
                    >
                      Sign up
                    </a>
                  </li> -->
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import { mapGetters } from 'vuex'
// import LocaleDropdown from './LocaleDropdown'
// import Dropdown from './common/Dropdown'

export default {
  components: {
    // Dropdown,
    // LocaleDropdown
  },

  data: () => ({
    appName: window.config.appName,
    isMenuOpen: false
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  methods: {
    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to login.
      this.$router.push({ name: 'login' })
    }
  }
}
</script>
