<template>
    <div id="MyApp">
        <div id="app">
            <!-- route outlet -->
            <!-- component matched by the route will render here -->
            <div  v-if="isRouterLoaded">
                <router-view></router-view>
                <loader></loader>
            </div>
        
            <div class="loading-page" v-else>
                <loading-page></loading-page>
            </div>
        </div>
    </div>
</template>
<script>
import LoadingPage from "./components/layouts/LoadingPage"
import Loader from "./components/shared/Loader"
import {mapGetters} from 'vuex'
export default {
    
    components: { 
      LoadingPage,
      Loader
    },
    computed:{
        isRouterLoaded: function() {
          if (this.$route.name !== null) return true
          return false
        },
    },
    created() {
    },
    methods:{
        
    }
}
</script>
<style lang="scss" scoped>
  *{
    text-transform: none !important;
    font-family:'Quicksand', sans-serif  !important; 
    font-size: 12px !important;
    }
    html{
        
    font-size: 12px !important;
    }
    /* width */

</style>