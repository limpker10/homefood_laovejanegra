<template>
  <v-card class="d-flex align-center" color="rgb(255, 255, 255, 0.55)" flat :height="fullpage?'100vh':'10vh'" tile>
    <v-card
      class="d-flex justify-center"
      flat
      color="rgb(255, 255, 255, 0)"
      width="100%" 
      tile
      style="height: 100%; display: flex; align-items: center; margin: inherit;"
    >
      <!--<hollow-dots-spinner style="margin: auto;" :animation-duration="1000" :size="100" :color="'#4DB6AC'"/>-->
      <fulfilling-bouncing-circle-spinner style="margin: auto;" :animation-duration="1000" :size="100" :color="'#AD91FD'"/>
      <!--<img style="margin: auto;" src="assets/images/cook.gif" alt="">-->
    </v-card>
  </v-card>
</template>
<script>
import { FulfillingBouncingCircleSpinner } from 'epic-spinners'
import { HollowDotsSpinner } from 'epic-spinners'

export default {
  props: {
    fullpage: {
      type: Boolean,
      default: true,
    },
    kind:{
      type: String,
      default:'atom'
    }
  },
  components: {
    HollowDotsSpinner,
    FulfillingBouncingCircleSpinner 
  },
  data() {
    return {};
  },
};
</script>


<style scoped>
/**
 * Transition animation between pages
 */
.fade-enter-active,
.fade-leave-active {
  transition-duration: 0.2s;
  transition-property: opacity;
  transition-timing-function: ease;
}

.fade-enter,
.fade-leave-active {
  opacity: 0;
}
</style>
