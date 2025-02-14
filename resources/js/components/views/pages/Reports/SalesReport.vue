<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color:  #AD91FD">Reporte Ingresos Egresos</h2> 
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
        <v-tabs v-model="tab" :show-arrows="false" background-color="transparent">
            <v-tab to="#tabs-incomes">Ingresos</v-tab>
            <v-tab to="#tabs-purchases">Compras</v-tab>
            <v-tab to="#tabs-expenses">Egresos</v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item value="tabs-incomes">
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
                        itemsPerPageText:'items por página'
                    }"
                    :loading="loading" loading-text="Cargando... Por favor, espere">
                    <template slot="no-data">
                        Aún no se han agregado registros.
                    </template>
                    <template v-slot:item.fecha_emision="{ item }">
                        <div>
                            {{item.fecha_emision | formatDate}}
                        </div>
                    </template>
                    <template v-slot:item.tipo_doc="{ item }">
                        <div>
                            {{item.cliente.tipo_doc.tipo_documento}}
                        </div>
                    </template>
                    <template v-slot:item.serie="{ item }">
                        <div>
                            {{item.serie.serie}}-{{String(item.id_comprobante).padStart(8,'0')}}
                        </div>
                    </template>
                    <template v-slot:item.cliente="{ item }">
                        <div>
                            {{item.cliente.nombre}}
                        </div>
                    </template>
                    <template v-slot:item.id_tipo_comprobante="{ item }">
                        <div v-if="item.id_tipo_comprobante==1">
                            BV
                        </div>
                        <div v-if="item.id_tipo_comprobante==2">
                            FT
                        </div>
                        <div v-if="item.id_tipo_comprobante=3">
                            NP
                        </div>
                    </template>
                    <template v-slot:item.estado="{ item }">
                        <div>
                            <v-chip v-if="item.id_estado_comprobante == 1" class="ma-2" color="success">
                                Aprobado
                            </v-chip>
                            <v-chip v-if="item.id_estado_comprobante == 2" class="ma-2" color="error">
                                Anulado
                            </v-chip>
                            <v-chip v-if="item.id_estado_comprobante == 3" class="ma-2" color="warning">
                                Rechazado
                            </v-chip>
                            <v-chip v-if="item.id_estado_comprobante == 4" class="ma-2" color="info">
                                Pendiente
                            </v-chip>
                        </div>
                    </template>
                </v-data-table>
            </v-tab-item>
            <v-tab-item value="tabs-purchases">
                <v-data-table
                  :headers="headers_purchase"
                  :items="data_purchase.data"
                  class="elevation-1"
                  :server-items-length="data_purchase.total"
                  :page="page_purchase"
                  :options.sync="options_purchase"
                  :items-per-page="itemsperpage_purchase"
                  :footer-props="{
                      itemsPerPageOptions: [25,50,100,1000],
                      itemsPerPageText:'items por página'
                  }"
                  >
                    <template v-slot:item.date_emit="{ item }">
                      <div>{{item.created_at | formatDate}}</div>
                    </template>
                </v-data-table>
            </v-tab-item>
            <v-tab-item value="tabs-expenses">
                <v-data-table
                  :headers="headers_expense"
                  :items="data_expense.data"
                  class="elevation-1"
                  :server-items-length="data_expense.total"
                  :page="page_expense"
                  :options.sync="options_expense"
                  :items-per-page="itemsperpage_expense"
                  :footer-props="{
                      itemsPerPageOptions: [25,50,100,1000], 
                      itemsPerPageText:'items por página'
                  }"
                  >
                      <template v-slot:item.date_emit="{ item }">
                          <div>{{item.created_at | formatDate}}</div>
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
      { text: 'ID', sortable: false, value: 'id_comprobante' },
      { text: 'Tipo Documento', sortable: false, value: 'id_tipo_comprobante' },
      { text: 'Número', sortable: false, value: 'serie' },
      { text: 'Fecha de Emisión', sortable: false, value: 'fecha_emision' },
      { text: 'Cliente', sortable: false, value: 'cliente' },
      { text: 'RUC', sortable: false, value: 'tipo_doc' },
      { text: 'Gravado', sortable: false, value: 'subtotal', align:'right' },
      { text: 'IGV', sortable: false, value: 'igv', align:'right' },
      { text: 'Total', sortable: false, value: 'total', align:'right'},
      { text: 'Estado', sortable: false, value: 'estado' }
    ],
    headers_purchase: [
      { text: 'ID', sortable: false, value: 'id_compra' },
      { text: 'Proveedor', sortable: false, value: 'nombre_proveedor' },
      { text: 'Nro. Factura', sortable: false, value: 'nro_factura' },
      { text: 'Total', sortable: false, value: 'total' },
      { text: 'Fecha', sortable: false, value: 'date_emit' }
    ],
    headers_expense: [
        { text: 'ID', sortable: false, value: 'id_egreso' },
        { text: 'Fecha', sortable: false, value: 'fecha_egreso' },
        { text: 'Motivo', sortable: false, value: 'motivo_egreso.motivo' },
        { text: 'Monto', sortable: false, value: 'monto' }
    ],
    desserts: [],
    editedIndex: -1,
    data: {
        data: [],
        total: 0,
    },
    data_purchase: {
        data: [],
        total: 0,
    },
    data_expense: {
        data: [],
        total: 0,
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
      fecha_fin: new Date().toISOString().substr(0, 10),
      id_tipo_comprobante:0,
      id_estado:0,
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
      this.getlist(event.page, event.itemsPerPage);
    },
    options_purchase(event) {
      this.itemsperpage_purchase=event.itemsPerPage;
      this.getlist_purchase(event.page, event.itemsPerPage);
    },
    options_expense(event) {
      this.itemsperpage_expense=event.itemsPerPage;
      this.getlist_expense(event.page, event.itemsPerPage);
    },
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
        this.urltoreturn='/exportarReporteComp/'+JSON.stringify(exportdata)
    },
    getReports(){
      this.getlist();
      this.getlist_purchase();
      this.getlist_expense();
    },
    getlist(page,perpage) {
        this.$store.commit('LOADER',true);
        this.data.data= [];
        this.data.total= 0;
        this.loading = true;
        this.searchData = {
            perpage: perpage,
            data: this.filter,
        };
        axios.post('api/getReportIncomes?page=' + this.page, this.searchData)
        .then(response => {
            this.data = response.data;
            this.page = response.data.current_page;
            this.getExportData();
            this.loading = false;
            this.$store.commit('LOADER',false);
        });
    },
    getlist_purchase(page,perpage){
      this.$store.commit('LOADER',true);
        this.data_purchase.data= [];
        this.data_purchase.total= 0;
        this.loading = true;
        this.searchData = {
            perpage: perpage,
            data: this.filter,
        };
        axios.post('api/getReportPurchases?page=' + this.page_purchase, this.searchData)
        .then(response => {
            this.data_purchase = response.data;
            this.page_purchase = response.data.current_page;
            this.getExportData();
            this.loading = false;
            this.$store.commit('LOADER',false);
        });
    },
    getlist_expense(page,perpage){
        this.$store.commit('LOADER',true);
        this.data_expense.data= [];
        this.data_expense.total= 0;
        this.loading = true;
        this.searchData = {
            perpage: perpage,
            data: this.filter,
        };
        axios.post('api/getReportExpenses?page=' + this.page_expense, this.searchData)
        .then(response => {
            this.data_expense = response.data;
            this.page_expense = response.data.current_page;
            this.getExportData();
            this.loading = false;
            this.$store.commit('LOADER',false);
        });
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
