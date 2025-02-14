<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color: #37474F">Productos Sucursal</h2> 
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
                    <v-icon small> mdi-office-building-marker</v-icon>
                </v-btn>
            </template>
        </v-data-table>

        <!--Sucursales-->
        <v-dialog
          v-model="dialogDetail"
          max-width="500px"
        >
          <v-card>
            <v-card-title>
              <span class="headline">Sucursales</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                    <v-row dense class="pa-2 align-center">
                        <v-col cols="8">
                        </v-col>
                        <v-col cols="4" style="text-align: right;">
                        <v-btn small color="primary" class="mr-2" @click="dialogAddDetail=true;">
                        Añadir Sucursal
                        <v-icon>mdi-plus</v-icon>
                        </v-btn>            
                    </v-col>
                    </v-row>
                    <v-simple-table>
                        <template v-slot:default>
                        <thead>
                            <tr>
                            <th class="text-left">Producto</th>
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
                                <td>{{ item.insumo.nombre_insumo }}</td>
                                <td>{{ item.cantidad }}</td>
                                <td>{{ item.unidad }}</td>
                                <td>
                                    <v-btn small icon @click="editSucursal(item)">
                                        <v-icon small> mdi-pencil</v-icon>
                                    </v-btn>
                                    <v-btn small icon @click="deleteSucursal(item)">
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
        <!--Add Sucursales-->
        <v-dialog
          v-model="dialogAddDetail"
          max-width="500px"
        >
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitleSucursal }}</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                <v-row>
                  <v-col cols="6" sm="12"  md="6" >
                    <v-autocomplete
                    v-if="editedIndexSucursal==-1"
                    v-model="model"
                    :items="items"
                    :loading="isLoading"
                    :search-input.sync="search"
                    hide-no-data
                    hide-selected
                    item-text="Description"
                    item-value="API"
                    label="Sucursal"
                    placeholder="Buscar Sucursal"
                    return-object
                    ></v-autocomplete>
                    <v-text-field v-else v-model="model.nombre_insumo" label="Sucursal" placeholder="Sucursal" disabled 
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
              <v-btn color="primary" @click="saveSucursal"> Guardar </v-btn>
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
    searchQuery: '',
    dialog: false,
    dialogDelete: false,
    dialogDetail: false,
    dialogAddDetail: false,
    headers: [
      { text: 'ID', sortable: false, value: 'id_producto' },
      { text: 'Código', sortable: false, value: 'codigo_producto' },
      { text: 'Nombre', sortable: false, value: 'nombre_producto' },
      { text: 'Categoría', sortable: false, value: 'id_categoria' },
      { text: 'Unidad Medida', sortable: false, value: 'id_unidad_medida' },
      { text: 'Precio', sortable: false, value: 'precio' },
      { text: 'Tipo Producto', sortable: false, value: 'tipo_producto_combo' },
      { text: 'Acciones', value: 'actions', align: 'center', sortable: false, align:'right' }
    ],
    desserts: [],
    recipe_detail: [],
    editedIndex: -1,
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
      editedSucursal: {
      },

    editedIndexSucursal: -1,
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
    id_detail:null
  }),

  computed: {
    formTitle () {
        return this.editedIndex === -1 ? 'Nueva Receta' : 'Editar Receta'
    },
    formTitleSucursal () {
        return this.editedIndexSucursal === -1 ? 'Nuevo Sucursal' : 'Editar Sucursal'
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
        const Description = entry.nombre_insumo;
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
      if (this.items.length > 0) return;
      if (this.isLoading) return;
      this.isLoading = true;
      //fetch("/api/getSucursals")
      //fetch("https://api.publicapis.org/entries")
      fetch("/api/getSucursalsFecth")
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
        
        axios.post('/api/getProductos?page=' + this.page, this.searchData)
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
    },
    close () {
        this.dialog = false
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

    

    editSucursal (item) {
        this.editedIndexSucursal = this.recipe_detail.indexOf(item)
        //this.editedSucursal = Object.assign({}, item)
        let insumo = item.insumo;
        let data = {item, insumo };
        this.model = Object.assign({},item, insumo);
        this.dialogAddDetail = true;
    },

    deleteSucursal (item) {
        this.editedIndexSucursal = this.recipe_detail.indexOf(item)
        this.editedSucursal = Object.assign({}, item)
        this.dialogDelete = true
    },

    deleteSucursalConfirm () {
        this.loading = true;
        axios.delete('/api/recipe_detail/'+this.editedSucursal.id_receta, this.editedSucursal)
            .then(response => {
                this.loading = false;
            }).finally(() => (this.cargando = false));
        this.closeDelete();
    },
    saveSucursal () {
        this.loading = true;
        if (this.editedIndex > -1) {
            //Object.assign(this.desserts[this.editedIndex], this.editedItem),
            axios.put('/api/recipe_detail/'+this.editedIndexSucursal.id_receta, {id_receta: this.id_detail, id_insumo: this.model.id_insumo, cantidad: this.model.cantidad, unidad: this.model.unidad_medida.simbolo})
            .then(response => {
                this.loading = false;
                this.getDetail(this.editedIndexSucursal.id_receta)
            }).finally(() => (this.cargando = false));
        } else {
            axios.post('/api/recipe_detail', {id_receta: this.id_detail, id_insumo: this.model.id_insumo, cantidad: this.model.cantidad, unidad: this.model.unidad_medida.simbolo})
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
