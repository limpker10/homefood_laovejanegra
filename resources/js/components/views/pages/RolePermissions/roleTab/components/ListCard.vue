<template>
  <div>
     <v-card v-if="!loading">
      <v-card-title>Roles</v-card-title>
      <v-card-text>
        <div class="d-flex flex-column flex-sm-row">
            <div class="flex-grow-1 pt-2 pa-sm-2" style="padding: 0px 16px !important;">
                <v-data-table
                    :headers="headers_role"
                    :items="role_list"
                    class="flex-grow-1 scroll-me"
                    :loading="loading"
                    >
                    
                    <template  v-slot:[`item.action`]="{ item }">
                        <div class="actions d-flex justify-content-between">
                            <v-btn icon @click="editItem(item)">
                                <v-icon>mdi-border-color</v-icon>
                            </v-btn>
                            <v-btn icon @click="deleteItem(item)">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </div>
                    </template>
                    </v-data-table>
            </div>
        </div>
      </v-card-text>
    </v-card>
    
    <template v-else>
      <v-skeleton-loader class="mx-auto" type="image"></v-skeleton-loader>
    </template>

    <!--editar rol-->
    <v-dialog v-model="dialog" max-width="500px">
        <v-card>
        <v-card-title>
            <span class="headline">Editar Rol</span>
        </v-card-title>
        <v-card-text>
            <v-container>
            <v-row>
                <v-col cols="12" sm="12" md="12" style="width: 100%; padding: 0px 24px;">
                <v-text-field v-model="editedItem.title" label="Nombre"></v-text-field>
                </v-col>
                <v-col cols="12" sm="12" md="12" style="width: 100%; padding: 0px 24px;">
                <v-text-field v-model="editedItem.name" label="Identificador" disabled></v-text-field>
                </v-col>
            </v-row>
            </v-container>
        </v-card-text>

        <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn class="btn-actions"  @click="close">Cancelar</v-btn>
            <v-btn color="primary" class="mr-2" @click="updateRol(); edit_loading=true;" :loading="edit_loading">Guardar cambios</v-btn>
        </v-card-actions>
        </v-card>
    </v-dialog>
    <!--eliminar rol-->
    <v-dialog v-model="deleteDialog" max-width="290">
        <v-card>
            <v-card-title class="headline">Eliminar Rol</v-card-title>
            <v-card-text>¿Está seguro de que desea eliminar este rol?</v-card-text>
            <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn @click="deleteDialog = false">Cancelar</v-btn>
            <v-btn color="error" @click="deleteRole(); delete_loading=true;" :loading="delete_loading">Eliminar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
  name: "list-rol-card",
  props: {
    headers_role: {
      type: Array,
      default: [],
    },
    options: {
      type: Object,
      default: {},
    },
    role_list: {
      type: Array,
      default: [],
    },
    loading: {
      type: Boolean,
      default: true,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    validate:{
        type:Function,
        default:()=>{}
    }
  },
  computed: {
    
  },
  components: {},
  data() {
    return {
        rules: [(v) => !!v || "Campo es obligatorio"],
      
        edit_loading:false,
        delete_loading:false,
        isLoadingModal:false,
        editedIndex: -1,
        editedItem: {
            name: '',
            description: '',
        },
        dialog: false,
        deleteDialog: false,
    };
  },
  created() {},
  methods: {
    set_prop(_prop, value) {
      this.$emit("update:" + _prop, value);
    },

    editItem (item) {
        this.editedIndex = this.role_list.indexOf(item)
        this.editedItem = Object.assign({}, item);
        this.dialog = true
    },

    deleteItem (item) {
        this.editedIndex = this.role_list.indexOf(item);
        this.editedItem = Object.assign({}, item);
        this.deleteDialog = true;
    },
    close () {
        this.dialog = false;
        this.delete_loading = false;
        this.edit_loading = false;
        this.$nextTick(() => {
            this.editedItem = Object.assign({}, this.defaultItem)
            this.editedIndex = -1
        })
    },

    async updateRol () {
        try{
            const permission = await API.roles.update( this.editedItem.id, this.editedItem );
            this.loading = false;
            this.validate();
            this.close();
            this.$swal({
                toast: true,
                position: 'top-end',
                icon:'success',
                title:'Rol actualizazdo correctamente',
                showConfirmButton:false,
                timerProgressBar:true,
                timer:2500
            });
        }
        catch(error){
            this.$swal({
                icon: 'error',
                title: 'Oops...',
                text: 'Error al actualizar.'
            });
            console.error(error);
        }
    }, 

    async deleteRole() {
        this.isLoading=true;
        try {
            const response = await API.roles.remove(this.editedItem.id);
            this.$swal({
                toast: true,
                position: 'top-end',
                icon:'success',
                title:'Rol eliminado correctamente',
                showConfirmButton:false,
                timerProgressBar:true,
                timer:2500
            });
            this.validate();
            this.deleteDialog = false;
            this.isLoading=false;
            this.isLoadingModal=false;
            this.delete_loading = false;
        } catch (error) {
            this.isLoading=false;
            this.delete_loading = false;
            this.isLoadingModal=false;
            this.deleteDialog = false;
            this.$swal({
                icon: 'error',
                title: 'Oops...',
                text: 'Error al eliminar.'
            });
            console.error(error);
        }
    },
  },
};
</script>

