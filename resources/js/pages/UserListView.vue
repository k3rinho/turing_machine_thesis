<template>
  <div class="w-full py-2 mx-auto lg:max-w-screen-xl">
    <div class="flex flex-col">
      <div class="shadow overflow-hidden border-b border-gray-100 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-200">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider" />
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                Név
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                Leírás
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                Fiók létrehozásának ideje
              </th>

              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Megnyitás</span>
              </th>
              <th />
            </tr>
          </thead>
          <tbody v-if="users && users.length" class="bg-white divide-y divide-gray-200">
            <tr v-for="(u,index) in users" :key="u.id" class="hover:bg-gray-200">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 w-12">
                {{ index }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ u.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ u.email }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ u.created_at }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button class="text-white px-2 py-1 rounded-md bg-indigo-500 hover:text-gray-300" @click="edit(u)">
                  Módosítás
                </button>
              </td>
              <td class="px-1 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button class="text-white px-2 py-1 rounded-md bg-red-500 hover:text-gray-300" @click="deleteUser(u.id)">
                  Töröl
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="text-center">
          <button class="text-white px-8 py-3 mx-auto rounded-md bg-primary hover:text-gray-300 inline-block" @click="adduser()">
            Új user
          </button>
        </div>
      </div>
    </div>
    <v-dialog v-model="dialog" max-width="600">
      <v-card>
        <v-card-title>
          <span class="headline">Felhasználó</span>
        </v-card-title>
        <v-card-text>
          <v-form ref="form">
            <v-text-field
              v-model="newUser.name"
              label="Név" outlined
              :rules="[v => !!v || 'Név megadása kötelező']"
            />
            <v-text-field
              v-model="newUser.email"
              label="Email" outlined
              :rules="[v => !!v || 'Email megadása kötelező']"
            />
            <v-text-field
              v-model="newUser.password"
              label="Jelszó" outlined
              :rules="[v => !!v || 'Jelszó megadása kötelező']"
            />
            <v-select v-model="newUser.role"
                      :rules="[v => !!v || 'Role megadása kötelező']"
                      label="Role" outlined
                      :items="[{id:'',text:''}, {id:'U',text:'Sima user'}, {id:'T', text:'Feladat posztoló'}, {id:'A',text:'Admin'}]" item-text="text" item-value="id"
            />
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="blue darken-1" text @click="dialog = false">
            Mégse
          </v-btn>
          <v-btn color="blue darken-1" text @click="save()">
            Mentés
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'
export default {
  components: {

  },
  middleware: 'auth',
  metaInfo () {
    return { title: this.$t('home') }
  },
  data () {
    return {
      dialog: false,
      active: false,
      users: [],
      newUser: {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
      }
    }
  },
  computed: {
    ...mapGetters({
      user: 'auth/user'
    })
  },
  async mounted () {
    this.$store.dispatch('auth/fetchUser')
    this.getUsers()
  },
  methods: {
    async getUsers () {
      const { data } = await axios.get('/users')
      this.users = data
    },
    async deleteUser (id) {
      // confirm
      if (confirm('Biztosan törölni akarod a felhasználót?')) {
        await axios.delete('/user?id=' + id)
        this.getUsers()
      }
    },
    async adduser () {
      this.newUser = {}
      this.dialog = true
    },
    async edit (user) {
      this.newUser = user
      this.dialog = true
    },
    async save () {
      await axios.post('/newuser', this.newUser)
      this.getUsers()

      this.dialog = false
    }

  }
}
</script>
