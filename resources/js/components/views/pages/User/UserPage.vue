<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color:  #AD91FD">Usuarios</h2>
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
                Añadir Usuario
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
              {{item.sucursal.razon_social_sucursal}}
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
                        v-model="editedItem.name"
                        label="Nombre"
                        :rules="requiredRules"
                      ></v-text-field>
                      <v-text-field
                        v-model="editedItem.email"
                        label="Correo"
                        :rules="[...requiredRules, ...emailRules]"
                      ></v-text-field>
                      <v-text-field
                        v-model="editedItem.password"
                        :type="show_password ? 'text' : 'password'"
                        label="Contraseña"
                        prepend-icon="mdi-lock"
                        :rules="editedIndex==-1? requiredRules : []"
                        :append-icon="show_password ? 'mdi-eye' : 'mdi-eye-off'"
                        @click:append="show_password = !show_password"
                      ></v-text-field>
                      <v-text-field
                        v-model="editedItem.password_confirm"
                        :type="show_cpassword ? 'text' : 'password'"
                        label="Confirmar Contraseña"
                        prepend-icon="mdi-lock"
                        :rules="editedIndex==-1? [...requiredRules, ...confirmPasswordRules] : confirmPasswordRules"
                        :append-icon="show_cpassword ? 'mdi-eye' : 'mdi-eye-off'"
                        @click:append="show_cpassword = !show_cpassword"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6">
                      <v-select
                        :items="sucursales"
                        label="Sucursal"
                        placeholder="Selecciona una sucursal"
                        v-model="editedItem.id_sucursal"
                        item-text="razon_social_sucursal"
                        item-value="id_sucursal"
                        :rules="requiredRules"
                      ></v-select>
                      <v-select
                        :items="role_list"
                        label="Rol"
                        placeholder="Selecciona un rol"
                        v-model="editedItem.role"
                        item-text="title"
                        item-value="name"
                        :rules="requiredRules"
                      ></v-select>
                      <v-text-field
                        v-model="editedItem.access_code"
                        label="Código Acceso"
                        :rules="[...requiredRules]"
                      ></v-text-field>
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
                >¿Desea eliminar este Usuario?</v-card-title
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
    show_password: false,
    show_cpassword: false,
    headers: [
      { text: "ID", sortable: false, value: "id" },
      { text: "Nombre", sortable: false, value: "name" },
      { text: "Correo", sortable: false, value: "email" },
      { text: "Sucursal", sortable: false, value: "sucursal" },
      //{ text: "Rol", sortable: false, value: "rol" },
      { text: "Acciones", value: "actions", sortable: false, align: "right" },
    ],
    sucursales: [],
    roles: [],
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
      usuario: "",
      role: ""
    },
    defaultItem: {
      codigo: "",
      usuario: "",
      role: ""
    },
    role_list: [],
    requiredRules: [(v) => !!v || "Campo obligatorio"],
    emailRules: [(v) => !v || /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(v) || 'Debe escribir un correo valido'],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nuevo Usuario" : "Editar Usuario";
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
      
    confirmPasswordRules(){
      return [(v) => v === this.editedItem.password || "Las contraseñas deben coincidir"]
    }
  },

  watch: {
    options(event) {
      this.itemsperpage = event.itemsPerPage;
      this.getlist(event.page, event.itemsPerPage);
    },
  
  },

  created() {
    this.getSucursales();
    this.getroles();
    this.getUser();
    this.getlist();
  },

  methods: {
    async getroles( ){
        try{
            const role = await API.roles.list();
            this.role_list = role.data;
        }catch(error){
            console.error(error.response.status);
        }
    },
    reset() {
      this.dialog = true;
      this.editedIndex = -1;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
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
    /*getRoles() {
      axios
        .get("/api/getRoles/")
        .then((response) => {
          this.roles = response.data;
        })
        .catch((resonse) => {
          console.log("error: " + response.data);
        });
    },*/
    getlist(page, perpage) {
      this.$store.commit('LOADER',true);
      this.data.data = [];
      this.data.total = 0;
      this.loading = true;
      this.searchData = {
        perpage: perpage,
        data: this.searchQuery,
      };
      this.cargando = true;
      axios
        .post("api/getUsuarios?page=" + this.page, this.searchData)
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
        })
        .finally(() => (this.cargando = false));
    },
    editItem(item) {
      this.editedIndex = this.data.data.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.editedItem.role = item.roles[0].name;
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
        .delete("api/users/" + this.editedItem.id, this.editedItem)
        .then((response) => {
          this.loading = false;
          this.getlist();
          this.close();
          this.$swal({
              toast: true,
              position: 'top-end',
              icon:'success',
              title:'Usuario eliminado correctamente',
              showConfirmButton:false,
              timerProgressBar:true,
              timer:2500
          });

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
    save () {
      if(this.$refs.form.validate()){
        this.loading = true;
        //this.editedItem.role = [this.editedItem.role];
        if (this.editedIndex > -1) {
            //Object.assign(this.desserts[this.editedIndex], this.editedItem),
            axios.put('api/users/'+this.editedItem.id,this.editedItem)
            .then(response => {
                this.loading = false;
                this.getlist();
                this.close();
                this.$swal({
                    toast: true,
                    position: 'top-end',
                    icon:'success',
                    title:'Usuario actualizado correctamente',
                    showConfirmButton:false,
                    timerProgressBar:true,
                    timer:2500
                });
            }).catch((error) => {
              this.$swal({
                icon:'error',
                title:'Opps!',
                text: error.response.data.message,
                timerProgressBar:true,
                timer:2500
              })
            })
            .finally(() => (
              this.cargando = false
            ));
        } else {
            axios.post('api/users', this.editedItem)
            .then(response => {
                this.loading = false;
                this.getlist();
                this.close();
                this.$swal({
                    toast: true,
                    position: 'top-end',
                    icon:'success',
                    title:'Usuario registrado correctamente',
                    showConfirmButton:false,
                    timerProgressBar:true,
                    timer:2500
                });
            })
            .catch((error) => {
              this.$swal({
                icon:'error',
                title:'Opps!',
                text: error.response.data.message,
                timerProgressBar:true,
                timer:2500
              });
              this.isLoading = false;
            })
            .finally(() => (
              this.cargando = false
            ));
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
