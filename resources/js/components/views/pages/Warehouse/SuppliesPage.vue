<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color: #37474f">Stock de Insumos</h2>
        <!--<v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>-->
      </div>
      <v-spacer></v-spacer>
      <v-btn icon>
        <v-icon>mdi-refresh</v-icon>
      </v-btn>
    </div>
    <!--<div class="my-2">
      <div>
        <v-card class="mb-4" light style="padding: 15px">
          <v-row>
            <v-col
              cols="12"
              sm="4"
              md="4"
            >
              Filtro
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
    </div>-->
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
              itemsPerPageOptions: [25, 50, 100, 1000],
              itemsPerPageText: 'Filas por página',
            }"
            :loading="loading"
            loading-text="Cargando... Por favor, espere"
          >
            <template slot="no-data">
              Aún no se han agregado registros.
            </template>
            <template v-slot:[`item.nombre_insumo`]="{ item }">
              <div>
                {{ item.insumo.nombre_insumo }}
              </div>
            </template>
            <template v-slot:[`item.categoria`]="{ item }">
              <div>
                {{
                  item.insumo.categoria != null
                    ? item.insumo.categoria.nombre
                    : "SIN CATEGORIA"
                }}
              </div>
            </template>
            <template v-slot:[`item.unidad_medida`]="{ item }">
              <div>
                {{ item.insumo.unidad_medida.simbolo }}
              </div>
            </template>
            <template v-slot:[`item.sucursal`]="{ item }">
              <div>
                {{ item.sucursal.nombre_sucursal }}
              </div>
            </template>
            <template v-slot:[`item.actions`]="{ item }">
              <div>
                <v-btn small icon @click="editItem(item)">
                  <v-icon small> mdi-pencil </v-icon>
                </v-btn>
              </div>
            </template>
          </v-data-table>
        </v-card>
      </div>
    </div>
    <v-dialog v-model="dialogEdit" width="500">
      <v-card>
        <v-card-title primary-title> Ajuste de Inventario </v-card-title>
        <v-card-text>
          <v-text-field
            name="Stock"
            label="Stock"
            v-model="editedStock"
            id="stock"
            type="number"
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            text
            small
            color="secondary"
            @click="
              dialogEdit = !dialogEdit;
              editedStock = null;
            "
            >Cancelar</v-btn
          >
          <v-btn text small color="primary" @click="saveEdit">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
 
<script>
export default {
  components: {},
  data: () => ({
    search: "",
    dialog: false,
    date: new Date().toISOString().substr(0, 10),
    menu1: false,
    menu2: false,
    headers: [
      { text: "Insumo", sortable: false, value: "nombre_insumo" },
      { text: "Categoría", sortable: false, value: "categoria" },
      { text: "Unidad Medida", sortable: false, value: "unidad_medida" },
      { text: "Sucursal", sortable: false, value: "sucursal" },
      { text: "Stock", sortable: false, value: "stock" },
      { text: "Acciones", sortable: false, value: "actions" },
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
    page: 1,
    productos: [],
    tipos_comp: [],
    estados_comp: [],
    filter: "",
    urltoreturn: "",
    loading: false,
    verComprobanteDialog: false,
    urlComprobante: "",
    dialogEdit: false,
    editedStock: null,
    editedId: null,
  }),

  computed: {},

  watch: {
    options(event) {
      this.itemsperpage = event.itemsPerPage;
      console.log(event);
      this.getlist(event.page, event.itemsPerPage);
    },
  },

  created() {},

  methods: {
    editItem(item){
      this.editedStock = parseFloat(item.stock);
      this.editedId = item.id_insumo_sucursal;
      this.dialogEdit = true;
      console.log(item);
    },
    saveEdit() {
      this.$swal({
        title: "¿Estas seguro de realizar este cambio?",
        text: "No seras capaz de revertir esta acción!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Si, guardar!",
        cancelButtonText: "No, cancelar!",
      }).then((result) => {
        if (result.isConfirmed) {
          let data = new FormData();
          data.append("id", this.editedId);
          data.append("stock", this.editedStock);
          console.log(data);
          axios
            .post("/api/updateStockInsumo", data)
            .then((res) => {
              this.$swal({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "Stock actualizado correctamente",
                showConfirmButton: false,
                timerProgressBar: true,
                timer: 2500,
              });
              this.dialogEdit = false;
              this.getlist(this.page, this.itemsperpage);
            })
            .catch((err) => {
              console.error(err);
              this.$swal({
                toast: true,
                position: "top-end",
                icon: "error",
                title: "Ocurrio un error",
                showConfirmButton: false,
                timerProgressBar: true,
                timer: 2500,
              });
              this.dialogEdit = false;
              this.getlist(this.page, this.itemsperpage);
            });
        }
      });
    },

    getExportData() {
      const exportdata = {
        sucursal: this.filter,
      };
      this.urltoreturn = "/exportarReporteComp/" + JSON.stringify(exportdata);
    },

    async getlist(page, perpage) {
      this.$store.commit("LOADER", true);
      this.loading = true;
      try {
        this.data.data = [];
        this.data.total = 0;
        this.page = page;
        this.searchData = {
          perpage: perpage,
          data: this.filter,
        };
        /*axios.post('api/getAlmacen?page=' + this.page, this.searchData)
        .then(response => {
            this.data = response.data;
            this.page = response.data.current_page;
            this.getExportData();
            this.loading = false;
            this.$store.commit('LOADER',false);
        });*/
        /*this.data = response.data;
        this.page = response.data.current_page;
        this.loading = false;*/

        const data = await API.supplies.stock(this.page, this.searchData);
        this.data = data.data;
        console.log(this.data.data, "registro");
        this.page = data.current_page;
        this.loading = false;
        this.$store.commit("LOADER", false);
      } catch (error) {
        console.error(error.response);
      }
    },
  },
};
</script>
<style lang="scss" scoped>
.btn-actions {
  background-color: #fff !important;
  color: #121212;
}
* {
  text-transform: none !important;
  //font-size: 12px !important;
  //font-family:'Quicksand', sans-serif  !important;
}
</style>
