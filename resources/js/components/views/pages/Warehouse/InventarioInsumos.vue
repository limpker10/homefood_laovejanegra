<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color: #37474F">Kárdex de Movimientos de Insumos</h2> 
        <!--<v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>-->
      </div>
      <v-spacer></v-spacer>
      <v-btn icon >
        <v-icon>mdi-refresh</v-icon>
      </v-btn>
    </div>
    <div class="my-2" style="display:none;">
      <div>
        <v-card class="mb-4" light style="padding: 15px">
          <v-row>
            <v-col
              cols="12"
              sm="4"
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
              sm="4"
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
              md="2"
            >
            <v-btn color="primary" class="mr-2" @click="getlist(1,25)">
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
        <!--<v-row dense class="pa-2 align-center">
            <v-col cols="9">
            </v-col>
            <v-col cols="3" style="text-align: right;">
             <v-btn color="success" class="mr-2"  :href="urltoreturn">
               Exportar Excel
              <v-icon>mdi-file-excel</v-icon>
            </v-btn>            
          </v-col>
        </v-row>
        <br>-->
        <v-data-table
            :headers="headers"
            :items="data.data"
            class="elevation-1"
            :server-items-length="data.total"
            :page="page"
            :options.sync="options"
            :items-per-page="itemsperpage"
            :footer-props="{
                itemsPerPageOptions: [25,50,100,1000],
                itemsPerPageText:'Filas por página'
            }"
            :loading="loading" loading-text="Cargando... Por favor, espere">
            <template slot="no-data">
                Aún no se han agregado registros.
            </template>
            <template v-slot:[`item.id_insumo`]="{ item }">
                <div>
                    {{item.insumo.id_insumo}}
                </div>
            </template>

            <template v-slot:[`item.nombre_insumo`]="{ item }">
                <div>
                    {{item.insumo.nombre_insumo}}
                </div>
            </template>

            <template v-slot:[`item.stock`]="{ item }">
                <div>
                    {{item.stock}}
                </div>
            </template>
        </v-data-table>
      </v-card>
    
      <v-dialog v-model="verComprobanteDialog" max-width="500px">
          <v-card>
              <iframe :src="urlComprobante" frameborder="0" height="700px" width="100%">
              </iframe>
          </v-card>
      </v-dialog>
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
    headers: [
      { text: 'ID', sortable: false, value: 'id_insumo' },
      { text: 'Insumo', sortable: false, value: 'nombre_insumo' },
      { text: 'Stock', sortable: false, value: 'stock' },
    ],
    desserts: [],
    editedIndex: -1,
    data: {
        data: [],
        total: 0,
    },
    options: {},
    searchData: {},
    itemsperpage: 20,
    page:1,
    productos: [],
    tipos_comp: [],
    estados_comp: [],
    filter: {
      fecha_inicio: new Date().toISOString().substr(0, 10),
      fecha_fin: new Date().toISOString().substr(0, 10),
    },
    urltoreturn:'',
    loading: false,
    verComprobanteDialog:false,
    urlComprobante: ''
  }),

  computed: {
    
  },

  watch: {
    options(event) {
      this.itemsperpage=event.itemsPerPage;
      console.log(event)
      this.getlist(event.page, event.itemsPerPage);
    },
  },

  created () {
  },

  methods: {
    getExportData(){
        const exportdata={
            fecha_inicio: this.filter.fecha_inicio,
            fecha_fin: this.filter.fecha_fin,
            id_comprobante:this.filter.id_tipo_comprobante,
            id_estado:this.filter.id_estado
        }
        this.urltoreturn='/exportarReporteComp/'+JSON.stringify(exportdata)
    },

    getlist(page,perpage) {
        this.$store.commit('LOADER',true);
        this.data.data= [];
        this.data.total= 0;
        this.loading = true;
        this.page = page
        this.searchData = {
            perpage: perpage,
            data: this.filter,
        };
        axios.post('api/getInventarioInsumos?page=' + this.page, this.searchData)
        .then(response => {
            this.data = response.data;
            this.page = response.data.current_page;
            this.getExportData();
            this.loading = false;
            this.$store.commit('LOADER',false);
        });
        /*this.data = response.data;
        this.page = response.data.current_page;
        this.loading = false;*/
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
