<template>
  <!--
    itt található a gép komponens összes eleme, ennek használatával tetszőleges számú gépet hozhatunk létre kód duplikálása nélkül
    Az alap struktúra (legalábbis a template) szinte változatlan, csak a kebab menü és a gép / szabály választó menü került bele
  -->
  <div>
    <v-container v-if="config" fluid style="max-width: 1500px">
      <div class="d-flex justify-space-between align-center flex-wrap">
        <h1>
          {{ name }}
        </h1>
        <span style="display: none">
          {{ szabalyok }}
          {{ actupdate }}
        </span>
        <v-text-field v-model="$store.state.machine.startingValue" label="Input" outlined dense hide-details style="max-width: 300px" class="my-2" />
        <!-- error message show section -->
        <v-alert v-if="errorMessage" dismissible class="warning--text">
          {{ errorMessage }}
        </v-alert>
      </div>
      <div class="d-flex justify-space-between align-center py-2 flex-wrap">
        <div class="d-flex align-center">
          <div class="d-flex align-center py-2">
            <v-text-field v-model="kezdoallapot" label="Kezdőállapot" outlined dense hide-details style="max-width: 150px" class="mx-1" />
            <v-text-field v-model="vegallapot" label="Végállapot" outlined dense hide-details style="max-width: 150px" class="mx-1" />
            <v-text-field v-model="maxIterations" label="Maximum iteráció" outlined dense hide-details class="mx-1" style="max-width: 150px" type="number" />
          </div>

          <v-text-field v-model="newsymbol" label="Új szimbólum hozzáadása" outlined dense hide-details style="max-width: 360px" maxlength="1" class="ma-1" @keyup.enter="addnewsymbol" />
          <v-btn outlined class="ma-1 rounded-sm" color="primary" icon @click="addnewsymbol">
            <v-icon>mdi-plus</v-icon>
          </v-btn>
        </div>
        <div class="d-flex align-center flex-wrap">
          <input ref="inputFile" type="file" hidden accept="application/json" @change="importFile">
          <v-btn small class="ma-1" color="info" @click="$refs.inputFile.click()">
            Import
          </v-btn>
          <v-btn small class="ma-1" color="info" @click="exportJson">
            Export
          </v-btn>
          <!-- <v-btn small outlined class="ma-1" color="warning" @click="reset()">
            Reset
          </v-btn> -->
          <v-btn small outlined class="ma-1" color="purple" @click="hideTrash = !hideTrash">
            {{ hideTrash ? "Show all" : "Clean mode" }}
          </v-btn>
        </div>
      </div>
      <v-form ref="form" lazy-validation>
        <v-card class="my-4">
          <form ref="form" action="">
            <v-simple-table class="custom-table">
              <template #default>
                <thead>
                  <tr>
                    <th class="text-left text-h6 font-weight-bold" style="width: 50px">
                      Állapotok
                    </th>
                    <th v-for="(value, key) in symbolList" :key="'symbol' + key" class="text-center text-h6 font-weight-bold">
                      {{ key }}
                      <v-btn class="ma-1 rounded-sm" color="red" icon x-small @click="removeSymbol(key)">
                        <v-icon>mdi-trash-can-outline</v-icon>
                      </v-btn>
                    </th>
                    <th />
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(szabaly, szabalyneve) in szabalyok" :key="'row_' + szabalyneve" :class="szabalyneve === actstate ? 'yellow' : ''">
                    <td class="text-center pa-0" style="width: 50px">
                      <div class="text-h6">
                        {{ szabalyneve }}
                      </div>
                    </td>

                    <td v-for="(alelem, val) in szabaly" :key="'sd' + szabalyneve + '_' + val" :class="szabalyneve === actstate && val === actchar ? 'orange' : ''">
                      <div :key="'sdx' + szabalyneve + '_' + val" class="d-flex align-center">
                        <div v-if="alelem.machine !== true" class="d-flex align-center">
                          <v-select v-model="alelem.uj_allapot" label="Új állapot" outlined dense :rules="[v => !!v || '', v => stateList.includes(v) || '']" hide-details color="red" :items="allapotList" style="width: 110px" class="mx-1" />

                          <!-- <v-select label="Irány/érték" outlined dense hide-details v-model="alelem.uj_ertek" :error="(!alelem.uj_ertek || alelem.uj_ertek === '') && !alelem.irany" :items="ertekList" v-if="!hideTrash || alelem.irany" style="width: 110px" class="mx-1" @change="clearOther(alelem, 'irany', alelem.uj_ertek)"></v-select> -->

                          <v-select v-if="!hideTrash || alelem.irany" v-model="alelem.uj_ertek" label="Új érték" outlined dense hide-details :error="(!alelem.uj_ertek || alelem.uj_ertek === '') && !alelem.irany" :items="ertekList" style="width: 110px" class="mx-1" @change="clearOther(alelem, 'irany', alelem.uj_ertek)" />
                          <v-select v-if="!hideTrash || alelem.uj_ertek" v-model="alelem.irany" label="Irány" outlined dense hide-details :error="!alelem.uj_ertek && !alelem.irany" :items="selectOptions" item-text="text" item-value="value" style="width: 110px" class="mx-1" @change="clearOther(alelem, 'uj_ertek', alelem.irany)" />
                        </div>
                        <div v-else style="display: flex">
                          <v-select v-model="alelem.machineID" label="Válassz gépet" width="130px" style="max-width: 160px; width: 100%" outlined dense hide-details :items="machinelist" item-value="id" item-text="name" class="mx-1" @change="changeMachine()" />
                          <v-select v-model="alelem.uj_allapot" label="Új állapot" outlined dense :rules="[v => !!v || '', v => stateList.includes(v) || '']" hide-details :items="allapotList" style="width: 110px" class="mx-1" />
                        </div>
                        <v-spacer />

                        <v-menu offset-y>
                          <template #activator="{ on, attrs }">
                            <v-btn icon v-bind="attrs" v-on="on">
                              <v-icon>mdi-dots-vertical</v-icon>
                            </v-btn>
                          </template>
                          <v-list style="padding-top: 0">
                            <v-list-item style="background: #dadada">
                              Típus
                            </v-list-item>
                            <v-list-item link @click="useMachine(szabalyneve, val, null)">
                              Szabály
                            </v-list-item>
                            <v-list-item link @click="useMachine(szabalyneve, val, true)">
                              Gép
                            </v-list-item>
                          </v-list>
                        </v-menu>
                      </div>
                    </td>
                    <td class="text-center py-4">
                      <v-btn small color="error" icon @click="removeRule(szabalyneve)">
                        <v-icon>mdi-delete</v-icon>
                      </v-btn>
                    </td>
                  </tr>
                  <tr v-show="!hideTrash" class="green lighten-5">
                    <td class="text-h6 text-center green lighten-5">
                      <v-text-field v-model="newrulestate" outlined dense hide-details style="max-width: 120px" class="mx-1" />
                    </td>
                    <td v-for="(key, idx) in symbols" :key="'last' + idx">
                      <div v-if="newrule[key]" class="d-flex align-center">
                        <div class="d-flex align-center" style="width: 100%">
                          <div v-if="newrule[key].machine !== true" class="d-flex align-center">
                            <v-select v-model="newrule[key].uj_allapot" label="Új állapot" outlined dense hide-details :items="allapotList" style="width: 110px" class="mx-1" />
                            <v-select v-if="!hideTrash || newrule[key].irany" v-model="newrule[key].uj_ertek" label="Új érték" outlined dense hide-details :items="ertekList" style="width: 110px" class="mx-1" @change="clearOther(alelem, 'irany', alelem.uj_ertek)" />
                            <v-select v-model="newrule[key].irany" label="Irány" outlined dense hide-details :items="selectOptions" item-text="text" item-value="value" style="max-width: 110px" class="mx-1" />
                          </div>
                          <div v-else style="display: flex">
                            <v-select v-model="newrule[key].machineID" label="Válassz gépet" width="130px" style="max-width: 200px; width: 100%" outlined dense hide-details :items="machinelist" class="mx-1" @change="changeMachine()" />
                            <v-select v-model="newrule[key].uj_allapot" label="Új állapot" outlined dense hide-details :items="allapotList" style="width: 110px" class="mx-1" />
                          </div>
                          <v-spacer />
                          <v-menu offset-y>
                            <template #activator="{ on, attrs }">
                              <v-btn icon v-bind="attrs" v-on="on">
                                <v-icon>mdi-dots-vertical</v-icon>
                              </v-btn>
                            </template>
                            <v-list style="padding-top: 0">
                              <v-list-item style="background: #dadada">
                                Típus
                              </v-list-item>
                              <!-- az alábbival tudunk választani szabály és gép között -->
                              <!-- választáskoz meghívjuk a useMachineNewRecord függvényt, ami megadja az adott állapotnak, hogy gépre hivatkozunk -->
                              <v-list-item
                                link
                                @click="
                                  newrule[key].machine = false;
                                  useMachineNewRecord();
                                "
                              >
                                Szabály
                              </v-list-item>
                              <v-list-item
                                link
                                @click="
                                  newrule[key].machine = true;
                                  useMachineNewRecord();
                                "
                              >
                                Gép
                              </v-list-item>
                            </v-list>
                          </v-menu>
                        </div>
                      </div>
                    </td>
                    <td style="padding: 0 2px">
                      <v-btn class="add" small color="info" @click="addRule">
                        Hozzáad
                      </v-btn>
                    </td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </form>
        </v-card>
      </v-form>
    </v-container>
    <v-snackbar :value="message" timeout="2000" top right>
      {{ message }}
    </v-snackbar>
  </div>
