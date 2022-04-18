<template>
  <div class="w-full py-2 mx-auto lg:max-w-screen-xl">
    <div class="flex flex-col">
      <div class="">
        <div class="">
          <h2 class="w-full text-2xl font-bold">
            Saját gépeim
          </h2>
          <div class="shadow overflow-hidden border-b border-gray-100 sm:rounded-lg">
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
              <template v-if="user.machines && user.machines.length">
                <tbody v-for="(m,index) in user.machines" :key="m.id" class="bg-white divide-y divide-gray-200 " :class="{'': selRow == m.id}">
                  <tr @click="selRow=m.id">
                    <td class="px-1" :class="{'bg-green-200': m.task}">
                      {{ m.task && m.task.user.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {{ index+1 }}
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
                  <!-- <tr v-if="selRow == m.id && m.task">
                    <td colspan="2" class="px-6 py-2 whitespace-nowrap text-sm text-gray-700">
                      {{ m.task.name }}
                    </td>
                    <td colspan="7" class="px-6 py-2 text-sm text-gray-700 break-words whitespace-normal text-right">
                      Feladat: {{ m.task.description }}{{ m.task.description }}{{ m.task.description }}{{ m.task.description }}{{ m.task.description }}{{ m.task.description }}{{ m.task.description }} {{ m.task.description }}{{ m.task.description }}{{ m.task.description }}{{ m.task.description }}{{ m.task.description }}
                    </td>
                  </tr> -->
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
          </div>
          <div class="text-right">
            <v-btn color="primary" dark @click="addMachine()">
              Új gép hozzáadása
            </v-btn>
          </div>
          <hr class="mt-12">
          <h2 class="w-full text-2xl font-bold">
            Feladott feladatok
          </h2>
          <div class="shadow overflow-hidden border-b border-gray-100 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-200">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider" />
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                    Név
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
                <tr v-for="(t) in tasks" :key="t.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ t.user.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ t.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ t.created_at }}
                  </td>
                  <td class="w-0 px-3 ">
                    <!-- <v-text-field v-model="t.testinput" small dense outlined label="Teszt input" hide-details="" style="font-size:12px;width:120px" /> -->
                  </td>
                  <td class="w-0 px-3 ">
                    <v-btn small dark color="green" @click="pickupTask(t)">
                      Feladat felvétele
                    </v-btn>
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

          <hr class="mt-12">
          <h2 class="w-full text-2xl font-bold">
            Publikált gépek
          </h2>
          <div class="shadow overflow-hidden border-b border-gray-100 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-200">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-800 uppercase tracking-wider">
                    Gép megnevezése
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
              <template v-if="publicMachines && publicMachines.length">
                <tbody v-for="(m) in publicMachines" :key="m.id" class="bg-white divide-y divide-gray-200 " :class="{'': selRow == m.id}">
                  <tr @click="selRow=m.id">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ m.name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ m.created_at }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ m.updated_at }}
                    </td>
                    <td class="w-0 px-3 ">
                      <v-text-field v-model="m.testinput" small dense outlined label="Teszt input" hide-details="" style="font-size:12px;width:120px" />
                    </td>
                    <td class="w-0 px-3 ">
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
          </div>
        </div>
      </div>
    </div>
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
      active: false,
      userMachines: [],
      tasks: [],
      selRow: '',
      publicMachines: []
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

    const { data } = await axios.get('allpublicmachines')
    this.publicMachines = data

    this.synchTasks()
  },
  methods: {
    async synchTasks () {
      const { data } = await axios.get('tasks')
      this.tasks = data
    },
    open (id) {
      this.$router.push({ name: 'machine', params: { id } })
    },
    addMachine () {
      const name = prompt('Gép neve:')
      if (name) {
        axios.post('createproject', { name: name })
        this.$store.dispatch('auth/fetchUser')
      }
    },
    async runMachine (m) {
      if (!m.testinput) {
        alert('Add meg a teszt inputot!')
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
    },
    async pickupTask (task) {
      const name = prompt('A feladathoz add meg az új géped nevét!')
      if (name) {
        axios.post('createproject', { name: name, task: task.id })
        this.$store.dispatch('auth/fetchUser')
        this.synchTasks()
      }
    }
  }
}
</script>
