<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color: #37474F">Reporte Consolidado</h2> 
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
              sm="6"
              md="4"
            >
            <v-btn color="primary" class="mr-2" @click="getReports">
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
            <v-tab to="#tabs-incomes">Ventas</v-tab>
            <v-tab to="#tabs-purchases">Compras</v-tab>
            <v-tab to="#tabs-expenses">Egresos</v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tabs-incomes">
                <v-data-table
                    :headers="headers"
                    :items="data.data"
                    class="elevation-1"
                    :items-per-page="25"
                    :footer-props="{
                        itemsPerPageOptions: [25,50,100,1000],
                        itemsPerPageText:'Filas por página'
                    }"
                    :loading="loading" loading-text="Cargando... Por favor, espere">
                    <template slot="no-data">
                        Aún no se han agregado registros.
                    </template>
                    
                </v-data-table>
            </v-tab-item>
            <v-tab-item value="tabs-purchases">
                <v-data-table
                  :headers="headers_purchase"
                  :items="data_purchase.data"
                  class="elevation-1"
                  :items-per-page="25"
                  :footer-props="{
                      itemsPerPageOptions: [25,50,100,1000],
                      itemsPerPageText:'Filas por página'
                  }"
                  >
                  <template slot="no-data">
                        Aún no se han agregado registros.
                    </template>
                    
                  <template v-slot:item.nombre_categoria="{ item }">
                      <div>
                        {{item.nombre_categoria_insumo}}
                        {{item.nombre_categoria_producto}}
                      </div>
                  </template>
                </v-data-table>
            </v-tab-item>
            <v-tab-item value="tabs-expenses">
                <v-data-table
                  :headers="headers_expense"
                  :items="data_expense.data"
                  class="elevation-1"
                  :items-per-page="25"
                  :footer-props="{
                      itemsPerPageOptions: [25,50,100,1000], 
                      itemsPerPageText:'Filas por página'
                  }"
                  >
                  <template slot="no-data">
                      Aún no se han agregado registros.
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
export default {
  components: {
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
      { text: 'Monto Total', sortable: false, value: 'precio_total' }
    ],
    headers_purchase: [
      { text: 'Código', sortable: false, value: 'codigo_producto' },
      { text: 'Nombre Producto/Insumo', sortable: false, value: 'nombre_producto' },
      { text: 'Categoría', sortable: false, value: 'nombre_categoria' },
      { text: 'Cantidad', sortable: false, value: 'cantidad' },
      { text: 'Monto', sortable: false, value: 'precio_total' }
    ],
    headers_expense: [
        { text: 'ID', sortable: false, value: 'id_egreso' },
        { text: 'Motivo', sortable: false, value: 'motivo_egreso.motivo' },
        { text: 'Monto', sortable: false, value: 'monto' }
    ],
    desserts: [],
    editedIndex: -1,
    data: {
        data: [],
    },
    data_purchase: {
        data: [],
    },
    data_expense: {
        data: [],
    },
    options: {},
    options_purchase: {},
    options_expense: {},
    searchData: {},
    itemsperpage: 25,
    itemsperpage_purchase: 25,
    itemsperpage_expense: 25,
    page:1,
    page_purchase:1,
    page_expense:1,
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
    urlComprobante: ''
  }),

  computed: {
    
  },

  watch: {
  },

  created () {
    /*this.getlist();
    this.getlist_purchase();
    this.getlist_expense();*/
  },

  methods: {
    getExportData(){
        const exportdata={
            fecha_inicio: this.filter.fecha_inicio,
            fecha_fin: this.filter.fecha_fin
        }
        this.urltoreturn='/exportarReporteConsolidado/'+JSON.stringify(exportdata)
    },
    getReports(){
      this.getlist();
    },
    getlist() {
        this.$store.commit('LOADER',true);
        this.loading = true;
        axios.post('api/getReportConsolidated', this.filter)
        .then(response => {
            this.data.data = response.data.ventas;
            this.data_purchase.data = response.data.compras;
            this.data_expense.data = response.data.egresos;
            this.getExportData();
            this.loading = false;
            this.$store.commit('LOADER',false);
        });
    },
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
    //font-size: 12px !important;
    //font-family:'Quicksand', sans-serif  !important;
    }
</style>
