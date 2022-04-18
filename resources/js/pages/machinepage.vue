<template>
  <div>
    <v-btn v-if="task" class="bottom-0" color="orange" style="position:fixed" dark @click="showTask()">
      Feladat részletei
    </v-btn>
    <v-container fluid style="max-width: 1500px">
      <div style="border-bottom: 2px solid #1976d2" class="flex justify-between">
        <div>
          <v-btn
            v-for="(item, index) in $store.state.machine.machines"
            :key="item.id"
            :color="$store.state.machine.activeMachine === index ? 'primary' : 'grey-lighten'"
            style="
            text-transform: none;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            margin-right: 10px;
            margin-left: 0px;
          "
            @click="
              $store.state.machine.activeMachine = index;
              setPageLocalstorage(item);
            "
            @contextmenu="showContextmenu(item)"
          >
            {{ item.name }}
            <v-menu v-if="item" v-model="item.showMenu" :position-x="x" :position-y="y" absolute offset-y>
              <template #activator="{ on }">
                <v-btn icon :color="$store.state.machine.activeMachine === index ? 'white' : 'grey-darken-6'" v-on="on" @click="openMenu(item)">
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
              </template>

              <v-list>
                <v-list-item @click="renameMachine">
                  <v-list-item-title> Átnevezés </v-list-item-title>
                </v-list-item>
                <v-list-item @click="removeMachine">
                  <v-list-item-title><span style="color: red"> Törlés</span> </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </v-btn>
          <v-btn
            @click="
              newmachineName = null;
              newmachineDialog = true;
            "
          >
            +
          </v-btn>
        </div>
        <div class="inline-flex">
          <div class="self-end mr-3">
            Publikálás
          </div>
          <div class="self-top">
            <v-switch v-model="fullRecord.public " hide-details="" @change="setPublic()">
              <template #input="{ on, off }">
                <v-checkbox v-model="$store.state.machine.public" :on-color="on" :off-color="off" />
              </template>
            </v-switch>
          </div>
        </div>
      </div>
      <v-tabs-items v-model="$store.state.machine.activeMachine">
        <v-tab-item v-for="(item, tabindex) in $store.state.machine.machines" :key="'tab' + tabindex" :transition="false" :reverse-transition="false">
          <v-card flat>
            <Machine v-if="item.config" :id="tabindex" :actchar="actChar" :actstate="actState" :config="item.config" :name="item.name" :machinelist="$store.state.machine.machines.filter(el => el != item)" :render-step="renderStep" @onConfigChange="(val)=>saveConfig(val,tabindex)" />
          </v-card>
        </v-tab-item>
      </v-tabs-items>
      <div class="d-flex flex-wrap">
        <v-btn
          color="primary"
          large
          class="ma-5"
          @click="
            //$store.state.machine.startingMachine = $store.state.machine.activeMachine;
            //$store.state.machine.printableResult = '';
            $store.state.machine.isStepper = true;
            $store.state.machine.manualStepCount++;
            $store.state.machine.autoStepcount = 0;
            runTuring(1);
          "
        >
          Léptet ({{ globalIteration }})
        </v-btn>
        <v-btn
          v-if="!$store.state.machine.isStepper"
          large
          dark
          color="green"
          class="ma-5"
          @click="
            //$store.state.machine.startingMachine = $store.state.machine.activeMachine;
            $store.state.machine.printableResult = '';
            $store.state.machine.iterationCount = 0;
            $store.state.machine.autoStepcount = 0;
            $store.state.machine.tempValue = $store.state.machine.startingValue;
            runTuring(2);
          "
        >
          Futtatás
        </v-btn>
        <v-btn
          v-else
          large
          color="primary"
          class="ma-5"
          @click="
            globalIteration = 0;
            $store.state.machine.autoStepcount = 0;
            //$store.state.machine.startingMachine = $store.state.machine.activeMachine;
            $store.state.machine.iterationCount = $store.state.machine.manualStepCount;
            $store.state.machine.isStepper = false;
            runTuring(3);
          "
        >
          Futtatás
        </v-btn>
        <v-btn large dark color="red" class="ma-5" @click="$store.state.machine.stopped = true">
          Stop
        </v-btn>
        <v-btn large color="warning" class="ma-5" @click="resetDisplay()">
          Kijelző reset
        </v-btn>
        <div style="flex: 1; min-width: 300px">
          <v-card dark height="300px">
            <v-card-text id="rescontainer" class="card-box">
              <div class="result" v-html="$store.state.machine.printableResult" />
            </v-card-text>
          </v-card>
        </div>
      </div>
    </v-container>

    <!-- A v-dialog egy popupot hoz létre, a popupban meg tudjuk adni az új gép nevét -->
    <v-dialog v-model="newmachineDialog" max-width="290">
      <v-card>
        <v-card-title class="text-h5">
          Új gép hozzáadása
        </v-card-title>
        <v-card-text>
          <v-text-field v-model="newmachineName" outlined autofocus @keyup.enter="addNewMachine()" />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <!-- AddNewMachine függvény: részletek lejebb -->
          <v-btn color="green darken-1" text @click="addNewMachine()">
            Mentés
          </v-btn><v-btn color="gray darken-1" text @click="newmachineDialog = false">
            bezár
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- A v-dialog egy popupot hoz létre, a popupban meg tudjuk adni az új gép nevét -->
    <v-dialog v-model="renameMachineDialog" persistent max-width="290">
      <v-card>
        <v-card-title class="text-h5">
          Új gép átnevezése
        </v-card-title>
        <v-card-text>
          <v-text-field v-model="machineName" outlined />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="green darken-1" text @click="updateName()">
            Mentés
          </v-btn><v-btn color="gray darken-1" text @click="renameMachineDialog = false">
            bezár
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-if="task" v-model="showTaskDialog" persistent max-width="490">
      <v-card>
        <v-card-title class="text-h5">
          {{ task.name }}
        </v-card-title>
        <v-card-text>
          {{ task.description }}
          <br>
          <hr>
          Inputok: {{ task.IO }}
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="gray darken-1" text @click="showTaskDialog = false">
            bezár
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="showPublicDialog" persistent max-width="490">
      <v-card>
        <v-card-title>
          {{ fullRecord.public? 'A gépet publikáltuk': 'Publikálás megszünetve' }}
        </v-card-title>
        <v-card-actions>
          <v-spacer />
          <v-btn color="gray darken-1" text @click="showPublicDialog = false">
            bezár
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
// beimportáljuk a machine komponenst
import axios from 'axios'
import Machine from '../components/machine.vue'
export default {
  name: 'App',
  // itt mondjuk meg az appnak, hogy a fent említett Machine komponensként lesz használva
  components: {
    Machine
  },
  middleware: 'auth',
  // in this section we define all the instance variables, that can be used anywhere within this app
  data: function () {
    return {
      backendResult: [],
      showMenu: null,
      showTaskDialog: false,
      renameMachineDialog: false,
      oldmachineName: null,
      machineName: null,
      newmachineName: null,
      newmachineDialog: false,
      more: ['Új gép hozzáadása'],
      tab: null,
      text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
      x: 0,
      y: 0,
      machines: [],
      newmachines: [],
      renderStep: {},
      actChar: '',
      actState: '',
      prevMachine: null,
      task: {},
      fullRecord: {},
      showPublicDialog: false
    }
  },
  computed: {
    stateList () {
      // we need to add "vegallapot" manually as it will never be in the ruleset
      return Object.keys(this.szabalyok).concat(this.vegallapot)
    },
    symbolList () {
      return this.szabalyok[Object.keys(this.szabalyok)[0]]
    },
    actPos: {
      get () {
        return this.$store.state.machine.actPos
      },
      set (val) {
        this.$store.commit('machine/setActPos', val)
      }
    },

    allapotList () {
      return Object.keys(this.szabalyok).concat([this.vegallapot])
    },
    ertekList () {
      return Object.keys(this.symbolList)
    },
    globalIteration: {
      get: function () {
        return this.$store.state.machine.iterationCount
      },
      set: function (val) {
        this.$store.commit('machine/setIterationCount', val)
      }
    },
    cMachines () {
      return this.$store.state.machine.machines
    }
  },

  watch: {
    machines: {
      deep: true,
      handler () {
        // window.localStorage.setItem('machines', JSON.stringify(this.machines))
      }
    }
  },
  async mounted () {
    this.$store.state.machine.machines = []
    await axios.get('/configuration?id=' + this.$route.params.id).then(response => {
      // this.newmachines = response.data
      try {
        this.$store.state.machine.machines = JSON.parse(response.data.data)
        this.task = response.data.task
        this.fullRecord = response.data
      } catch (e) {
        this.$store.state.machine.machines = []
      }
      this.$store.state.machine.activeMachine = 0
    })
    // this.$store.state.machine.automated = false
    // az utoljára megnyitott tabot nyitjuk meg újranyitáskor (nyit * 3 :D )
    // this.$store.state.machine.activeMachine = Number(window.localStorage.getItem('currpage'))

    // page loadon előszedjük az összes gépet is local storageból
    // if (window.localStorage.getItem('machines')) {
    //   this.machines = JSON.parse(window.localStorage.getItem('machines'))

    //   console.log(this.machines)
    // }
  },
  methods: {
    async runTuring (type = null) {
      this.$store.state.machine.stopped = false
      if (this.backendResult.length === 0) {
        const response = await axios.post('/processing', { data: this.$store.state.machine.machines })
        this.backendResult = response.data
      }
      if (type !== 1) {
        this.$store.state.machine.printableResult = ''
        this.globalIteration = 0

        for (const step of this.backendResult) {
          if (!this.$store.state.machine.stopped) {
            await this.visualize(step, 100)
          }
        }
      } else {
        await this.visualize(this.backendResult[this.globalIteration], 0)
      }
    },
    visualize (step, timeout) {
      // promise
      return new Promise(resolve => {
        if (this.prevMachine !== step.machine && this.prevMachine !== null) {
          timeout = timeout + 1000
          this.$store.state.machine.activeMachine = (step.machine)
        }

        setTimeout(() => {
          this.renderStep = step
          let s = step.input
          this.actState = step.state
          const index = step.input.length - step.cursor - 1
          this.actChar = s.charAt(index)
          s = s.substring(0, index) + '<b>' + this.actChar + '</b>' + s.substring(index + 1)

          this.$store.state.machine.printableResult += `<div style="line-height:5px">

          <i style='width:40px;display:inline-block;text-align:right'>${this.globalIteration + 1}</i>
          <i style='width:100px;display:inline-block'>${this.cMachines.find((el) => el.id - 1 === step.machine).name}</i>
          ${s} - ${step.state}</div>`

          this.globalIteration++
          this.prevMachine = step.machine
          resolve('vizualizalva')
        }, timeout)
      })
    },
    sleep (timeout) {
      // promise
      return new Promise(resolve => {
        setTimeout(() => {
          resolve('wake up')
        }, timeout)
      })
    },
    reset () {
      for (const key in localStorage) {
        if (key.indexOf('szabalyok') > -1 || key.indexOf('machines') > -1) {
          window.localStorage.removeItem(key)
        }
      }

      window.location.reload()
    },
    resetDisplay () {
      this.$store.state.machine.printableResult = ''
      this.globalIteration = 0
      this.backendResult = []
    },
    showMessage (txt) {
      this.message = txt
    },

    async saveConfig (data, index) {
      const allmachines = this.$store.state.machine.machines
      allmachines[index].config = data
      await axios.post('/saveconfig', { id: this.$route.params.id, data: allmachines })
    },
    updateName () {
      const elementIndex = this.$store.state.machine.machines.indexOf(this.oldmachineName)
      this.$store.state.machine.machines[elementIndex] = this.machineName
      this.renameMachineDialog = false
    },
    renameMachine () {
      this.renameMachineDialog = true
    },
    removeMachine () {
      console.log(this.$store.state.machine.machines)
      this.$store.state.machine.machines.splice(this.$store.state.machine.machines.indexOf(this.oldmachineName), 1)
    },
    showContextmenu (name) {
      this.oldmachineName = name
      this.machineName = name
      event.preventDefault()
      this.showMenu = false
      this.x = event.clientX
      this.y = event.clientY
      this.$nextTick(() => {
        this.showMenu = true
      })
    },
    addNewMachine () {
      // ezt a változót false-ra állítva eltűntetjük a popupot
      // this.newmachineDialog = false;

      // itt vizsgáljuk, hogy létezik-e már a megadott gép (case-sensitive --> ha naggyal van felvéve, kisbetűvel engedni fogja)

      if (this.$store.state.machine.machines.indexOf(this.newmachineName) > -1) {
        alert('Machine name already exist.')
        return false
      }

      // ezután az új gép nevét bepusholjuk egy tömbbe, ennek eredményeképp már meg is fog jelenni az új tabunk
      this.$store.state.machine.machines.push(this.newmachineName)

      // az utolsó hozzáadott gépre fókuszálunk - ezt épp "most" adtuk az apphoz
      this.$store.state.machine.activeMachine = this.$store.state.machine.machines.length - 1

      this.newmachineName = ''
      // el is mentjük local storage-ba, hogy mely gépen állunk jelenleg (újranyitáskor ezt nyitja az app automatikusan)
      this.setPageLocalstorage()
    },
    setPageLocalstorage () {
      setTimeout(() => {
        window.localStorage.setItem('currpage', this.$store.state.machine.activeMachine)
      }, 200)
    },
    openMenu (name) {
      this.oldmachineName = name
      this.machineName = name
      event.preventDefault()
      this.showMenu = false
      this.x = event.clientX
      this.y = event.clientY
      this.$nextTick(() => {
        this.showMenu = true
      })
    },
    showTask () {
      this.showTaskDialog = true
    },
    async setPublic () {
      // if (!this.fullRecord.public) {
      //   this.fullRecord.public = true
      // } else {
      //   this.fullRecord.public = false
      // }
      await axios.post('/setpublic', { id: this.$route.params.id, public: this.fullRecord.public ? 1 : 0 })
      this.showPublicDialog = true
    }
  }
}
</script>
<style lang="scss">
/* alap CSS szabályok */
.custom-table {
  .v-label {
    font-size: 13px;
  }

  .v-label.v-label--active {
    font-size: 14px;
  }

  td,
  th {
    border-left: 0.5px solid #e0e0e0 !important;
    border-right: 0.5px solid #e0e0e0 !important;
  }

  td:first-child {
    border-color: #c6c1c1 !important;
    background-color: #e1e1e1;
  }
}

.card-box {
  max-height: 100%;
  overflow-y: auto;

  &::-webkit-scrollbar {
    width: 5px;
  }

  /* Track */
  &::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  /* Handle */
  &::-webkit-scrollbar-thumb {
    background: #888;
  }

  /* Handle on hover */
  &::-webkit-scrollbar-thumb:hover {
    background: #555;
  }

  .result {
    .nth {
      padding: 10px;
    }

    i {
      padding: 5px;
    }

    b {
      color: red !important;
    }
  }
}
</style>
