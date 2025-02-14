<template>
    <div class="flex-grow-1">
        <div class="d-flex align-center py-3">
            <h2 style="color:  #AD91FD">Sucursales</h2>
            <v-spacer></v-spacer>
            <v-btn icon >
                <v-icon>mdi-refresh</v-icon>
            </v-btn>
        </div>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
                <v-col cols="4">
                    <v-text-field v-model="searchQuery" append-icon="mdi-magnify" class="flex-grow-1 mr-1" single-line hide-details placeholder="Buscar por nombre …" @keyup.enter="searchDataTable(searchQuery)"></v-text-field>
                </v-col>
                <v-col class="text-right">
                    <v-btn small color="primary" class="mr-2" @click="dialog=true;">Añadir Sucursal<v-icon>mdi-plus</v-icon></v-btn>            
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
                :footer-props="{ itemsPerPageOptions: [25,50,100,1000], }"
                :loading="loading" loading-text="Loading... Please wait">
                <template v-slot:[`item.actions`]="{ item }">
                    <v-btn small icon @click="editSucursal(item)">
                        <v-icon small> mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn small icon @click="deleteSucursal(item)">
                        <v-icon small> mdi-delete</v-icon>
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>
        <!-- Add/Edit Dialog -->
        <v-dialog v-model="dialog" max-width="1000px">
            <v-card>
                <v-card-title>
                    <span class="headline" v-show="!dialogEditar">Agregar Sucursal</span>
                    <span class="headline" v-show="dialogEditar">Editar Sucursal</span>
                </v-card-title>
                <v-card-text>
                    <v-form v-model="valid" ref="form">
                        <v-row>
                            <v-col cols="12" sm="6"  md="6">
                                <v-text-field 
                                    v-model="form.ruc_sucursal" 
                                    label="RUC Sucursal" 
                                    :rules="requiredRules">
                                </v-text-field>
                                <v-text-field 
                                    v-model="form.nombre_sucursal" 
                                    label="Nombre Sucursal" 
                                    :rules="requiredRules">
                                </v-text-field>
                                <v-text-field 
                                    v-model="form.departamento" 
                                    label="Departamento" 
                                    :rules="requiredRules">
                                </v-text-field>
                                <v-text-field 
                                    v-model="form.provincia" 
                                    label="Provincia" 
                                    :rules="requiredRules">
                                </v-text-field>
                                <v-text-field 
                                    v-model="form.distrito" 
                                    label="Distrito" 
                                    :rules="requiredRules">
                                </v-text-field>
                                <v-text-field 
                                    v-model="form.telefono" 
                                    label="Teléfono" 
                                    :rules="requiredRules">
                                </v-text-field>
                                <v-text-field 
                                    v-model="form.email" 
                                    label="Correo Electrónico" 
                                    :rules="requiredRules">
                                </v-text-field>
                                <v-text-field 
                                    v-model="form.facturador" 
                                    label="URL Facturación" 
                                    :rules="requiredRules">
                                </v-text-field>
                                <v-switch
                                    v-model="form.impresion_automatica"
                                    flat
                                    label="Impresion automática"
                                ></v-switch>
                            </v-col>
                            <v-col cols="12" sm="6"  md="6">
                                <v-text-field 
                                    v-model="form.razon_social_sucursal" 
                                    label="Razón Social Sucursal" 
                                    :rules="requiredRules">
                                    </v-text-field>
                                <v-text-field 
                                    v-model="form.cod_domicilio_fiscal" 
                                    label="Código Dom. Fiscal" 
                                    :rules="requiredRules">
                                    </v-text-field>
                                <v-text-field 
                                    v-model="form.direccion_fiscal" 
                                    label="Dirección Fiscal" 
                                    :rules="requiredRules">
                                    </v-text-field>
                                <v-text-field 
                                    v-model="form.direccion_comercial" 
                                    label="Dirección Comercial" 
                                    :rules="requiredRules" 
                                    ></v-text-field>
                                <v-text-field 
                                    v-model="form.direccion_web" 
                                    label="Dirección Web" 
                                    :rules="requiredRules">
                                    </v-text-field>
                                <v-text-field 
                                    v-model="form.nro_cuenta_bancario" 
                                    label="Nro. Cuenta Bancario" 
                                    :rules="requiredRules">
                                    </v-text-field>
                                <v-text-field 
                                    v-model="form.cci_bancario" 
                                    label="CCI Bancario" 
                                    :rules="requiredRules">
                                </v-text-field>
                                <v-text-field 
                                    v-model="form.token_facturador" 
                                    label="Token Facturación" 
                                    :rules="requiredRules">
                                </v-text-field>
                            </v-col>
                        </v-row>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="error" dark @click="dialog = false" >Cancelar</v-btn>
                            <v-btn type="submit" color="primary" @click="dialogEditar ? updateSucursal() : createSucursal()"> Guardar </v-btn>
                        </v-card-actions>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
        <!-- Fin Add/Edit Dialog -->
    </div>
