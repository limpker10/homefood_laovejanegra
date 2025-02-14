<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color: #ad91fd">Mesas</h2>
        <!--<v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>-->
      </div>
      <v-spacer></v-spacer>
      <v-btn icon>
        <v-icon>mdi-refresh</v-icon>
      </v-btn>
    </div>

    <div class="my-2">
      <div>
        <v-card class="mb-4" light style="padding: 15px">
          <v-row dense class="pa-2 align-center">
            <v-col cols="4">
              <v-text-field
                v-model="searchQuery"
                append-icon="mdi-magnify"
                class="flex-grow-1 mr-1"
                single-line
                hide-details
                placeholder="Buscar por nombre …"
                @keyup.enter="searchData_(searchQuery)"
                @keyup.delete="researchData(searchQuery)"
              ></v-text-field>
            </v-col>
            <v-col cols="5"> </v-col>
            <v-col cols="3" style="text-align: right">
              <v-btn small color="primary" class="mr-2" @click="reset()">
                Añadir Mesa
                <v-icon>mdi-plus</v-icon>
              </v-btn>
            </v-col>
          </v-row>
          <br />
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
              itemsPerPageText: 'items por página',
            }"
            :loading="loading"
            loading-text="Loading... Please wait"
          >
            <template v-slot:[`item.sucursal`]="{ item }">
              <div>
                {{ item.zona.sucursal.razon_social_sucursal }}
              </div>
            </template>
            <template v-slot:[`item.zona`]="{ item }">
              <div>
                {{ item.zona.nombre }}
              </div>
            </template>
            <template v-slot:[`item.actions`]="{ item }">
              <v-btn small icon @click="editItem(item)">
                <v-icon small> mdi-pencil</v-icon>
              </v-btn>
              <v-btn small icon @click="deleteItem(item)">
                <v-icon small> mdi-delete</v-icon>
              </v-btn>
            </template>
          </v-data-table>

          <v-dialog v-model="dialog" max-width="400px">
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>
              <v-card-text>
                <v-form ref="form" v-model="valid" lazy-validation>
                  <v-container>
                    <v-row>
                      <v-col cols="12" sm="12" md="12">
                        <v-select
                          :items="sucursales"
                          label="Sucursal"
                          placeholder="Selecciona una sucursal"
                          v-model="editedItem.id_sucursal"
                          @change="onChangeSucursal"
                          item-text="razon_social_sucursal"
                          item-value="id_sucursal"
                          :rules="requiredRules"
                        ></v-select>
                        <v-select
                          :items="zonas"
                          label="Zona"
                          placeholder="Selecciona una zona"
                          v-model="editedItem.id_zona"
                          item-text="nombre"
                          item-value="id_zona"
                          :rules="requiredRules"
                        ></v-select>
                        <v-text-field
                          v-model="editedItem.nro_mesa"
                          label="Nombre de Mesa"
                          :rules="requiredRules"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-form>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" dark @click="close">Cancelar</v-btn>
                <v-btn color="primary" @click="save"> Guardar </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" max-width="300px">
            <v-card>
              <v-card-title class="subtitle-1"
                >¿Desea eliminar esta Mesa?</v-card-title
              >
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" dark @click="closeDelete">Cancelar</v-btn>
                <v-btn color="primary" @click="deleteItemConfirm"
                  >Aceptar</v-btn
                >
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-card>
      </div>
    </div>
  </div>
</template>
 
