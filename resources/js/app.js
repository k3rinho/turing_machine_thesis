import Vue from 'vue'
import store from '~/store'
import router from '~/router'
import i18n from '~/plugins/i18n'
import App from '~/components/App'
import Vuex from 'vuex'
import vuetify from './plugins/vuetify'

import '~/plugins'
import '~/components'

Vue.config.productionTip = false
Vue.use(Vuex)

Vue.use(vuetify, {
  icons: {
    iconfont: 'mdi'
  },
  global: {
    ripples: false
  }
})
Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
  vuetify,
  i18n,
  store,
  router,
  ...App
})
