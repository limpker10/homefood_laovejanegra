<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color: #37474F">Recetas</h2>
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
            <v-col cols="5">
            </v-col>
            <v-col cols="3" style="text-align: right;">
             <v-btn small color="primary" class="mr-2" @click="dialog=true;">
              Añadir Receta
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
            itemsPerPageOptions: [25,50,100,1000]
        }"
        :loading="loading" loading-text="Loading... Please wait"
        >
            <template v-slot:[`item.actions`]="{ item }">
                <v-btn small icon @click="getDetail(item.id_receta)">
                    <v-icon small> mdi-clipboard-list-outline</v-icon>
                </v-btn>
                <v-btn small icon @click="editItem(item)">
                    <v-icon small> mdi-pencil</v-icon>
                </v-btn>
                <v-btn small icon @click="deleteItem(item)">
                    <v-icon small> mdi-delete</v-icon>
                </v-btn>
            </template>
            <template v-slot:[`item.type_doc`]="{ item }">
                <div>
                    {{item.tipo_doc.tipo_documento}}
                </div>
            </template>
        </v-data-table>

        <v-dialog
          v-model="dialog"
          max-width="400px"
        >
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
                    <v-col cols="12" sm="12"  md="12" >
                      <v-text-field
                        v-model="editedItem.nombre_receta"
                        label="Receta"
                        placeholder="Receta"
                        :rules="requiredRules"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" dark @click="close" >Cancelar</v-btn>
                <v-btn color="primary" :disabled="!valid" @click="save"> Guardar </v-btn>
              </v-card-actions>
            </v-form>
          </v-card>
        </v-dialog>

        <v-dialog v-model="dialogDelete" max-width="300px">
          <v-card>
            <v-card-title class="subtitle-1">¿Desea eliminar esta Receta?</v-card-title>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" dark @click="closeDelete">Cancelar</v-btn>
              <v-btn color="primary" @click="deleteInsumoConfirm">Aceptar</v-btn>
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <!--Ingredientes-->
        <v-dialog
          v-model="dialogDetail"
          max-width="500px"
        >
          <v-card>
            <v-card-title>
              <span class="headline">Ingredientes</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                    <v-row dense class="pa-2 align-center">

                        <v-col cols="8" class="d-flex justify-content-end align-center">
                            <v-btn small color="primary" class="mr-2" @click="openDialog(2)">
                                Añadir Insumo
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                            <v-btn small color="secondary" class="mr-2" @click="openDialog(1)">
                                Añadir Producto
                                <v-icon>mdi-plus</v-icon>
                            </v-btn>
                        </v-col>
                    </v-row>
                    <v-simple-table>
                        <template v-slot:default>
                        <thead>
                            <tr>
                            <th class="text-left">Insumo</th>
                            <th class="text-left">Cantidad</th>
                            <th class="text-left">UnidadMedida</th>
                            <th class="text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                            v-for="(item,index) in recipe_detail"
                            :key="index"
                            >
                                <td>{{ (item.insumo != null) ? item.insumo.nombre_insumo : item.producto.nombre_producto}}</td>
                                <td>{{ item.cantidad }}</td>
                                <td>{{ item.unidad }}</td>
                                <td>
                                    <v-btn small icon @click="editInsumo(item)">
                                        <v-icon small> mdi-pencil</v-icon>
                                    </v-btn>
                                    <v-btn small icon @click="deleteInsumo(item)">
                                        <v-icon small> mdi-delete</v-icon>
                                    </v-btn>
                                </td>
                            </tr>
                        </tbody>
                        </template>
                    </v-simple-table>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" dark @click="dialogDetail=false;" >Cerrar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <!--Add Ingredientes-->
        <v-dialog
          v-model="dialogAddDetail"
          max-width="500px"
        >
          <v-card>

            <v-card-title>
              <span class="headline">{{ formTitleInsumo }}</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                <v-row>
                  <v-col cols="6" sm="12"  md="6" >
                    <v-autocomplete
                    v-if="editedIndexInsumo == -1"
                    v-model="model"
                    :items="items"
                    :loading="isLoading"
                    :search-input.sync="search"
                    hide-no-data
                    hide-selected
                    item-text="Description"
                    item-value="API"
                    label="Insumo"
                    placeholder="Buscar Insumo"
                    return-object
                    ></v-autocomplete>
                    <v-text-field
                    v-else v-model="(model.nombre_insumo != undefined) ? model.nombre_insumo : model.nombre_producto" label="Insumo" placeholder="Insumo" disabled
                    ></v-text-field>
                  </v-col>
                  <v-col cols="3" sm="12"  md="3" >
                    <v-text-field v-model="model.cantidad" label="Cantidad" placeholder="Cantidad"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="3" sm="12"  md="3" >
                    <v-text-field v-model="model.unidad_medida.simbolo" label="Unidad" placeholder="Unidad" disabled
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" dark @click="dialogAddDetail=false" >Cancelar</v-btn>
              <v-btn color="primary" @click="saveInsumo"> Guardar </v-btn>
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
  components: {
  },
  data: () => ({
    valid: true,
    dialogAddProduct:false,
    searchQuery: '',
    dialog: false,
    dialogDelete: false,
    dialogDetail: false,
    dialogAddDetail: false,
    headers: [
      { text: 'ID', sortable: false, value: 'id_receta' },
      { text: 'Nombre', sortable: false, value: 'nombre_receta' },
      { text: 'Acciones', value: 'actions', align: 'center', sortable: false}
    ],
    desserts: [],
    recipe_detail: [],
    /*editedIndex: -1,*/
    data: {
        data: [],
        total: 0,
    },
    options: {},
    searchData: {},
    itemsperpage: 25,
    page:1,
    productos: [],
    tipos_doc: [],
    loading:false,
    autocomplteLoading:false,
    editedIndex: -1,
      editedItem: {
        codigo: '',
        categoria:''
      },
      defaultItem: {
        codigo: '',
        categoria:''
      },
      editedInsumo: {
      },

    editedIndexInsumo: -1,
    requiredRules: [
      v => !!v || 'Campo obligatorio',
    ],
    descriptionLimit: 60,
    entries: [],
    isLoading: false,
    model: {
        unidad_medida: {}
    },
    search: null,
    API_autocomplete:{
        insumos: '/api/getInsumosFecth',
        productos:'/api/getProductosFetch'
    },
    id_detail:null,
      isProducto: false,
  }),

  computed: {
    formTitle () {
        return this.editedIndex === -1 ? 'Nueva Receta' : 'Editar Receta'
    },
    formTitleInsumo () {
        return this.editedIndexInsumo === -1 ? 'Nuevo Insumo/Producto' : 'Editar Insumo/Producto'
    },
    fields() {
      if (!this.model) return [];
      return Object.keys(this.model).map((key) => {
        return {
          key,
          value: this.model[key],
        };
      });
    },
    items() {
      return this.entries.map((entry) => {
        const Description = (entry.nombre_insumo == undefined) ? entry.nombre_producto : entry.nombre_insumo;
      return Object.assign({}, entry, { Description });
      });
    },
  },

  watch: {
    options(event) {
      this.itemsperpage=event.itemsPerPage;
      this.getlist(event.page, event.itemsPerPage);
    },
    search(val) {
        console.log(val);
      //if (this.items.length > 0) return;
      if (this.isLoading) return;
      this.isLoading = true;
      let url = (this.isProducto) ? this.API_autocomplete.productos : this.API_autocomplete.insumos;
      fetch(url)
        .then((res) => res.json())
        .then((res) => {
          const { count, entries } = res;
          this.count = count;
          this.entries = entries;
        })
        .catch((err) => {
          console.log(err);
        })
        .finally(() => (this.isLoading = false));
    },
  },

  created () {
    this.getlist();
  },

  methods: {
      openDialog(flag){
          this.editedIndexInsumo = -1
          this.isProducto = (flag == 1) ?  true : false;
          this.model.cantidad = "";
          //this.unidad_medida=""
          this.dialogAddDetail=true;
          console.log(this.editedIndexInsumo,'index');
      },
    searchUser() {
      this.searchData = {
        data: this.searchQuery,
      };
      this.data.data= [];
      this.data.total= 0;
      this.data.from=0;
      this.data.to=0;
      this.getlist(1,this.itemsperpage);
    },

    getlist(page,perpage) {
        this.data.data= [];
        this.data.total= 0;
        this.loading = true;
        this.searchData = {
            perpage: perpage,
            data: this.searchQuery,
        };
        this.cargando = true;

        axios.post('/api/getRecetas?page=' + this.page, this.searchData)
        .then(response => {
            this.data = response.data;
            this.page = response.data.current_page;
            this.loading = false;


        }).finally(() => (this.cargando = false));
    },

    researchUser(){
      if(this.searchQuery.length==0){
        this.getlist(1,this.itemsperpage);
      }
    },

    editItem (item) {
        this.editedIndex = this.data.data.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true;
    },

    deleteItem (item) {
        this.editedIndex = this.data.data.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialogDelete = true
    },

    deleteItemConfirm () {
        //this.desserts.splice(this.editedIndex, 1)
        this.loading = true;
        axios.delete('/api/recipe/'+this.editedItem.id_receta, this.editedItem)
            .then(response => {
                this.loading = false;

                this.getlist();
                this.close();
            }).finally(() => (this.cargando = false));
        this.closeDelete();
    },
    save () {
      if(this.$refs.form.validate()){
        this.loading = true;
        if (this.editedIndex > -1) {
            //Object.assign(this.desserts[this.editedIndex], this.editedItem),
            axios.put('/api/recipe/'+this.editedItem.id_receta, this.editedItem)
            .then(response => {
                this.loading = false;
                this.getlist();
                this.close();
            }).finally(() => (this.cargando = false));
        } else {
            axios.post('/api/recipe', this.editedItem)
            .then(response => {
                this.loading = false;
                this.getlist();
                this.close();
            }).finally(() => (this.cargando = false));
        }
      }
      else{
        return;
      }
    },
    close () {
        this.editedIndexInsumo = -1;
        this.dialog = false;
        this.$refs.form.reset();
        this.$nextTick(() => {
            this.editedItem = Object.assign({}, this.defaultItem)
            this.editedIndex = -1
        })
    },

    closeDelete () {
        this.dialogDelete = false
        this.$nextTick(() => {
            this.editedItem = Object.assign({}, this.defaultItem)
            this.editedIndex = -1
        })
    },



    getDetail(id){
        this.dialogDetail = true;
        this.id_detail = id;
        axios.get('/api/recipe_detail/'+id)
            .then(response => {
                this.loading = false;
                this.recipe_detail = response.data;
            }).finally(() => (this.cargando = false));
    },
    closeDetail() {
        this.dialogDelete = false
        this.$nextTick(() => {
            this.editedItem = Object.assign({}, this.defaultItem)
            this.editedIndex = -1
        })
    },



    editInsumo (item) {
        console.log(item);

        this.editedIndexInsumo = this.recipe_detail.indexOf(item)
        this.editedInsumo = Object.assign({}, item)
        let insumo = (item.insumo != null) ? item.insumo : item.producto;
        let data = {item, insumo };
        this.model = Object.assign({},item, insumo);
        this.dialogAddDetail = true;
    },

    deleteInsumo (item) {
        this.editedIndexInsumo = this.recipe_detail.indexOf(item)
        this.editedInsumo = Object.assign({}, item)
        this.dialogDelete = true

    },

    deleteInsumoConfirm () {
        this.loading = true;
        console.log(this.editedInsumo);
        axios.delete('/api/recipe_detail/'+this.editedInsumo.id_receta, this.editedInsumo)
            .then(response => {
                this.loading = false;
            }).finally(() => (this.cargando = false));
        this.closeDelete();
    },
    saveInsumo () {
        this.loading = true;
        if (this.editedIndexInsumo > -1) {
            console.log(this.model);
            //Object.assign(this.desserts[this.editedIndex], this.editedItem),
            axios.put('/api/recipe_detail/'+this.model.id_receta, {id_receta: this.id_detail, id_insumo: this.model.id_insumo, cantidad: this.model.cantidad, unidad: this.model.unidad_medida.simbolo})
            .then(response => {
                this.loading = false;
                this.getDetail(this.model.id_receta);
                this.dialogAddDetail = false;
            }).finally(() => (this.cargando = false));
        } else {
            let dataSend = {
                id_receta: this.id_detail,
                id_insumo: (this.model.id_insumo != null) ? this.model.id_insumo : null,
                id_producto: (this.model.id_producto != null) ? this.model.id_producto : null,
                cantidad: this.model.cantidad,
                unidad: this.model.unidad_medida.simbolo
            }
            axios.post('/api/recipe_detail', dataSend)
            .then(response => {
                this.loading = false;
                this.model = {unidad_medida: {}}
                this.dialogAddDetail = false;
                this.getDetail(this.id_detail)
            }).finally(() => (this.cargando = false));
        }
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
