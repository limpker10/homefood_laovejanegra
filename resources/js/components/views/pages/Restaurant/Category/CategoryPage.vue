<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color:  #AD91FD">Categorías</h2>
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
                Añadir Categoría
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
            <template v-slot:[`item.tipo`]="{ item }">
              <div v-if="item.tipo_producto_insumo == 1">Producto</div>
              <div v-if="item.tipo_producto_insumo == 2">Insumo</div>
            </template>
            <template v-slot:[`item.color`]="{ item }">
              <div
                :style="[
                  {
                    'background-color': item.color,
                    'border-radius': 5 + 'px',
                    height: 20 + 'px',
                    width: 40 + 'px',
                  },
                ]"
              ></div>
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

          <v-dialog v-model="dialog" max-width="700px">
            <v-card>
               <v-form
              ref="form"
              v-model="valid"
              lazy-validation
            >
              <v-card-title>
                <span class="headline">{{ formTitle }}</span>
              </v-card-title>
              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" sm="6" md="6">
                      <v-text-field
                        v-model="editedItem.nombre"
                        label="Nombre"
                        :rules="requiredRules"
                      ></v-text-field>
                      <v-text-field
                        v-model="editedItem.color"
                        v-mask="mask"
                        label="Color"
                        :rules="requiredRules"
                      >
                        <template v-slot:append>
                          <v-menu
                            v-model="menu"
                            top
                            nudge-bottom="105"
                            nudge-left="16"
                            :close-on-content-click="false"
                          >
                            <template v-slot:activator="{ on }">
                              <div :style="swatchStyle" v-on="on" />
                            </template>
                            <v-card>
                              <v-card-text class="pa-0">
                                <v-color-picker
                                  v-model="editedItem.color"
                                  flat
                                />
                              </v-card-text>
                            </v-card>
                          </v-menu>
                        </template>
                      </v-text-field>
                      <v-select
                        :items="tipoinsumo"
                        label="Tipo"
                        placeholder="Selecciona un tipo"
                        v-model="editedItem.tipo_producto_insumo"
                        item-text="name"
                        item-value="id"
                        :rules="requiredRules"
                      ></v-select>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <p>Sucursal</p>
                      <v-checkbox class="my-0 py-0"
                        v-model="sucursales_selected_all"
                        label="Todas las sucursales"
                      ></v-checkbox>
                      <v-list  class="my-0 py-0">
                        <v-list-item class="my-0 py-0"
                          v-for="item in sucursales"
                          :key="item.id_sucursal"
                        >
                          <v-checkbox class="my-0 py-0"
                            v-model="sucursales_selected"
                            :label="item.nombre_sucursal"
                            :value="item.id_sucursal"
                          ></v-checkbox>
                        </v-list-item>
                      </v-list>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" dark @click="close"
                  >Cancelar</v-btn
                >
                <v-btn color="primary" @click="save" :disabled="!valid"> Guardar </v-btn>
              </v-card-actions>
              
            </v-form>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" max-width="300px">
            <v-card>
              <v-card-title class="subtitle-1"
                >¿Desea eliminar esta Categoría?</v-card-title
              >
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" dark @click="closeDelete"
                  >Cancelar</v-btn
                >
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
    valid: true,
    searchQuery: '',
    dialog: false,
    dialogDelete: false,
    dialogSucursal: false,
    headers: [
      { text: "ID", sortable: false, value: "id_categoria" },
      { text: "Categoria", sortable: false, value: "nombre" },
      { text: "Tipo", sortable: false, value: "tipo" },
      { text: "Color", sortable: false, value: "color" },
      { text: "Acciones", value: "actions", sortable: false, align: "right" },
    ],
    sucursales: [],
    sucursales_selected: [],
    tipoinsumo: [
      { id: 1, name: "Producto" },
      { id: 2, name: "Insumo" },
    ],
    editedIndex: -1,
    data: {
      data: [],
      total: 0,
    },
    options: {},
    searchData: {},
    currentUser: {},
    itemsperpage: 25,
    page: 1,
    loading: false,
    editedIndex: -1,
    editedItem: {
      codigo: "",
      categoria: "",
    },
    defaultItem: {
      codigo: "",
      categoria: "",
      color: "#1976D2FF",
    },

    requiredRules: [(v) => !!v || "Campo obligatorio"],
    mask: "!#XXXXXXXX",
    menu: false,
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nueva Categoría" : "Editar Categoría";
    },
    swatchStyle() {
      const { color, menu } = this;
      return {
        backgroundColor: this.editedItem.color,
        cursor: "pointer",
        height: "30px",
        width: "30px",
        borderRadius: menu ? "50%" : "4px",
        transition: "border-radius 200ms ease-in-out",
      };
    },
    sucursales_selected_all: {
      get: function () {
        if(this.sucursales.length === this.sucursales_selected.length){
          return true;
        }else{
          return false;
        }

      },
      set: function (value) {
        let selected = [];
        if (value) {
          for (let sucursal in this.sucursales) {
            selected.push(this.sucursales[sucursal].id_sucursal);
          }
        this.sucursales_selected = selected;
        }else{

          this.sucursales_selected = [this.currentUser.id_sucursal];
        }
      },
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
        this.editedItem.color = "#1976D2FF";
        this.sucursales_selected = [this.currentUser.id_sucursal];
        this.sucursales_selected_all = false;
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
    getSucursalesPorCategoria(id_categoria) {
      axios
        .get("/api/getSucursalesPorCategoria/" + id_categoria)
        .then((response) => {
          this.sucursales_selected = response.data;
        })
        .catch((resonse) => {
          console.log("error: " + response.data);
        });
    },
    getlist(page, perpage) {
      this.$store.commit('LOADER',true);
      this.data.data = [];
      this.data.total = 0;
      this.loading = true;
      this.searchData = {
        perpage: perpage,
        data: this.searchQuery,
      };
      axios
        .post("api/getCategorias?page=" + this.page, this.searchData)
        .then((response) => {
          this.data = response.data;
          this.page = response.data.current_page;
          this.loading = false;
          
          this.$store.commit('LOADER',false);
        });
    },

    researchData() {
      if (this.searchQuery.length == 0) {
        this.getlist(1, this.itemsperpage);
      }
    },

    changeStatus(id, status) {
      if (status == 0) {
        status = 1;
      } else {
        status = 0;
      }
      axios
        .put("/api/changeStatus/" + id, {
          table: "categoria_productos",
          id: "id_categoria",
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
        });
    },

    asignarSucursal(item) {
      this.editedIndex = this.data.data.indexOf(item);
      this.dialogSucursal = true;
    },

    editItem(item) {
      this.editedIndex = this.data.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.getSucursalesPorCategoria(item.id_categoria)
      this.dialog = true;
    },

    deleteItem(item) {
      this.editedIndex = this.data.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialogDelete = true;
    },

    deleteItemConfirm() {
      this.loading = true;
      axios
        .delete("api/category/" + this.editedItem.id_categoria, this.editedItem)
        .then((response) => {
          this.loading = false;
          this.getlist();
          this.close();
          this.$swal({
                      toast: true,
                      position: 'top-end',
                      icon:'success',
                      title:'Categoria eliminada correctamente',
                      showConfirmButton:false,
                      timerProgressBar:true,
                      timer:2500
                  });

        });
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
    save () {
      if(this.$refs.form.validate()){
        this.loading = true;
        this.editedItem.color = this.editedItem.color.substr(0, 7)
        let data = {
          item: this.editedItem,
          sucursales: this.sucursales_selected,
        };
        if (this.editedIndex > -1) {
            //Object.assign(this.desserts[this.editedIndex], this.editedItem),
            axios.put('api/category/'+this.editedItem.id_categoria,data)
            .then(response => {
                this.loading = false;
                this.getlist();
                this.close();
                this.$swal({
                    toast: true,
                    position: 'top-end',
                    icon:'success',
                    title:'Categoría actualizada correctamente',
                    showConfirmButton:false,
                    timerProgressBar:true,
                    timer:2500
                });
            });
        } else {
            axios.post('api/category', data)
            .then(response => {
                this.loading = false;
                this.getlist();
                this.close();
                this.$swal({
                    toast: true,
                    position: 'top-end',
                    icon:'success',
                    title:'Categoría registrada correctamente',
                    showConfirmButton:false,
                    timerProgressBar:true,
                    timer:2500
                });
            });
        }
      }
      else{
        return;
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
