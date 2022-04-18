import Vue from 'vue'

import Dropdown from './Dropdown'
import Card from './Card'
import Button from './Button'
import Header from './Header'
import SurveySection from './SurveySection'
// Components that are registered globaly.
[
  Card,
  Button,
  Dropdown,
  Header,
  SurveySection
].forEach(Component => {
  Vue.component(Component.name, Component)
})
