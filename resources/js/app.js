/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

 require('./bootstrap');

 //import './bootstrap'
 import moment from 'moment';
 import router from './router/router';
 import vuetify from './plugins/vuetify';
 import VueTheMask from 'vue-the-mask';
 import VueSweetalert2 from 'vue-sweetalert2';
 import VueToast from 'vue-toast-notification';
 
 import { abilitiesPlugin } from '@casl/vue';

 //PLUGINS
 import 'vuetify/dist/vuetify.min.css';
 import 'vue-toast-notification/dist/theme-sugar.css';
 import ability from './plugins/ability';


 //VUEX 
 import store from './store/index'

 window.Vue = require('vue').default;
 Vue.use(VueSweetalert2);
 Vue.use(VueTheMask);
 Vue.use(abilitiesPlugin,ability);
 Vue.use(VueToast);

   Vue.filter('formatDate', function(value) {
      if (value) {
         return moment(String(value)).format('YYYY-MM-DD')
      }
   });
   
   Vue.filter('formatTime', function(value) {
      if (value) {
         return moment(String(value)).format('LT')
      }
   });

   Vue.filter('formatDate2', function(value) {
      if (value) {
         return moment(String(value)).format('DD/MM/YYYY')
      }
   });
 
 /**
  * The following block of code may be used to automatically register your
  * Vue components. It will recursively scan this directory for the Vue
  * components and automatically register them with their "basename".
  *
  * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
  */
 
 // const files = require.context('./', true, /\.vue$/i)
 // files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
 
 Vue.component('my-app', require('./App.vue').default);
 
 /**
  * Next, we will create a fresh Vue application instance and attach it to
  * the page. Then, you may begin adding components to this application
  * or customize the JavaScript scaffolding to fit your unique needs.
  */
 
 const app = new Vue({
    el: '#app',
    router,
    vuetify,
    ability,
    store,
 });
 