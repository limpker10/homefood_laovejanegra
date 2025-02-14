<template>
    <div class="flex-grow-1">
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Egresos</h2>
        </div>

        <v-card class="mb-4" light style="padding: 15px">
            <v-row>
                <v-col cols="12" sm="6" md="4">
                    <v-menu
                        v-model="menuFechInicio"
                        :close-on-content-click="true"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto">
                        <template v-slot:activator="{ on, attrs }">
                        <v-text-field v-model="filter.fecha_inicio" label="Fecha Inicio" prepend-icon="mdi-calendar"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                        ></v-text-field>
                        </template>
                        <v-date-picker no-title v-model="filter.fecha_inicio"></v-date-picker>
                    </v-menu>
                </v-col>
                <v-col cols="12" sm="6" md="4">
                    <v-menu
                        v-model="menuFechFin"
                        :close-on-content-click="true"
                        :nudge-right="40"
                        transition="scale-transition"
                        offset-y
                        min-width="auto">
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field v-model="filter.fecha_fin" label="Fecha Fin" prepend-icon="mdi-calendar"
                                readonly
                                v-bind="attrs"
                                v-on="on"
                            ></v-text-field>
                        </template>
                        <v-date-picker no-title v-model="filter.fecha_fin"></v-date-picker>
                    </v-menu>
                </v-col>
                <v-col class="text-right">
                    <v-btn color="primary" class="mr-2" @click="getlist">
                        <v-icon>mdi-magnify</v-icon>Buscar
                    </v-btn>            
                </v-col>
            </v-row>
        </v-card>
        <br>
        <v-card class="mb-4" light style="padding: 15px">
            <v-row dense class="pa-2 align-center">
                <v-col class="text-right">
                    <v-btn small color="primary" class="mr-2" @click="dialog=true;">
                        Añadir Egreso <v-icon>mdi-plus</v-icon>
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
                :footer-props="{ itemsPerPageOptions: [25,50,100,1000], }"
                :loading="loading" loading-text="Cargando... Por favor, espere">
                <template slot="no-data">
                    Aún no se han agregado registros.
                </template>
                <template v-slot:[`item.fecha_egreso`]="{ item }">
                    <div>{{item.fecha_egreso | formatDate}}</div>
                </template>
                <template v-slot:[`item.actions`]="{ item }">
                    <v-btn small icon @click="editItem(item)">
                        <v-icon small> mdi-eye</v-icon>
                    </v-btn>
                    <v-btn small icon @click="deleteItem(item)">
                        <v-icon small> mdi-delete</v-icon>
                    </v-btn>
                    
                </template>
            </v-data-table>

            <v-dialog v-model="dialog" max-width="600px">
                <v-card>
                    <v-card-title>
                        <span class="headline">{{ formTitle }}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-form ref="form">
                            <v-row>
                                <v-col cols="6" sm="6"  md="6" >
                                    <v-menu
                                        v-model="menuFechEgreso"
                                        :close-on-content-click="true"
                                        :nudge-right="40"
                                        transition="scale-transition"
                                        offset-y
                                        min-width="auto">
                                        <template v-slot:activator="{ on, attrs }">
                                        <v-text-field v-model="form.fecha_egreso" label="Fecha Inicio" prepend-icon="mdi-calendar"
                                            readonly
                                            v-bind="attrs"
                                            v-on="on"
                                            :rules="requiredRules"
                                        ></v-text-field>
                                        </template>
                                        <v-date-picker no-title v-model="form.fecha_egreso"></v-date-picker>
                                    </v-menu>
                                    <v-select :items="motivos_egreso" label="Motivo de Egreso" v-model="form.id_motivo_egreso"
                                        item-text="motivo" 
                                        item-value="id_egreso_motivo"
                                        :rules="requiredRules"
                                        :readonly="editedIndex != -1"
                                    ></v-select>
                                    <v-checkbox
                                        v-model="form.caja_chica"
                                        label="¿Egreso salio de caja chica?"
                                    ></v-checkbox>
                                </v-col>
                                <v-col cols="6" sm="6"  md="6" >
                                    <v-text-field v-model="form.monto" label="Monto de Egreso" type="number" 
                                        class="inputPrice"
                                        autocomplete="off" 
                                        :rules="requiredRules" 
                                        :readonly="editedIndex != -1"
                                    ></v-text-field>
                                    <v-text-field v-model="form.detalle" label="Detalle" 
                                        autocomplete="off"
                                        :rules="requiredRules"
                                        :readonly="editedIndex != -1"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-col v-if="editedIndex == -1" class="text-right">
                            <v-btn color="error" dark @click="dialog = false">Cancelar</v-btn>
                            <v-btn color="primary" :disabled="isLoading" :loading="isLoading" @click="save"> Guardar </v-btn>
                        </v-col>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="dialogDelete" max-width="300px">
                <v-card>
                <v-card-title class="subtitle-1"
                    >¿Desea eliminar este Egreso?</v-card-title
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
</template>
 
