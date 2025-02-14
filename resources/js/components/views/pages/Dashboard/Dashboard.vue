<template>
    <v-container fluid>
      <div>
        <v-row dense class="pa-4" style="padding-top: 0px !important;">
          <v-col sm="4" md="3" xl="3" class="pa-4">
            <v-card :color="'#512DA8'" ><!--#4DB6AC-->
              <v-list-item three-line>
                  <v-list-item-avatar tile size="80">
                      <v-img :src="'assets/images/room_free_256px.png'"></v-img>
                  </v-list-item-avatar>
                  <v-list-item-content>
                      <v-list-item-title class="headline mb-1 white--text text-center">
                          {{cards.disponible}}
                      </v-list-item-title>
                      <v-list-item-subtitle class="white--text text-center" style="font-size: 15px;">
                        Habitaciones Disponibles
                      </v-list-item-subtitle>
                  </v-list-item-content>
              </v-list-item>
            </v-card>
          </v-col>
          <v-col sm="4" md="3" xl="3" class="pa-4">
            <v-card :color="'#512DA8'" ><!--#E57373-->
              <v-list-item three-line>
                  <v-list-item-avatar tile size="80">
                      <v-img :src="'assets/images/room_occuped_256px.png'"></v-img>
                  </v-list-item-avatar>
                  <v-list-item-content>
                      <v-list-item-title class="headline mb-1 white--text text-center">
                          {{cards.ocupado}}
                      </v-list-item-title>
                      <v-list-item-subtitle class="white--text text-center" style="font-size: 15px;">
                        Habitaciones Ocupadas
                      </v-list-item-subtitle>
                  </v-list-item-content>
              </v-list-item>
            </v-card>
          </v-col>
          <v-col sm="4" md="3" xl="3" class="pa-4">
            <v-card :color="'#512DA8'" ><!--#80DEEA-->
              <v-list-item three-line>
                  <v-list-item-avatar tile size="80">
                      <v-img :src="'assets/images/room_clean_256px.png'"></v-img>
                  </v-list-item-avatar>
                  <v-list-item-content>
                      <v-list-item-title class="headline mb-1 white--text text-center">
                        {{cards.limpieza}}
                      </v-list-item-title>
                      <v-list-item-subtitle class="white--text text-center" style="font-size: 15px;">
                        Habitaciones en Limpieza
                      </v-list-item-subtitle>
                  </v-list-item-content>
              </v-list-item>
            </v-card>
          </v-col>
          <v-col sm="4" md="3" xl="3" class="pa-4">
            <v-card :color="'#512DA8'" ><!--#7b87cc-->
              <v-list-item three-line>
                  <v-list-item-avatar tile size="80">
                      <v-img :src="'assets/images/room_booking_256px.png'"></v-img>
                  </v-list-item-avatar>
                  <v-list-item-content>
                      <v-list-item-title class="headline mb-1 white--text text-center">
                        {{cards.reserva}}
                      </v-list-item-title>
                      <v-list-item-subtitle class="white--text text-center" style="font-size: 15px;">
                        Habitaciones Reservadas
                      </v-list-item-subtitle>
                  </v-list-item-content>
              </v-list-item>
            </v-card>
          </v-col>
          <v-col sm="12" md="7" xl="6" class="pa-4">
            <v-card class="pa-4" style="padding-top: 0px !important;">
              <v-app-bar flat dense color="rgba(0, 0, 0, 0)">
                    <v-toolbar-title v-text="'Ingresos vs. Egresos'"/>
              </v-app-bar>
              <charts-dashboard>
              </charts-dashboard>
            </v-card>
          </v-col>
          <v-col sm="12" md="5" xl="6" class="pa-4">
            <v-card class="pa-4" style="padding-top: 0px !important;">
              <v-app-bar flat dense color="rgba(0, 0, 0, 0)">
                    <v-toolbar-title v-text="'Productos mas Vendidos'"/>
              </v-app-bar>
              <sales-dashboard>
              </sales-dashboard>
            </v-card>
          </v-col>
        </v-row>
      </div>
    </v-container>
</template>

<script>
import ChartsDashboard from "./ChartsDashboard";
import SalesDashboard from "./SalesDashboard";
import Chart from 'chart.js';
export default {
    components: {
      ChartsDashboard,
      SalesDashboard
    },
    data: () => ({
      drawer: null,
      cards: {}
    }),
    methods:{
      async getCards(){
        try{
            const data = await API.dashboard.card();
            this.cards = data.data;
            this.$store.commit('LOADER',false);
        }catch(error){
            console.error(error);
            this.$store.commit('LOADER',false);
        }
      },
    },
    created(){
      this.getCards();
    }
}
</script>