</template>
<script>
export default {
    data: () => ({
        //--- Datatable ---
        page:1,
        itemsperpage: 25,
        options: {},
        loading:false,
        headers: [
            {text: 'Sucursal', value: 'nombre_sucursal', sortable: false},
            {text: 'Email', value: 'email', sortable: false},
            {text: 'Teléfono', value: 'telefono', sortable: false},
            {text: 'Departamento', value: 'departamento', sortable: false},
            {text: 'Provincia', value: 'provincia', sortable: false},
            {text: 'Acciones', value: 'actions', align: 'center', sortable: false, align:'right'}
            
        ],
        data: {
            data: [],
            total: 0,
        },
        searchQuery: '',
        searchData: {},
        valid: true,
        dialog: false,
        dialogEditar: false,
        dialogDelete: false,
        form: { 
            id_sucursal: '',
            nombre_sucursal: '',
            cod_domicilio_fiscal: '',
            direccion_fiscal: '',
            departamento: '',
            provincia: '',
            distrito:'',
            telefono:'',
            direccion_comercial:'',
            email:'',
            direccion_web:'',
            nro_cuenta_bancario:'',
            cci_bancario:'',
            impresion_automatica:1
        },
        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
        emailRules: [
            v => !!v || 'Campo obligatorio',
            v => /.+@.+\..+/.test(v) || 'Correo Electrónico debe ser válido',
        ],
    }),
    mounted() {
        this.getSucursales();
    },
    methods: {
        getSucursales(page, perpage) {
            this.$store.commit('LOADER',true);
            this.data.data= [];
            this.data.total= 0;
            this.searchData = {
                perpage: perpage,
                data: this.searchQuery,
            };
            axios.post('api/getSucursales?page=' + page, this.searchData).then(response => {
                this.data = response.data;
                this.page = response.data.current_page;
                this.$store.commit('LOADER',false);
            });
        },
        searchDataTable() {
            this.searchData = {
                data: this.searchQuery,
            };
            this.data.data= [];
            this.data.total= 0;
            this.data.from=0;
            this.data.to=0;
            this.getSucursales(1,this.itemsperpage);
        },

        createSucursal(){
            if(this.$refs.form.validate()){
                this.loading = true;
                axios.post('api/branch', this.form).then(()=>{
                    this.loading = false;
                    this.dialog = false;
                    this.getSucursales();
                    this.$refs.form.reset();
                    Toast.fire({
                        icon: 'success',
                        title: 'Sucursal creada correctamente!'
                    });
                }).catch(()=> {
                    Swal.fire("Error!", "Ocurrió un error.", "warning");
                });
            }
            else{
                return;
            }
        },
        editSucursal(item) {
            this.dialogEditar = true;
            this.dialog = true;
            this.form = Object.assign(item);
        },
        updateSucursal(){
            if(this.$refs.form.validate()){
                this.loading = true;
                axios.put('api/branch/'+this.form.id_sucursal,this.form).then(() => {
                    this.loading = false;
                    this.dialog = false;
                    this.getSucursales();
                    this.$refs.form.reset();
                    this.$swal({
                        toast: true,
                        position: 'top-end',
                        icon:'success',
                        title:'Usuario registrado correctamente',
                        showConfirmButton:false,
                        timerProgressBar:true,
                        timer:2500
                    });
                }).catch(()=> {
                    this.$swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.response.data.message
                    });
                });
            }
            else{
                return;
            }
        },
        deleteSucursal(item){
            axios.delete('api/branch/'+item.id_sucursal).then(()=>{
                this.$swal({
                    toast: true,
                    position: 'top-end',
                    icon:'success',
                    title:'Sucursal eliminada correctamente',
                    showConfirmButton:false,
                    timerProgressBar:true,
                    timer:2500
                });
                this.getSucursales();
            }).catch(()=> {
                this.$swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.response.data.message
                });
            });
        },
    },
    watch: {
        options(event) {
            this.itemsperpage = event.itemsPerPage;
            this.getSucursales(event.page, event.itemsPerPage);
        },
    },
}
</script>