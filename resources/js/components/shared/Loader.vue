
<template>
    <v-dialog
      v-model="$store.state.loader"
      persistent
      width="200"
    >
      <v-card
        color="primary"
        dark
        class="align-center"
        style="display: flex;"
      >
        <v-card-text 
        color="white"
        class="text-center">
          Cargando...
          
          <v-progress-linear
            indeterminate
            color="white"
            class="mb-0"
          ></v-progress-linear>
          <!--<br>
          <hollow-dots-spinner style="margin: auto;" :animation-duration="1000" :size="100" :color="'#FFF'"/>
      
          <fulfilling-bouncing-circle-spinner style="margin: auto;" :animation-duration="1000" :size="100" :color="'#FFF'"/>-->
        </v-card-text>
      </v-card>
    </v-dialog>
</template>
<script>
import { FulfillingBouncingCircleSpinner } from 'epic-spinners'
import { HollowDotsSpinner } from 'epic-spinners'

export default ({
    components: {
        HollowDotsSpinner,
        FulfillingBouncingCircleSpinner 
    },
})
</script>

<style lang="scss" scoped>
.v-dialog:not(.v-dialog--fullscreen) {
    max-height: 90%;
    box-shadow: none !important;
}
</style>