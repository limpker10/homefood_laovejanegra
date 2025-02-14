<template lang="">
    <v-container fluid>
      <div class="d-flex align-center py-3">
        <div>
        <h2 style="color:  #AD91FD">Zonas</h2> 
        <!--<v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>-->
        </div>
        <v-spacer></v-spacer>
      </div>
    <v-data-table
      :headers="headers"
      :items="zonas.data"
      class="elevation-1"
      :server-items-length="pagination.total"
      :page="pagination.page"
      :options.sync="options"
      :items-per-page="10"
      :footer-props="{
        itemsPerPageOptions: [10, 25, 50],
        itemsPerPageText: 'Zonas por página',
      }"
      :loading="loading"
      loading-text="Cargando... Por favor espera"
    >
        <template v-slot:top>
            <v-toolbar
                flat
            >
                <v-spacer></v-spacer>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                         <v-icon
                              color="primary"
                              class="mr-2"
                              v-bind="attrs"
                              v-on="on"
                              @click="initialize(1,10)"
                         >
                              mdi-refresh
                         </v-icon>
                    </template>
                    Recargar
               </v-tooltip>
               <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                         <v-icon
                              color="primary"
                              class="mr-2"
                              v-bind="attrs"
                              v-on="on"
                              @click="dialog=true;"
                         >
                              mdi-plus
                         </v-icon>
                    </template>
                    Agregar Habitacion
               </v-tooltip>
                <v-dialog
                v-model="dialog"
                max-width="500px"
                >
                        <v-card>
                            <v-card-title>
                            <span class="headline">{{ formTitle }}</span>
                            </v-card-title>

                            <v-card-text>
                            <v-container>
                                <v-row>
                                  <v-col
                                      cols="12"
                                      sm="12"
                                      md="12"
                                  >
                                      <v-text-field
                                        v-model="editedItem.nombre_zona"
                                        label="Nombre"
                                      ></v-text-field>
                                  </v-col>
                                </v-row>
                            </v-container>
                            </v-card-text>

                            <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="error"
                                @click="close"
                            >
                                Cancelar
                            </v-btn>
                            <v-btn
                                color="primary"
                                @click="save"
                            >
                                Guardar
                            </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <v-dialog v-model="dialogDelete" max-width="500px">
                        <v-card>
                            <v-card-title class="headline">¿Estas seguro de eliminar esta zona?</v-card-title>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="error" dark @click="closeDelete">Cancelar</v-btn>
                                <v-btn color="primary" dark @click="deleteItemConfirm">Confirmar</v-btn>
                                <v-spacer></v-spacer>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
            </v-toolbar>
    </template>
    <template v-slot:[`item.actions`]="{ item }">
      <v-icon
        small
        class="mr-2"
        
        @click="editItem(item)"
      >
        mdi-pencil
      </v-icon>
      <v-icon
        
        small
        @click="deleteItem(item)"
      >
        mdi-delete
      </v-icon>
    </template>
    <template v-slot:no-data>
      <v-btn
        color="primary"
        @click="initialize"
      >
        Reiniciar
      </v-btn>
    </template>
  </v-data-table>
    </v-container>
</template>
<script>
  export default {
    data: () => ({

      searchQuery:'',
      options:{},
      sendData:{},
      pagination:{
        total:0,
        page :1,
        perpage:10,
        current_page:1,
        last_page:1,
      },
      loading:false,
      dialog: false,
      dialogDelete: false,
      headers: [
        {
          text: 'ID',
          align: 'start',
          sortable: false,
          value: 'id_zona_hotel',
        },
        { 
            text: 'Nombre',
            align: 'center',
            value: 'nombre_zona'
        },
        {
            text: 'Actions',
            align: 'center',
            value: 'actions', align: 'center',
            sortable: false
        },
      ],
      zonas: [],
      editedIndex: -1,
      editedItem: {
        id_zona_hotel:0,
        nombre_zona: '',
      },

      defaultItem: {
        id_zona_hotel:0,
        nombre_zona: '',
      },
    }),
    
    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'Nueva Zona' : 'Editar Zona'
      },
    },

    watch: {
      dialog (val) {
        val || this.close()
      },
      dialogDelete (val) {
        val || this.closeDelete()
      },
      options(event) {
        console.log(event)
          this.pagination.perpage = event.itemsPerPage;
          //this.pagination.page = event.page;
          this.initialize(event.page, event.itemsPerPage);
      },
    },

    created () {
      //this.initialize()
    },

    methods: {
      async initialize (page,perpage) {
        this.pagination.page = page;
        this.loading = true;
        this.sendData = {
            perpage: perpage,
        };
        const response = await axios.post('/api/getZonasHotel?page='+page, this.sendData);
        this.pagination.current_page = response.data.current_page;
        this.zonas = response.data;
        this.pagination.last_page =response.data.last_page;
        this.pagination.total = response.data.total;
        if(this.pagination.current_page > this.pagination.last_page){
          console.log(this.pagination.last_page);
           const response2 = await axios.post('/api/getZonasHotel?page='+this.pagination.last_page, this.sendData);
           this.pagination.current_page = response2.data.current_page;
           this.zonas = response2.data;
           this.pagination.total = response2.data.total;
           this.pagination.last_page =response2.data.last_page;
        }
        this.loading = false;
      },

      editItem (item) {
        this.editedIndex = 1;
        this.editedItem.id_zona_hotel = item.id_zona_hotel;
        this.editedItem.nombre_zona = item.nombre_zona;
        console.log(item);
        //this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        this.editedItem.id_zona_hotel = item.id_zona_hotel;
        //this.editedIndex = this.desserts.indexOf(item)
        //this.editedItem = Object.assign({}, item)
        this.dialogDelete = true
      },

      async deleteItemConfirm () {
        const rpta =  await axios.delete("/api/zonehotel/"+this.editedItem.id_zona_hotel);
        console.log(rpta);
        this.editedItem = Object.assign({},this.defaultItem);
        this.initialize(this.pagination.current_page,this.pagination.perpage);
        //console.log(this.editedItem);
        this.closeDelete()
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

      async save () {
        
        if (this.editedIndex === -1) {
  
          const rpta = await axios.post("/api/zonehotel",this.editedItem);
          console.log(rpta);
          this.initialize(this.pagination.current_page,this.pagination.perpage);
          this.editedItem = Object.assign({},this.defaultItem);
        } else {
          console.log(this.editedItem.id_zona_hotel);
          const rpta = await axios.put("/api/zonehotel/"+this.editedItem.id_zona_hotel,this.editedItem);
          console.log(rpta);
          this.initialize(this.pagination.current_page,this.pagination.perpage);
          this.editedItem = Object.assign({},this.defaultItem);
        }
        console.log(this.editedItem);
        this.close()
      },
    },
  }
</script>
<style lang="">
    
</style>