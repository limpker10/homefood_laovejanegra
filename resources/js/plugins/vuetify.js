// Vuetify Documentation https://vuetifyjs.com

import Vue from 'vue'
//import Vuetify from 'vuetify/lib/framework'
import Vuetify from 'vuetify'
import ripple from 'vuetify/lib/directives/ripple'


import esPeru from 'vuetify/es5/locale/es'

Vue.use(Vuetify, { directives: { ripple } })

const theme = {
  primary: '#AD91FD',
  secondary: '#6D6D6D',
  accent: '#B3AAEB',
  info: '#4ED0E1',
  success: '#4CAF50',
  warning: '#FB8C00',
  error: '#FF5252',
}

Vue.component('my-app', {
  methods: {
    changeLocale () {
      this.$vuetify.lang.current = 'es'
    },
  },
})

export default new Vuetify({
  breakpoint: { mobileBreakpoint: 960 },
  icons: {
    values: { expand: 'mdi-menu-down' },
  },
  font: {
    family: 'Quicksand'
  },
  theme: {
    themes: {
      dark: theme,
      light: theme,
    },
  },
  lang:{
    locales: { esPeru },
    current: 'esPeru',
  }
})