<script>
export default {
  components: {},
  data: () => ({
    searchQuery: "",
    dialog: false,
    dialogDelete: false,
    headers: [
      { text: "ID", sortable: false, value: "id_mesa" },
      { text: "Mesa", sortable: false, value: "nro_mesa" },
      { text: "Zona", sortable: false, value: "zona" },
      { text: "Sucursal", sortable: false, value: "sucursal" },
      {
        text: "Acciones",
        value: "actions",
        align: "center",
        sortable: false,
        align: "right",
      },
    ],
    valid:"",
    desserts: [],
    sucursales: [],
    zonas: [],
    editedIndex: -1,
    data: {
      data: [],
      total: 0,
    },
    options: {},
    searchData: {},
    itemsperpage: 25,
    page: 1,
    productos: [],
    currentUser: {},
    loading: false,
    editedIndex: -1,
    editedItem: {
      codigo: "",
      mesa: "",
    },
    defaultItem: {
      codigo: "",
      mesa: "",
    },
    requiredRules: [(v) => !!v || "Campo obligatorio"],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nueva Mesa" : "Editar Mesa";
    },
  },

  watch: {
    options(event) {
      this.itemsperpage = event.itemsPerPage;
      this.getlist(event.page, event.itemsPerPage);
    },
  },

  created() {
    this.getSucursales();
    this.getUser();
    this.getlist();
  },

  methods: {
    reset() {
      this.dialog = true;
      this.editedIndex = -1;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
        //selecciona automaticamente la sucursal del usuario actual
        this.editedItem.id_sucursal = this.currentUser.id_sucursal;
      });
    },
    searchData_() {
      this.searchData = {
        data: this.searchQuery,
      };
      this.data.data = [];
      this.data.total = 0;
      this.data.from = 0;
      this.data.to = 0;
      this.getlist(1, this.itemsperpage);
    },
    getUser() {
      axios
        .get("/api/user")
        .then((response) => {
          this.currentUser = response.data;
          this.getZonas(this.currentUser.id_sucursal);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getSucursales() {
      axios
        .get("/api/branch")
        .then((response) => {
          this.sucursales = response.data;
        })
        .catch((resonse) => {
          console.log("error: " + response.data);
        });
    },
    getZonas(id_sucursal) {
      axios
        .get("/api/branch/" + id_sucursal)
        .then((response) => {
          this.zonas = response.data.zonas;
        })
        .catch((resonse) => {
          console.log("error: " + response.data);
        });
    },
    getlist(page, perpage) {
      this.data.data = [];
      this.data.total = 0;
      this.loading = true;
      this.searchData = {
        perpage: perpage,
        data: this.searchQuery,
      };
      this.cargando = true;

      axios
        .post("api/getMesas?page=" + this.page, this.searchData)
        .then((response) => {
          this.data = response.data;
          this.page = response.data.current_page;
          this.loading = false;
        })
        .finally(() => (this.cargando = false));
    },
    researchData() {
      if (this.searchQuery.length == 0) {
        this.getlist(1, this.itemsperpage);
      }
    },
    onChangeSucursal() {
      this.getZonas(this.editedItem.id_sucursal);
    },

    changeStatus(id, status) {
      if (status == 0) {
        status = 1;
      } else {
        status = 0;
      }
      axios
        .put("/api/changeStatus/" + id, {
          table: "mst_mesas",
          id: "id_mesa",
          status: status,
        })
        .then((response) => {
          this.loading = false;
          Toast.fire({
            icon: "success",
            title: "Estado actualizado correctamente!",
          });
          this.getlist();
          this.close();
        })
        .finally(() => (this.cargando = false));
    },
    editItem(item) {
      this.editedIndex = this.data.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
      this.editedItem.id_sucursal = item.zona.id_sucursal;
    },

    deleteItem(item) {
      this.editedIndex = this.data.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      //this.desserts.splice(this.editedIndex, 1)
      this.loading = true;
      axios
        .delete("api/table/" + this.editedItem.id_mesa, this.editedItem)
        .then((response) => {
          this.loading = false;

          this.getlist();
          this.close();
        })
        .finally(() => (this.cargando = false));
      this.closeDelete();
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    save() {
      if(this.$refs.form.validate()){
        this.loading = true;
        if (this.editedIndex > -1) {
          axios
            .put("api/table/" + this.editedItem.id_mesa, this.editedItem)
            .then((response) => {
              this.loading = false;
              this.getlist();
              this.close();
            })
            .finally(() => (this.cargando = false));
        } else {
          axios
            .post("api/table", this.editedItem)
            .then((response) => {
              this.loading = false;
              this.getlist();
              this.close();
            })
            .finally(() => (this.cargando = false));
        }
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