</template>

<script>
// import axios from 'axios'
// @ is an alias to /src
// import defaultRuleset from '../static/defaultRuleset.json'

export default {
  name: 'App',
  // itt találhatók a komponensnek átadott propertyk, ezeket a Home.vue view-ban adjuk meg neki
  props: ['id', 'name', 'machinelist', 'config', 'renderStep', 'actchar', 'actstate'],

  // in this section we define all the instance variables, that can be used anywhere within this app
  data: function () {
    return {
      selectOptions: [
        { text: 'Jobbra', value: -1 },
        { text: '---', value: null },
        { text: 'Balra', value: 1 }
      ],
      maxIterations: 30,
      fatalErrorCaught: false,
      runningRule: null,
      runningVal: null,
      newrulestate: null,
      newrule: {},
      newsymbol: '',
      hideTrash: false,
      symbols: [],
      kezdoallapot: 's',
      vegallapot: 'h',
      message: '',
      errorMessage: '',
      // this is only to hack to show the running turing steps delayed..
      timingVariable: 0,
      speed: 200,
      result: '',
      bemenet: null,
      szabalyok: [],
      actupdate: null,
      localstep: 0,
      disabledStepper: false,
      backendResult: []
    }
  },
  computed: {
    actPos: {
      get () {
        return this.$store.state.machine.actPos
      },
      set (val) {
        this.$store.commit('machine/setActPos', val)
      }
    },
    stateList () {
      // we need to add "vegallapot" manually as it will never be in the ruleset
      return Object.keys(this.szabalyok).concat(this.vegallapot)
    },
    symbolList () {
      return this.szabalyok[Object.keys(this.szabalyok)[0]]
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
    }
  },

  watch: {
    szabalyok: {
      deep: true,
      handler (val) {
        // window.localStorage.setItem('szabalyok' + this.id, JSON.stringify(val))
        this.$emit('onConfigChange', val)
      }
    }
  },
  async mounted () {
    // this.$store.state.machine.manualStepCount = 0;
    // amikor mountoljuk a gépet, beolvassuk a megfelelő szamályt localstorageból ()
    // if (window.localStorage.getItem('szabalyok' + this.id) != null) {
    //   this.szabalyok = JSON.parse(localStorage.getItem('szabalyok' + this.id))
    // } else {
    //   this.szabalyok = this.config
    // }
    this.szabalyok = this.config
    // késleltetve megnézzük hogy validak-e a mezőink
    setTimeout(() => {
      if (this.$refs.form) this.$refs.form.validate()
    }, 1000)

    // itt nincs változás
    this.alignNewrecord()
    setTimeout(() => {
      this.checkError()
    }, 1000)
  },
  methods: {

    clearOther (alelem, key, value) {
      if (value != null) {
        alelem[key] = null
      }
    },
    useMachine (i1, i2, value) {
      this.szabalyok[i1][i2].machine = value
      this.actupdate = Math.floor(Math.random() * 100)

      // console.log(this.actupdate);
      this.showMessage('Módosítás mentve')
      window.localStorage.setItem('szabalyok' + this.id, JSON.stringify(this.szabalyok))
      this.szabalyok = null
      this.szabalyok = JSON.parse(window.localStorage.getItem('szabalyok' + this.id))
      // window.location.reload();
    },
    useMachineNewRecord () {
      const temp = this.newrule
      this.newrule = {}
      this.newrule = temp
      // window.location.reload();
    },
    // csak teszteléshez kellett
    changeMachine () {
      // window.location.reload();
    },
    removeRule (szab) {
      // delete row using delete key from object
      const res = Object.assign({}, this.szabalyok)
      delete res[szab]
      this.szabalyok = res

      this.showMessage('Törölve')
      window.localStorage.setItem('szabalyok' + this.id, JSON.stringify(this.szabalyok))
    },

    scroll () {
      setTimeout(function () {
        const element = document.getElementById('rescontainer')
        element.scrollTop = element.scrollHeight + 500
      }, 200)
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
      // window.location.reload()
    },
    showMessage (txt) {
      this.message = txt
    },

    alignNewrecord () {
      this.symbols = Object.keys(this.szabalyok).length > 0 ? Object.keys(this.szabalyok[Object.keys(this.szabalyok)[0]]) : ['0', '1', '#']
      this.symbols.forEach(sym => {
        this.newrule[sym] = {}
      })
    },
    removeSymbol (key) {
      const remove = confirm("Biztosan töröljük a '" + key + "' szimbólumot?")
      if (remove) {
        this.symbols.splice(this.symbols.indexOf(key), 1)
        Object.keys(this.szabalyok).forEach(element => {
          delete this.szabalyok[element][key]
          // console.log(this.szabalyok[element]);
        })
        delete this.newrule[key]
        window.localStorage.setItem('szabalyok' + this.id, JSON.stringify(this.szabalyok))
      }
    },
    addnewsymbol () {
      if (this.newsymbol == null || this.newsymbol === '') {
        alert('Blank symbol cannot be added')
        return false
      }

      if (Object.keys(this.symbolList).indexOf(this.newsymbol) > -1) {
        alert('This symbol has already been added in the list')
        this.newsymbol = ''
        return false
      }

      Object.keys(this.szabalyok).forEach(element => {
        this.szabalyok[element][this.newsymbol] = {}
        console.log(this.szabalyok[element])
      })
      this.symbols.push(this.newsymbol)
      window.localStorage.setItem('szabalyok' + this.id, JSON.stringify(this.szabalyok))
      this.szabalyok = JSON.parse(localStorage.getItem('szabalyok' + this.id))
      this.showMessage('Hozzáadva')
      this.alignNewrecord()
      this.newsymbol = ''
    },
    addRule () {
      if (!this.newrulestate) {
        alert('Nincs név megadva')
        return
      }
      this.szabalyok[this.newrulestate] = this.newrule
      this.newrule = {}
      this.newrulestate = ''
      this.showMessage('Hozzáadva')
      this.alignNewrecord()
      window.localStorage.setItem('szabalyok' + this.id, JSON.stringify(this.szabalyok))
      this.$nextTick(() => {
        this.checkError()
      })
    },
    checkError () {},
    exportJsonAllData () {
      const toSave = {}
      for (const key in localStorage) {
        if (key.indexOf('szabalyok') > -1 || key.indexOf('machines') > -1) {
          toSave[key] = localStorage.getItem(key)
        }
      }
      // create link element for download json
      const element = document.createElement('a')
      // file name with date
      const filename = `turing_${new Date().toJSON().slice(0, 10)}.json`
      // convert json to uricomponent and click to download

      element.setAttribute('href', 'data:text/plain;charset=utf-8,' + toSave)
      element.setAttribute('download', filename)
      // not to display created link
      element.style.display = 'none'
      document.body.appendChild(element)
      element.click()
      document.body.removeChild(element)
    },
    exportJson () {
      let toSave = null
      for (const key in localStorage) {
        if (key.indexOf('szabalyok' + this.id) > -1) {
          toSave = localStorage.getItem(key)
        }
      }
      // create link element for download json
      const element = document.createElement('a')
      // file name with date

      const filename = `turing_${this.$store.state.machine.machines[this.id]}_${new Date().toJSON().slice(0, 10)}.json`
      // convert json to uricomponent and click to download

      element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(toSave))
      element.setAttribute('download', filename)
      // not to display created link
      element.style.display = 'none'
      document.body.appendChild(element)
      element.click()
      document.body.removeChild(element)
    },
    importFile (event) {
      // on import button click after change file read the file and parse it to our variable and save
      const file = event.target.files[0]
      const reader = new FileReader()
      let toRestore = {}
      reader.onload = e => {
        toRestore = e.target.result

        window.localStorage.setItem('szabalyok' + this.id, toRestore)

        this.alignNewrecord()
        location.reload()
      }
      reader.readAsText(file)
    },
    importFileAllData (event) {
      // on import button click after change file read the file and parse it to our variable and save
      const file = event.target.files[0]
      const reader = new FileReader()
      let toRestore = {}
      reader.onload = e => {
        toRestore = JSON.parse(e.target.result)

        for (const key in toRestore) {
          window.localStorage.setItem(key, toRestore[key])
        }

        this.alignNewrecord()
        location.reload()
      }
      reader.readAsText(file)
    }
  }
}
</script>
<style lang="scss">
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
.v-select--outlined >>> fieldset {
  border-color: rgba(192, 0, 250, 0.986);
}
.v-application p {
  margin-bottom: 0;
  line-height: 0px;
}
td {
  padding: 0 2px !important;
}
</style>
