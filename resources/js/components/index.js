import './common'
import './forms'
import VHeader from './common/Header'
import Vue from 'vue'
import Child from './Child'

// Components that are registered globaly.
[
  Child
].forEach(Component => {
  console.log(Component.name)
  Vue.component(Component.name, Component)
})

Vue.component('VHeader', VHeader)
