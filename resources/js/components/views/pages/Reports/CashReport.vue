<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color:  #AD91FD">Ranking de Platos</h2> 
        <!--<v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>-->
      </div>
      <v-spacer></v-spacer>
      <v-btn icon >
        <v-icon>mdi-refresh</v-icon>
      </v-btn>
    </div>
    <div class="my-2">
      <div>
        <v-card class="mb-4" light style="padding: 15px">
          <v-row>
            <v-col
              cols="12"
              sm="6"
              md="4"
            >
              <v-menu
                v-model="menu1"
                :close-on-content-click="false"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="filter.fecha_inicio"
                    label="Fecha Inicio"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  no-title
                  v-model="filter.fecha_inicio"
                  @input="menu1 = false"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col
              cols="12"
              sm="6"
              md="4"
            >
              <v-menu
                v-model="menu2"
                :close-on-content-click="false"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="filter.fecha_fin"
                    label="Fecha Fin"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  no-title
                  v-model="filter.fecha_fin"
                  @input="menu2 = false"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col
              cols="12"
              sm="4"
              md="4"
            >
            <v-btn color="primary" class="mr-2" @click="getlist">
              <v-icon>mdi-magnify</v-icon>
              Buscar
            </v-btn>            
            </v-col>
          </v-row>
        </v-card>
      </div>
    </div>
    <div class="my-2">
    <div>
      <v-card class="mb-4" light style="padding: 15px">
        <v-row dense class="pa-2 align-center" v-if="urltoreturn">
          <v-col cols="9">
          </v-col>
          <v-col cols="3" style="text-align: right;">
            <v-btn color="success" class="mr-2"  :href="urltoreturn">
              Exportar Excel
              <v-icon>mdi-file-excel</v-icon>
            </v-btn>            
          </v-col>
        </v-row>
        <br>
        <v-tabs v-model="tab" :show-arrows="false" background-color="transparent">
            <v-tab to="#tabs-ranking">Ranking</v-tab>
            <v-tab to="#tabs-graphic">Gráfico</v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tabs-graphic">
               <div id="appchart">
                <ranking-dishes :chart-data.sync="data_chart" :options.sync="chart_options"></ranking-dishes>
              </div>
            </v-tab-item>
            <v-tab-item value="tabs-ranking">
                <v-data-table
                    :headers="headers"
                    :items="data.data"
                    class="elevation-1"
                    :page="page"
                    :options.sync="options"
                    :items-per-page="itemsperpage"
                    :footer-props="{
                        itemsPerPageOptions: [25,50,100,1000],
                        itemsPerPageText:'items por página'
                    }"
                    :loading="loading" loading-text="Cargando... Por favor, espere">
                    <template slot="no-data">
                        Aún no se han agregado registros.
                    </template>
                    <template v-slot:item.precio_total="{ item }">
                        <div>
                            S/. {{item.precio_total}}
                        </div>
                    </template>
                </v-data-table>
            </v-tab-item>
        </v-tabs-items>

        
      </v-card>
    </div>
    </div>
  </div>
</template>
 
<script>
import RankingDishes from "./Charts/RankingDishes";

import Chart from 'chart.js';

export default {
  components: {
    RankingDishes,
  },
  data: () => ({
    search: '', 
    dialog: false,
    date: new Date().toISOString().substr(0, 10),
    menu1: false,
    menu2: false,
    tab: null,
    headers: [
      { text: 'Código', sortable: false, value: 'codigo_producto' },
      { text: 'Nombre Producto', sortable: false, value: 'nombre_producto' },
      { text: 'Categoría', sortable: false, value: 'nombre_categoria' },
      { text: 'Cantidad Vendida', sortable: false, value: 'cantidad' },
      { text: 'Monto Total', sortable: false, value: 'precio_total' },
    ],
    desserts: [],
    editedIndex: -1,
    data: {
        data: [],
        total: 0,
    },
    options: {},
    searchData: {},
    itemsperpage: 25,
    page:1,
    productos: [],
    tipos_comp: [],
    estados_comp: [],
    filter: {
      fecha_inicio: new Date().toISOString().substr(0, 10),
      fecha_fin: new Date().toISOString().substr(0, 10)
    },
    urltoreturn:null,
    loading: false,
    verComprobanteDialog:false,
    urlComprobante: '',


    data_chart: {
        labels: [],
        datasets: [
          {
            label: "# de Pedidos",
            data: [],
            borderWidth: 1,
            backgroundColor: "#ffca40",
          }
        ] 
      },
      chart_options: {
        backgroundColor: "#ffca40",
        responsive: true,
        maintainAspectRatio: false
      }
  }),

  computed: {
    
  },

  watch: {
    options(event) {
      this.itemsperpage=event.itemsPerPage;
      //this.getlist(event.page, event.itemsPerPage);
    },
  },

  created () {
    this.getlist();
  },

  methods: {
    getExportData(){
        const exportdata={
            fecha_inicio: this.filter.fecha_inicio,
            fecha_fin: this.filter.fecha_fin
        }
        this.urltoreturn='/exportarReporteRanking/'+JSON.stringify(exportdata)
    },
    getlist() {
        this.$store.commit('LOADER',true);
        this.data.data= [];
        this.data.total= 0;
        this.loading = true;
        axios.post('/api/getRanking', this.filter)
        .then(response => {
            this.data.data = response.data;
            this.getExportData();
            this.loading = false;
            this.$store.commit('LOADER',false);
            this.getChart();
        });
    },

    getChart(){
      // this.data_chart.datasets.data = this.data.data.map(({ cantidad }) => Number(cantidad));
      // this.data_chart.labels = this.data.data.map(({ nombre_producto }) => nombre_producto);
      
      this.data_chart= {
        labels: this.data.data.map(({ nombre_producto }) => nombre_producto),
        datasets: [
          {
            label: "# de Pedidos",
            data: this.data.data.map(({ cantidad }) => Number(cantidad)),
            borderWidth: 1
          }
        ] 
      };
    }
  }
}
</script>
<style lang="scss" scoped>
  .btn-actions{
    background-color: #fff !important;
    color: #121212;
  }
  *{
    text-transform: none !important;
    }
#appchart {
  font-family: "Avenir", Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
