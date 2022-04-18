<template>
  <div>
    <TaskView v-if="user.role.toLowerCase() == 't'" />
    <UserView v-if="user.role.toLowerCase() == 'u'" />
    <UserListView v-if="user.role.toLowerCase() == 'a'" />
  </div>
</template>

<script>

import TaskView from './TaskView.vue'
import UserView from './UserView.vue'
import UserListView from './UserListView.vue'
import { mapGetters } from 'vuex'

export default {
  components: {
    TaskView, UserView, UserListView
  },
  middleware: 'auth',
  computed: {
    ...mapGetters({
      user: 'auth/user'
    })
  }
}
</script>
