<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color:  #AD91FD">Insumos</h2> 
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
             <v-btn small color="primary" class="mr-2" @click="openDialog()">
              Añadir Insumos
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
        :loading="loading" loading-text="Loading... Please wait"
        >
        

            <template v-slot:[`item.actions`]="{ item }">
                <v-btn small icon @click="editItem(item)">
                    <v-icon small> mdi-pencil</v-icon>
                </v-btn>
                <v-btn small icon @click="deleteItem(item)">
                    <v-icon small> mdi-delete</v-icon>
                </v-btn>
            </template>
            <template v-slot:[`item.id_categoria`]="{ item }">
                <div>
                    {{(item.categoria != null) ? item.categoria.nombr : "SIN CATEGORIA"}}
                </div>
            </template>
            <template v-slot:[`item.id_unidad_medida`]="{ item }">
                <div>
                    {{item.unidad_medida.unidad_medida}}
                </div>
            </template>
        </v-data-table>

        <v-dialog
          v-model="dialog"
          max-width="600px"
        >
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>
            <v-card-text>
        
              <v-form
                ref="form"
                v-model="valid"
                lazy-validation
              >
                <v-container>
                    <v-row>
                    <v-col cols="12" sm="6"  md="4" >
                        <v-select :items="categorias" label="Categoría" placeholder="Selecciona una Categoría" v-model="editedItem.id_categoria" required item-text="nombre" item-value="id_categoria"></v-select>
                        <v-text-field  
                          v-model="editedItem.nombre_insumo" 
                          label="Nombre Insumo" 
                          placeholder="Nombre Insumo" 
                          :rules="requiredRules"
                        ></v-text-field>
                        <v-text-field 
                          v-model="editedItem.stock" 
                          label="Stock" 
                          placeholder="Stock" 
                          :rules="requiredRules"
                        ></v-text-field>
                        <v-text-field 
                          v-model="editedItem.precio" 
                          label="Precio" 
                          placeholder="Precio" 
                          :rules="requiredRules"
                        ></v-text-field>
                        <v-text-field 
                          v-model="editedItem.cantidad_medida" 
                          label="Cantidad Medida" 
                          placeholder="Cantidad Medida" 
                          :rules="requiredRules"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6"  md="4" >
                        <v-select :items="unidades" 
                          label="Unidad de Medida" 
                          placeholder="Selecciona una Unidad de Medida" 
                          v-model="editedItem.id_unidad_medida" 
                          item-text="unidad_medida" 
                          item-value="id_unidad_medida"
                          :rules="requiredRules"></v-select>
                        <v-text-field  
                          v-model="editedItem.codigo_insumo"
                          label="Código" 
                          placeholder="Código" 
                          :rules="requiredRules"
                        ></v-text-field>
                        <v-text-field 
                          v-model="editedItem.stock_minimo" 
                          label="Stock Mínimo" 
                          placeholder="Stock Mínimo" 
                          :rules="requiredRules"
                        ></v-text-field>
                        <v-text-field 
                          v-model="editedItem.medida" 
                          label="Medida" 
                          placeholder="Medida" 
                          :rules="requiredRules"
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="4" md="4">
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
              </v-form>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" dark @click="close" >Cancelar</v-btn>
              <v-btn color="primary" :disabled="!valid" @click="save"> Guardar </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="dialogDelete" max-width="300px">
          <v-card>
            <v-card-title class="subtitle-1">¿Desea eliminar este Insumo?</v-card-title>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" dark @click="closeDelete">Cancelar</v-btn>
              <v-btn color="primary" @click="deleteItemConfirm" :disabled="!valid">Aceptar</v-btn>
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
  components: {
  },
  data: () => ({
    sucursales: [],
    sucursales_selected: [],
    valid: true,
    searchQuery: '',
    dialog: false,
    dialogDelete: false,
    headers: [
      { text: 'ID', sortable: false, value: 'id_insumo' },
      { text: 'Código', sortable: false, value: 'codigo_insumo' },
      { text: 'Nombre', sortable: false, value: 'nombre_insumo' },
      { text: 'Categoría', sortable: false, value: 'id_categoria' },
      { text: 'Unidad Medida', sortable: false, value: 'id_unidad_medida' },
      { text: 'Acciones', value: 'actions', align: 'center', sortable: false, align:'right' }
    ],
    tab:0,
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
    productos: [],
    categorias: [],
    unidades: [],
    loading:false,
    autocomplteLoading:false,
    showName:true,
    showAdd:true,
    editedIndex: -1,
      editedItem: {
        codigo: '',
        categoria:'',
        nro_doc:'',
        stock: 0,
        stock_minimo: 0
      },
      defaultItem: {
        codigo: '',
        categoria:'',
        nro_doc:'',
        stock: 0,
        stock_minimo: 0
      },

    requiredRules: [
        v => !!v || 'Campo obligatorio',
      ],
  }),

  computed: {
    formTitle () {
        return this.editedIndex === -1 ? 'Nuevo Insumo' : 'Editar Insumo'
    },
    sucursales_selected_all: {
      set(val){
          this.sucursales_selected = [];
          if (val == true) {
              this.sucursales_selected = this.sucursales.map((s) => s.id_sucursal);
          }
      },
      get(){
          return this.sucursales_selected.length === this.sucursales.length;
      }
    },
  },

  watch: {
    options(event) {
      this.itemsperpage=event.itemsPerPage;
      this.getlist(event.page, event.itemsPerPage);
    },

  },

  created () {
    this.getCategorias();
    this.getUnidadesMedida();
    this.getlist();
    this.getSucursales();
  },

  methods: {
    getSucursales() {
      axios
        .get("/api/branch")
        .then((response) => {
          this.sucursales = response.data;
          let selected = [];
          for (let sucursal in this.sucursales) {
            selected.push(this.sucursales[sucursal].id_sucursal);
          }
          this.sucursales_selected = selected;
        })
        .catch((resonse) => {
          console.log("error: " + response.data);
        });
    },
    getCategorias(){
        axios.get('/api/getCategoriasPorTipo/2')
        .then((response) => {
            this.categorias = response.data;
        })
        .catch( resonse => {
            console.log('error: '+ response.data);
        }) 
    },
    
    getUnidadesMedida(){
        axios.get('/api/unitmeasure')
        .then((response) => {
            this.unidades = response.data;
        })
        .catch( resonse => {
            console.log('error: '+ response.data);
        }) 
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
        this.$store.commit('LOADER',true);
        this.data.data= [];
        this.data.total= 0;
        this.loading = true;
        this.searchData = {
            perpage: perpage,
            data: this.searchQuery,
        };
        this.cargando = true;
        
        axios.post('/api/getInsumos?page=' + this.page, this.searchData)
        .then(response => {
            this.data = response.data;
            this.page = response.data.current_page;
            this.loading = false;
            this.$store.commit('LOADER',false);
        });
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
        axios.delete('/api/supplies/'+this.editedItem.id_insumo, this.editedItem)
            .then(response => {
                this.loading = false;
                
                this.getlist();
                this.close();
            });
        this.closeDelete();
    },

    close () {
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
    openDialog(){
      this.sucursales_selected =this.sucursales.map(e => e.id_sucursal);
      this.dialog = true;
    },
    save () {
      if(this.$refs.form.validate()){
        this.loading = true;
        if (this.editedIndex > -1) {
            //Object.assign(this.desserts[this.editedIndex], this.editedItem),
            axios.put('/api/supplies/'+this.editedItem.id_insumo, this.editedItem)
            .then(response => {
                this.loading = false;
                this.getlist();
                this.close();
                this.$swal({
                    toast: true,
                    position: 'top-end',
                    icon:'success',
                    title:'Insumo actualizada correctamente',
                    showConfirmButton:false,
                    timerProgressBar:true,
                    timer:2500
                });
            });
        } else {
            console.log(this.editedItem.sucursales = this.sucursales_selected)
            axios.post('/api/supplies', this.editedItem)
            .then(response => {
                this.loading = false;
                this.getlist();
                this.close();
                this.$swal({
                    toast: true,
                    position: 'top-end',
                    icon:'success',
                    title:'Insumo registrada correctamente',
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

  }
}
</script>
<style lang="scss" scoped>
  .btn-actions{
    background-color: #fff !important;
    color: #121212;
  }
</style>
