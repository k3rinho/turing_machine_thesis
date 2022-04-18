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
                Létrehozás ideje
              </th>

              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Megnyitás</span>
              </th>
              <th />
            </tr>
          </thead>
          <tbody v-if="tasks && tasks.length" class="bg-white divide-y divide-gray-200">
            <tr v-for="(t,index) in tasks" :key="t.id" @click="showMachines(t)">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 w-12">
                {{ index }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ t.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ t.description }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ t.created_at }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button class="text-white px-2 py-1 rounded-md bg-indigo-500 hover:text-gray-300" @click="showMachines(t)">
                  Megnyitás
                </button>
              </td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr>
              <td colspan="7" class="font-lg text-center py-8">
                Nincs elérhető feladat
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="text-right">
        <v-btn color="primary" dark @click="newTask = {};dialog = true">
          Új feladat hozzáadása
        </v-btn>
      </div>
    </div>
    <v-dialog v-model="dialog" max-width="800">
      <v-card>
        <v-card-title>
          <span class="headline">Új feladat hozzáadása</span>
        </v-card-title>
        <v-card-text>
          <v-text-field v-model="newTask.name" outlined label="Feladat neve" required />
          <v-textarea v-model="newTask.description" outlined label="Feladat leírása" required />
          <v-text-field v-model="newTask.inputs" outlined label="Inputok (rendre, vesszővel elválasztva)" required />
          <v-text-field v-model="newTask.outputs" outlined label="Outputok (rendre, vesszővel elválasztva)" required />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="grey lighten-1" dark flat @click="dialog = false">
            Mégse
          </v-btn>
          <v-btn color="blue darken-1" dark flat @click="addTask()">
            Hozzáad
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialogMachines" max-width="1200">
      <v-card>
        <v-card-title>
          <span class="headline">Új feladat hozzáadása</span>
        </v-card-title>
        <v-card-text>
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-400">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                  #
                </th>
                <th class="w-40">
                  Feladat létrehozója
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                  Név
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                  Létrehozás ideje
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                  Legutóbbi módosítás
                </th>

                <th colspan="2" class="text-center font-medium text-gray-800 bg-gray-500">
                  Tesztelés
                </th>
                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Megnyitás</span>
                </th>
                <th />
              </tr>
            </thead>
            <template v-if="selTask.machines && selTask.machines.length">
              <tbody v-for="(m,index) in selTask.machines" :key="m.id" class="bg-white divide-y divide-gray-200 " :class="{'': selRow == m.id}">
                <tr @click="selRow=m.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ index+1 }}
                  </td>
                  <td class="px-1" :class="{'bg-green-200': m.task}">
                    {{ m.user && m.user.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ m.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ m.created_at }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ m.updated_at }}
                  </td>
                  <td class="w-0 px-3 " :class="{'bg-yellow-50': selRow == m.id,'bg-gray-50':selRow !==m.id}">
                    <v-text-field v-model="m.testinput" small dense outlined label="Teszt input" hide-details="" style="font-size:12px;width:120px" />
                  </td>
                  <td class="w-0 px-3 " :class="{'bg-yellow-50': selRow == m.id,'bg-gray-50':selRow !==m.id}">
                    <v-btn small dark color="blue" @click="runMachine(m)">
                      Teszt
                    </v-btn>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button class="text-white px-2 py-1 rounded-md bg-indigo-500 hover:text-gray-300" @click="open(m.id)">
                      Megnyitás
                    </button>
                  </td>
                  <td class="px-1 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button class="text-white px-2 py-1 rounded-md bg-red-500 hover:text-gray-300" @click="deleteProj(m.id)">
                      Töröl
                    </button>
                  </td>
                </tr>
              </tbody>
            </template>
            <tbody v-else>
              <tr>
                <td colspan="8" class="font-lg text-center py-8">
                  Nincs elérhető gép
                </td>
              </tr>
            </tbody>
          </table>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="grey lighten-1" dark flat @click="dialogMachines = false">
            Bezárás
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
      selTask: { machines: [] },
      dialogMachines: false,
      dialog: false,
      active: false,
      userMachines: [],
      tasks: [],
      newTask: {}
    }
  },
  computed: {
    ...mapGetters({
      user: 'auth/user'
    })
  },
  async mounted () {
    // axios.get('user')
    this.$store.dispatch('auth/fetchUser')
    this.synchTasks()
  },
  methods: {
    async showMachines (t) {
      const { data } = await axios.get('machinesbytask?id=' + t.id)
      this.selTask.machines = data
      this.dialogMachines = true
    },
    async synchTasks () {
      const { data } = await axios.get('tasksbyowner')
      this.tasks = data
    },
    open (id) {
      this.$router.push({ name: 'machine', params: { id } })
    },
    addMachine () {
      const name = prompt('Gép neve:')
      if (name) {
        const response = axios.post('createproject', { name: name })
        console.log(response.data)
        this.$store.dispatch('auth/fetchUser')
      }
    },
    async addTask () {
      this.newTask.creator_id = this.user.id
      this.newTask.io = JSON.stringify({ input: this.newTask.inputs.split(','), output: this.newTask.outputs.split(',') })
      await axios.post('task', this.newTask)
      this.synchTasks()
      this.dialog = false
    },
    async runMachine (m) {
      if (!m.testinput) {
        alert('Kérlek add meg a teszt inputot!')
        return
      }
      try {
        const { data } = await axios.post('/processing', { data: JSON.parse(m.data), input: m.testinput })
        alert('Input: ' + m.testinput + '\n' + data.length + ' lépés\nVégeredmény:' + data[data.length - 1].input)
      } catch (e) {
        alert('A konfiguráció nem működik a megadott inputtal! (' + m.testinput + ')')
      }
    },
    async deleteProj (id) {
      if (confirm('Biztosan törölni akarod a projektet?')) {
        await axios.post('deleteproject', { id })
        this.$store.dispatch('auth/fetchUser')
      }
    }
  }
}
</script>