<script>
export default {
    data: () => ({
        dialog: false,
        headers: [
            { text: 'ID', sortable: false, value: 'id_egreso' },
            { text: 'Fecha', sortable: false, value: 'fecha_egreso' },
            { text: 'Motivo', sortable: false, value: 'motivo_egreso.motivo' },
            { text: 'Monto', sortable: false, value: 'monto' },
            { text: 'Acciones', sortable: false,value: 'actions', align:'right' }
        ],
        editedIndex: -1,
        data: {
            data: [],
            total: 0,
        },
        options: {},
        itemsperpage: 25,
        page:1,
        loading:false,
        isLoading:false,

        filter:{
            fecha_inicio: null,//new Date().toISOString().substr(0, 10),
            fecha_fin: null//new Date().toISOString().substr(0, 10)
        },
        menuFechInicio: false,
        menuFechEgreso: false,
        menuFechFin: false,

        form: {
            id_tipo_egreso: '',
            id_motivo_egreso: '',
            id_metodo_gasto: '',
            caja_chica: 1,
            fecha_egreso: new Date().toISOString().substr(0, 10),
            monto:'',
            detalle: '',
        },
        tipos_egreso: [],
        motivos_egreso: [],
        metodos_gasto: [
            { id:1, name:'Caja Chica' },
            { id:2, name:'Cuenta Bancaria' },
        ],

        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
        dialogDelete: false
    }),

    mounted () {
        this.getlist();
        this.getMotivosEgreso();
    },

    methods: {
        getlist(page,perpage) {
            this.data.data= [];
            this.data.total= 0;
            this.loading = true;
            this.searchData = {
                perpage: perpage,
                data: this.filter,
            };

            axios.post('api/getEgresosAll?page=' + this.page, this.searchData).then(response => {
                this.data = response.data;
                this.page = response.data.current_page;
                
            }).finally(() => (this.loading = false));
        },
        getMotivosEgreso(){
            axios.get('/api/getListMotivosEgreso').then(response => {
                this.motivos_egreso = response.data;
            });
        },

        editItem (item) {
            this.editedIndex = this.data.data.indexOf(item)
            this.form = Object.assign({}, item)
            this.dialog = true;
        },
        save () {
            this.$store.commit('LOADER',true);
            this.loading = true;
            this.isLoading = true;
            if(this.$refs.form.validate()){
                axios.post('api/expenses', this.form).then(response => {
                    this.getlist();
                    this.dialog = false;
                    this.isLoading = false;
                }).catch((error)=>{
                    this.loading = true;
                    this.isLoading = false;
                }).finally(() => {
                     this.$store.commit('LOADER',false);
                    this.loading = false;
                    this.isLoading = false;
                });
            }
            else{
                 this.$store.commit('LOADER',false);
                this.loading = false;
                this.isLoading = false;
            }
        },

        deleteItem(item) {
            this.editedIndex = this.data.data.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialogDelete = true;
        },

        deleteItemConfirm() {
            this.loading = true;
            axios
            .delete("api/expenses/" + this.editedItem.id_egreso, this.editedItem)
            .then((response) => {
            this.loading = false;
            this.getlist();
            this.close();
            this.$swal({
                        toast: true,
                        position: 'top-end',
                        icon:'success',
                        title:'Egreso eliminado correctamente',
                        showConfirmButton:false,
                        timerProgressBar:true,
                        timer:2500
                    });

            });
            this.closeDelete();
        },
        closeDelete() {
            this.dialogDelete = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            });
        },
    },

    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'Nuevo Egreso' : 'Ver Egreso'
        },
    },
    watch: {
        options(event) {
            this.itemsperpage=event.itemsPerPage;
            this.getlist(event.page, event.itemsPerPage);
        },
        dialog(){
            if(!this.dialog){
                this.editedIndex = -1;
                this.$refs.form.reset();
                this.direcciones = [{id_direccion: "", direccion: ""}];
            }
        },
    },
}
</script>
<style lang="scss" scoped>
    .btn-actions{
        background-color: #fff !important;
        color: #121212;
    }
   *{
        text-transform: none !important;
    }
</style>
