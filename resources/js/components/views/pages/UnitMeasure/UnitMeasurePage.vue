<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color:  #AD91FD">Unidades de Medida</h2> 
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
              Añadir Unidad de Medida
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
                         v-model="editedItem.unidad_medida" 
                         label="Unidad de Medida" 
                         placeholder="Unidad de Medida" 
                         :rules="requiredRules"
                      ></v-text-field>
                      <v-text-field
                         v-model="editedItem.simbolo" 
                         label="Símbolo" 
                         placeholder="Símbolo" 
                         :rules="requiredRules"
                      ></v-text-field>
                      <v-text-field
                         v-model="editedItem.codigo_sunat" 
                         label="Código de Sunat" 
                         placeholder="Código de Sunat" 
                         :rules="requiredRules"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" dark @click="close" >Cancelar</v-btn>
                <v-btn color="primary" @click="save" :disabled="!valid"> Guardar </v-btn>
              </v-card-actions>
            </v-form>
          </v-card>
        </v-dialog>

        <v-dialog v-model="dialogDelete" max-width="300px">
          <v-card>
            <v-card-title class="subtitle-1">¿Desea eliminar esta Unidad de Medida?</v-card-title>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" dark @click="closeDelete">Cancelar</v-btn>
              <v-btn color="primary" @click="deleteItemConfirm">Aceptar</v-btn>
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
    valid: true,
    searchQuery: '',
    dialog: false,
    dialogDelete: false,
    headers: [
      { text: 'ID', sortable: false, value: 'id_unidad_medida' },
      { text: 'Nombre', sortable: false, value: 'unidad_medida' },
      { text: 'Código Sunat', sortable: false, value: 'codigo_sunat' },
      { text: 'Simbolo', sortable: false, value: 'simbolo' },
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

    requiredRules: [
      v => !!v || 'Campo obligatorio',
    ],
  }),

  computed: {
    formTitle () {
        return this.editedIndex === -1 ? 'Nueva Unidad de Medida' : 'Editar Unidad de Medida'
    },
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
        
        axios.post('/api/getUnidadesMedida?page=' + this.page, this.searchData)
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
        this.loading = true;
        axios.delete('/api/unitmeasure/'+this.editedItem.id_unidad_medida, this.editedItem)
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

    save () {
        if(this.$refs.form.validate()){
          this.loading = true;
          if (this.editedIndex > -1) {
              axios.put('/api/unitmeasure/'+this.editedItem.id_unidad_medida, this.editedItem)
              .then(response => {
                  this.loading = false;
                  this.getlist();
                  this.close();
                  this.$swal({
                      toast: true,
                      position: 'top-end',
                      icon:'success',
                      title:'Unidad actualizada correctamente',
                      showConfirmButton:false,
                      timerProgressBar:true,
                      timer:2500
                  });
              });
          } else {
              axios.post('/api/unitmeasure', this.editedItem)
              .then(response => {
                  this.loading = false;
                  this.getlist();
                  this.close();
                  this.$swal({
                      toast: true,
                      position: 'top-end',
                      icon:'success',
                      title:'Unidad registrada correctamente',
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
  *{
    text-transform: none !important;
    //font-size: 12px !important;
    //font-family:'Quicksand', sans-serif  !important;
    }
</style>
