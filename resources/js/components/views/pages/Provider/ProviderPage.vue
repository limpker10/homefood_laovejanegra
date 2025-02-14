<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color: #ad91fd">Proveedores</h2>
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
                @keyup.enter="searchUser(searchQuery)"
                @keyup.delete="researchUser(searchQuery)"
              ></v-text-field>
            </v-col>
            <v-col cols="5"> </v-col>
            <v-col cols="3" style="text-align: right">
              <v-btn small color="primary" class="mr-2" @click="dialog = true">
                Añadir Proveedores
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
            <template v-slot:[`item.actions`]="{ item }">
              <v-btn small icon @click="editItem(item)">
                <v-icon small> mdi-pencil</v-icon>
              </v-btn>
              <v-btn small icon @click="deleteItem(item)">
                <v-icon small> mdi-delete</v-icon>
              </v-btn>
            </template>
            <template v-slot:[`item.type_doc`]="{ item }">
              <div>
                {{ item.tipo_doc.tipo_documento }}
              </div>
            </template>
          </v-data-table>

          <v-dialog v-model="dialog" max-width="600px">
            <v-card>
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>
              <v-card-text>
                <v-form ref="form" v-model="valid" lazy-validation>
                  <v-container>
                    <v-row>
                      <v-col cols="12" sm="6" md="6">
                        <v-select
                          :items="tipos_doc"
                          label="Tipo Documento"
                          placeholder="Selecciona un tipo de Documento"
                          v-model="editedItem.id_tipo_doc"
                          required
                          item-text="tipo_documento"
                          item-value="id_tipo_doc"
                        ></v-select>
                        <v-text-field
                          v-model="editedItem.nombre"
                          label="Nombre / Razón Social Proveedor"
                          placeholder="Nombre / Razón Social Proveedor"
                          :rules="requiredRules"
                        ></v-text-field>
                        <v-text-field
                          v-model="editedItem.email"
                          label="Correo Electrónico"
                          placeholder="Correo Electrónico"
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" sm="6" md="6">
                        <v-text-field
                          v-model="editedItem.nro_doc"
                          label="Nro. Documento"
                          placeholder="Nro. Documento"
                          :rules="requiredRules"
                        >
                          <template v-slot:append>
                            <v-icon
                              color="primary"
                              class="mr-2"
                              @click="
                                obtenerDataDocumentos(editedItem.id_tipo_doc)
                              "
                            >
                              mdi-magnify
                            </v-icon>
                          </template>
                        </v-text-field>
                        <v-text-field
                          v-if="showAdd"
                          v-model="editedItem.direccion"
                          label="Dirección"
                          placeholder="Dirección"
                          :rules="requiredRules"
                        ></v-text-field>
                        <v-text-field
                          v-model="editedItem.telefono"
                          label="Teléfono"
                          placeholder="Teléfono"
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" dark @click="close">Cancelar</v-btn>
                <v-btn color="primary" :disabled="!valid" @click="save">
                  Guardar
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <v-dialog v-model="dialogDelete" max-width="300px">
            <v-card>
              <v-card-title class="subtitle-1"
                >¿Desea eliminar este Proveedor?</v-card-title
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
      { text: "ID", sortable: false, value: "id_proveedor" },
      { text: "Nombre", sortable: false, value: "nombre" },
      { text: "Tipo Doc.", sortable: false, value: "type_doc" },
      { text: "Nro. Doc", sortable: false, value: "nro_doc" },
      {
        text: "Acciones",
        value: "actions",
        align: "center",
        sortable: false,
        align: "right",
      },
    ],
    tab: 0,
    desserts: [],
    editedIndex: -1,
    valid: true,
    data: {
      data: [],
      total: 0,
    },
    options: {},
    searchData: {},
    itemsperpage: 25,
    page: 1,
    productos: [],
    tipos_doc: [],
    loading: false,
    autocomplteLoading: false,
    showName: true,
    showAdd: true,
    editedIndex: -1,
    editedItem: {
      codigo: "",
      categoria: "",
      nro_doc: "",
      nombre: "",
      direccion: "",
    },
    defaultItem: {
      codigo: "",
      categoria: "",
      nro_doc: "",
      nombre: "",
      direccion: "",
    },

    requiredRules: [(v) => !!v || "Campo obligatorio"],
    emailRules: [
      (v) => !!v || "Campo obligatorio",
      (v) => /.+@.+\..+/.test(v) || "Correo Electrónico debe ser válido",
    ],
    datosBus: {
      tipoDoc: "",
      numDoc: "",
    },
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nuevo Proveedor" : "Editar Proveedor";
    },
  },

  watch: {
    options(event) {
      this.itemsperpage = event.itemsPerPage;
      this.getlist(event.page, event.itemsPerPage);
    },
  },

  created() {
    this.getTiposDoc();
  },

  methods: {
    getTiposDoc() {
      axios
        .get("/api/getTiposDoc")
        .then((response) => {
          this.tipos_doc = response.data;
        })
        .catch((resonse) => {
          console.log("error: " + response.data);
        });
    },

    searchUser() {
      this.searchData = {
        data: this.searchQuery,
      };
      this.data.data = [];
      this.data.total = 0;
      this.data.from = 0;
      this.data.to = 0;
      this.getlist(1, this.itemsperpage);
    },

    getlist(page = 1, perpage = 25) {
      this.$store.commit("LOADER", true);
      this.data.data = [];
      this.data.total = 0;
      this.page = page;
      this.loading = true;
      this.searchData = {
        perpage: perpage,
        data: this.searchQuery,
      };
      this.cargando = true;

      axios
        .post("/api/getProveedores?page=" + this.page, this.searchData)
        .then((response) => {
          this.data = response.data;
          this.page = response.data.current_page;
          this.loading = false;
          this.$store.commit("LOADER", false);
        });
    },

    researchUser() {
      if (this.searchQuery.length == 0) {
        this.getlist(1, this.itemsperpage);
      }
    },

    editItem(item) {
      this.editedIndex = this.data.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
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
        .delete(
          "/api/provider/" + this.editedItem.id_proveedor,
          this.editedItem
        )
        .then((response) => {
          this.loading = false;
          this.getlist();
          this.closeDelete();
        })
        .finally(() => (this.cargando = false));
    },

    close() {
      this.dialog = false;
      this.$refs.form.reset();
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
      if (this.$refs.form.validate()) {
        this.loading = true;
        if (this.editedIndex > -1) {
          //Object.assign(this.desserts[this.editedIndex], this.editedItem),
          axios
            .put(
              "/api/provider/" + this.editedItem.id_proveedor,
              this.editedItem
            )
            .then((response) => {
              this.loading = false;
              this.getlist();
              this.close();
              this.$swal({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "Proveedor Actualizado correctamente",
                showConfirmButton: false,
                timerProgressBar: true,
                timer: 2500,
              });
            })
            .catch((error) => {
              this.loading = false;
              this.$swal({
                icon: "error",
                title: "Error ",
                text: error.response.data.message,
                timerProgressBar: true,
                timer: 2500,
              });
              this.isLoading = false;
            })
            .finally(() => (this.cargando = false));
        } else {
          axios
            .post("/api/provider", this.editedItem)
            .then((response) => {
              console.log(response);
              this.loading = false;
              if (response.data.allowed.status == true) {
                this.$swal({
                  toast: true,
                  position: "top-end",
                  icon: "success",
                  title: "Proveedor registrado correctamente",
                  showConfirmButton: false,
                  timerProgressBar: true,
                  timer: 2500,
                });
              } else {
                this.$swal({
                  toast: true,
                  position: "top-end",
                  icon: "warning",
                  title: response.data.allowed.message,
                  showConfirmButton: false,
                  timerProgressBar: true,
                  timer: 2500,
                });
              }
              this.getlist();
              this.close();
              console.log(response);
            })
            .catch((error) => {
              this.loading = false;
              this.$swal({
                icon: "error",
                title: "Error ",
                text: error.response.data.message,
                timerProgressBar: true,
                timer: 2500,
              });
              this.isLoading = false;
            })
            .finally(() => (this.cargando = false));
        }
      } else {
        return;
      }
    },

    async obtenerDataDocumentos(doc) {
      try {
        this.$store.commit("LOADER", true);
        this.datosBus.tipoDoc = this.editedItem.id_tipo_doc;
        this.datosBus.numDoc = this.editedItem.nro_doc;
        const rpta = await axios.post("/api/buscarDniRuc", this.datosBus);
        if (rpta.data.success === true) {
          if (this.datosBus.tipoDoc === 1) {
            this.editedItem.nombre = rpta.data.nombre_o_razon_social;
            this.editedItem.direccion = rpta.data.direccion_completa;
            this.$store.commit("LOADER", false);
          } else if (this.datosBus.tipoDoc === 2) {
            this.editedItem.nombre =
              rpta.data.result.Nombres + " " + rpta.data.result.Apellidos;
            this.$store.commit("LOADER", false);
          }
        } else {
          this.$store.commit("LOADER", false);
        }
        this.$store.commit("LOADER", false);
      } catch (error) {
        this.$store.commit("LOADER", false);
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
</style>
