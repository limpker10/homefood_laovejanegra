<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color:  #AD91FD">Compras</h2> 
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
        <v-row dense class="pa-2 align-center">
            <v-col cols="9">
            </v-col>
            <v-col cols="3" style="text-align: right;">
             <v-btn small color="primary" class="mr-2" :to="'/purchase_create'">
              Añadir Compra
              <v-icon>mdi-plus</v-icon>
            </v-btn>            
          </v-col>
        </v-row>
        <br>
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
        >
            <template v-slot:item.date_emit="{ item }">
                <div>{{item.created_at | formatDate}}</div>
            </template>
            <template v-slot:[`item.actions`]="{ item }">
                <v-btn small icon :to="'/purchase_view/'+item.id_compra">
                    <v-icon small> mdi-file-eye-outline</v-icon>
                </v-btn>
            </template>
        </v-data-table>
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
    headers: [
      { text: 'ID', sortable: false, value: 'id_compra' },
      { text: 'Proveedor', sortable: false, value: 'nombre_proveedor' },
      { text: 'Nro. Factura', sortable: false, value: 'nro_factura' },
      { text: 'Total', sortable: false, value: 'total' },
      { text: 'Fecha', sortable: false, value: 'date_emit' },
      { text: 'Acciones', value: 'actions', align: 'center', sortable: false, align:'right' }
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
    productos: []
  }),

  computed: {
    
  },

  watch: {
    options(event) {
      this.itemsperpage=event.itemsPerPage;
      this.getlist(event.page, event.itemsPerPage);
    },
  },

  created () {
    this.getlist();
  },

  methods: {
    getlist(page,perpage) {
       this.$store.commit('LOADER',true);
        this.data.data= [];
        this.data.total= 0;
        this.loading = true;
        this.searchData = {
            perpage: perpage,
            data: this.searchQuery,
        };
        this.cargando = true;
        axios.post('api/getCompras?page=' + this.page, this.searchData)
        .then(response => {
            this.data = response.data;
            this.page = response.data.current_page;
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
</style>
