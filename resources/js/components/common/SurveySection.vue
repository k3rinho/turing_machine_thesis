<template>
  <div class="grid mb-12">
    <div class="bg-white shadow-lg hover:shadow-xl duration-500 px-2 sm:px-6 md:px-2 py-2">
      <div href="#" class="sm:text-sm md:text-md lg:text-lg text-black font-bold hover:underline">
        <div class="flex">
          <div class="h-7 w-7 bg-primary text-white text-center mr-2 rounded-full text-md font-normal">
            {{ num }}
          </div>
          <span class="text-2xl">{{ title }}</span>
        </div>
      </div>
      <div class="grid grid-cols-12 gap-3 border-2 border-gray-50">
        <div class="col-span-0 sm:col-span-3 sm:block self-bottom pt-6 border-gray-200 border-r-2">
          <div :class="`grid mx-auto mb-3 py-1 w-4/5 2lg:w-3/5 text-${cSurvey.progress == 100 ? 'green-700' : 'primary'} content-center align-middle`">
            <div class="flex w-full">
              <div class="flex flex-col w-full ml-2">
                <div class="inline-block font-medium text-sm lg:text-md  ">
                  Progress:
                </div>
                <div class="inline-block font-medium text-2xl ">
                  {{ cSurvey.progress }}%
                </div>
              </div>
            </div>
          </div>
          <div class="grid grid-rows-2">
            <button
              class="bg-primary hover:bg-gray-700 text-white font-bold mx-auto py-1 px-4 w-4/5"
              @click="openSurvey(num)"
            >
              Go to section >>
            </button>
          </div>
        </div>

        <!-- Summary Column -->
        <div class="col-span-12 sm:col-start-4 sm:col-end-14 px-3 sm:px-0 self-center ">
          <div class="col-span-12 lg:col-span-8">
            <span :class="`inline-block rounded-full text-white
               bg-${cStatusColor(cSurvey.status)}-600 hover:bg-${cStatusColor(cSurvey.status)}-800 duration-300
               text-xs font-bold
               mr-1 md:mr-2 mb-2 px-2 md:px-4 py-1
               opacity-90 hover:opacity-100`"
            >
              {{ cSurvey.progress == 100 ? 'Completed' : strStatus(cSurvey.status) }}
            </span>
          </div>

          <div class="mt-2">
            <p class="mt-2 text-gray-600 text-sm md:text-md">
              Lorem ipsum dolor sit, amet consectetur adipisicing elit.
              Tempora expedita dicta totam totam gul koparam doloremque. Excepturi iste iusto eos enim
              reprehenderit nisi, accusamus...
            </p>
          </div>

          <!-- Question Labels -->
          <div class="grid grid-cols-2 mt-4 my-auto">
            <!-- Categories  -->
            <div>
              <button type="button" class="text-primary bg-white border focus:ring-0 border-primary hover:text-white hover:bg-gray-500 hover:border-gray-500 font-medium rounded-lg text-sm px-1 py-0.5 text-center mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Delegate this section
              </button>
            </div>
            <!-- User -->
            <div class="col-none hidden mr-2 lg:block lg:col-start-9 lg:col-end-12">
              <a href="#" class="flex items-center">
                <img src="/img/avatar.png" alt="avatar"
                     class="mr-2 w-8 h-8 rounded-full"
                >

                <div class="text-gray-600 font-bold text-sm hover:underline">
                  No delegation
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SurveySection',
  props: {
    progress: {
      type: Number,
      default: 0
    },
    title: {
      type: String,
      default: null
    },
    description: {
      type: String,
      default: null
    },
    num: {
      type: Number,
      default: null
    },
    statusColor: {
      type: String,
      default: 'orange'
    },
    status: {
      type: Number,
      default: 1
    },
    survey: {
      type: Object,
      default: null
    }
  },
  computed: {
    cSurvey () {
      return this.survey || { status: 0, progress: 0, statusColor: 'red' }
    }
  },
  methods: {
    openSurvey (num) {
      this.$emit('gotosurvey', num)
    },
    strStatus (num) {
      switch (num) {
        case 0:
          return 'Not started'
        case 1:
          return 'In progress'
        case 2:
          return 'Completed'
        default:
          return 'Not started'
      }
    },
    cStatusColor (num) {
      switch (num) {
        case 0:
          return 'red'
        case 1:
          return 'orange'
        case 2:
          return 'green'
        default:
          return 'red'
      }
    }
  }
}
</script>
