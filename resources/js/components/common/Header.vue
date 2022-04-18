<template>
  <div class="mb-10  md:mb-12">
    <h2 class="max-w-lg mb-2 font-sans text-3xl font-bold leading-none tracking-tight text-gray-900 sm:text-4xl ml-0">
      <span class="relative inline-block z-0">
        <svg viewBox="0 0 52 24" fill="currentColor" class="absolute top-0 left-0 z-0 hidden w-32 -ml-5 text-blue-gray-100 lg:w-32 lg:-ml-15 lg:-mt-6 sm:block">
          <defs>
            <pattern id="84d09fa9-a544-44bd-88b2-08fdf4cddd38" x="0" y="0" width=".135" height=".30">
              <circle cx="1" cy="1" r=".7" />
            </pattern>
          </defs>
          <rect fill="url(#84d09fa9-a544-44bd-88b2-08fdf4cddd38)" width="52" height="24" />
        </svg>
        <span class="relative z-0">{{ title }}</span>
      </span>
    </h2>
    <p class="text-base text-gray-700 md:text-md">
      {{ description }}
    </p>
  </div>
</template>

<script>
export default {
  name: 'VHeader',

  props: {
    title: {
      type: String,
      default: ''
    },
    description: {
      type: String,
      default: ''
    }
  }
}
</script>
