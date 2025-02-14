<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color: #37474F">Kárdex de movimiento</h2> 
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
            <template v-slot:[`item.fecha_movimiento`]="{ item }">
                <div>
                    {{item.fecha_movimiento | formatDate}}
                </div>
            </template>
            <template v-slot:[`item.tipo_producto_insumo`]="{ item }">
                <v-chip small v-if="item.tipo_producto_insumo == 0" class="ma-2" color="warning">
                    Producto
                </v-chip>
                <v-chip small v-if="item.tipo_producto_insumo == 1" class="ma-2" color="info">
                    Insumo
                </v-chip>
            </template>
            <template v-slot:[`item.id_sucursal`]="{ item }">
                <div>
                    {{item.sucursal.nombre_sucursal}}
                </div>
            </template>
            <template v-slot:[`item.id_tipo_movimiento`]="{ item }">
                <div>
                    {{item.tipo_movimiento.tipo_movimiento}}
                </div>
            </template>
            <template v-slot:[`item.estado`]="{ item }">
                <div>
                     <v-chip small v-if="item.id_estado_comprobante == 1" class="ma-2" color="success">
                        Aprobado
                    </v-chip>
                    <v-chip small v-if="item.id_estado_comprobante == 2" class="ma-2" color="error">
                        Anulado
                    </v-chip>
                    <v-chip small v-if="item.id_estado_comprobante == 3" class="ma-2" color="warning">
                        Rechazado
                    </v-chip>
                    <v-chip small v-if="item.id_estado_comprobante == 4" class="ma-2" color="info">
                        Pendiente
                    </v-chip>
                </div>
            </template>
            <template v-slot:[`item.stock_actual_calculado`]="{ item }">
                <div v-if="item.tipo_movimiento.id_tipo_movimiento == 1 || item.tipo_movimiento.id_tipo_movimiento == 2 ||item.tipo_movimiento.id_tipo_movimiento == 5">
                    {{ Number(item.stock_actual) + Number(item.cantidad) }}
                </div>
                <div v-if="item.tipo_movimiento.id_tipo_movimiento == 3 || item.tipo_movimiento.id_tipo_movimiento == 4">
                    {{ Number(item.stock_actual) - Number(item.cantidad) }}
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
      { text: 'ID', sortable: false, value: 'id_almacen_movimientos' },
      { text: 'Producto/Insumo', sortable: false, value: 'nombre_producto_insumo' },
      { text: 'Tipo', sortable: false, value: 'tipo_producto_insumo' },
      { text: 'Fecha de Movimiento', sortable: false, value: 'fecha_movimiento' },
      { text: 'Tipo de Movimiento', sortable: false, value: 'id_tipo_movimiento' },
      { text: 'Stock Inicial', sortable: false, value: 'stock_actual' },
      { text: 'Cantidad', sortable: false, value: 'cantidad' },
      { text: 'Stock Actual', sortable: false, value: 'stock_actual_calculado' },
      { text: 'Sucursal', sortable: false, value: 'id_sucursal' }
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
        axios.post('api/getAlmacen?page=' + this.page, this.searchData)
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

    verpdf(item){
      this.$store.commit('LOADER',true);
      //window.open('/generarTicketPDF/'+id, '_blank')               
      this.comprobanteDialog = false;
      if(item.id_tipo_comprobante!=3){
        this.urlComprobante = item.sucursal.facturador+'print/document/'+item.external_id+'/ticket'
        this.verComprobanteDialog = true;
        this.$store.commit('LOADER',false);
      }
      else{
        this.urlComprobante =  '/generarTicketPDF/'+item.id_comprobante
        this.verComprobanteDialog = true;
        this.$store.commit('LOADER',false);
      }
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
    //font-size: 12px !important;
    //font-family:'Quicksand', sans-serif  !important;
    }
</style>
