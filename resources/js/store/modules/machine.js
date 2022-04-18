// import axios from 'axios'
// import Cookies from 'js-cookie'
// import * as types from '../mutation-types'

// state
export const state = {
  actPos: 0,
  isStepper: false,
  manualStepCount: 0,
  manualGlobal: 0,
  autoStepcount: 0,
  globalStepCount: 0,
  globalTape: [],
  stepperIteration: 0,
  stopped: false,
  // az aktuális aktív tab (gép) indexe
  currpage: 0,

  // itt tároljuk az épp aktív gép azonosítóját  - indexét
  activeMachine: 0,

  // a machine.vue komponensben bőven ki lesz fejtve
  automated: false,

  // startingMachine: 0,
  startingValue: '#1110#',
  tempValue: null,

  // a gép akruális "kurzorpozaíciója"
  turingPos: null,
  stepNum: 0,
  // globálisan tároljuk a debug szekció tartalmát, ez azért fontos, mert a gépek elszeparáltan működnek komponensben
  // így alap esetben ez mindig kiürülne, emiatt lett létrehozva ez a global state
  printableResult: '',

  // ebben tároljuk a hozzáadott gépek nevét (az indexüket fogjuk használni azonosítóként, tehát: machines[0] ==> gép1)
  machines: ['gép1', 'gép2', 'gép3'],

  // ezzen a tömbben tároljuk új gépre lépéskor az azt megelőző gép azonosítóját, és a megadott új állapotot
  // amikor egy hívott gép befejezi a munkát, akkor a tümb legutolsó eleméből kiolvassuk a fenti 2 infót, így biztosan tudjuk, hogy mivel folytassunk
  // az egész végén "lecsípjük"/töröljük -> (array.splice()) az utolsó elemet
  // a folyamat akkor ér véget biztosan, amikor ez a tömb üres, azaz tuti nincs már gép benne tárolva, ahova vissza kéne lépni
  progressArray: [],

  // stepback:false,

  // ez számolja az iterációk teljes számát (összeadva az összes gépét)
  iterationCount: 0
}

// getters
export const getters = {

}

// mutations
export const mutations = {
  setIterationCount (state, payload) {
    state.iterationCount = payload
  },
  setManualGlobal (state, payload) {
    state.manualGlobal = payload
  },
  setActPos (state, payload) {
    state.actPos = payload
  }
}

// actions
export const actions = {

}
