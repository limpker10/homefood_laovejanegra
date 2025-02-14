<template>
    <div class="flex-grow-1">
        <div class="d-flex align-center py-3">
            <h2 style="color:  #AD91FD">Egresos {{$route.params.id == 'hotel' ? 'Hotel':'Restaurante'}}</h2>
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
                    <v-btn small color="primary" class="mr-2" @click="openDialog()">
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
                                    <v-text-field v-model="form.fecha_egreso" label="Fecha" disabled></v-text-field>
                                    <v-select :items="motivos_egreso" label="Motivo de Egreso" v-model="form.id_motivo_egreso"
                                              item-text="motivo"
                                              item-value="id_egreso_motivo"
                                              :rules="requiredRules"
                                              :readonly="editedIndex != -1"
                                    ></v-select>
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
                                                  :readonly="editedIndex != -1"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-col class="text-right">
                            <v-btn color="error" dark @click="closeDialog()">Cerrar</v-btn>
                            <v-btn  v-if="editedIndex == -1" color="primary" @click="save"> Guardar </v-btn>
                        </v-col>
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
            { text: 'ID', sortable: false, value: 'id_egreso', align:'center' },
            { text: 'Fecha', sortable: false, value: 'fecha_egreso', align:'center' },
            { text: 'Motivo', sortable: false, value: 'motivo_egreso.motivo', align:'center' },
            { text: 'Monto', sortable: false, value: 'monto' , align:'center'},
            { text: 'Acciones', sortable: false,value: 'actions', align:'center' }
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

        filter:{
            fecha_inicio: null,//new Date().toISOString().substr(0, 10),
            fecha_fin: null//new Date().toISOString().substr(0, 10)
        },
        menuFechInicio: false,
        menuFechFin: false,

        form: {
            id_tipo_egreso: '',
            id_motivo_egreso: '',
            id_metodo_gasto: '',
            rubro:'',
            fecha_egreso: new Date().toISOString().substr(0, 10),
            monto:'',
            detalle: '',
        },
        defaultForm: {
            id_tipo_egreso: '',
            id_motivo_egreso: '',
            id_metodo_gasto: '',
            rubro:'',
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
                rubro: this.$route.params.id
            };

            axios.post('/api/getEgresosAll?page=' + this.page, this.searchData).then(response => {
                this.data = response.data;
                this.page = response.data.current_page;

            }).finally(() => (this.loading = false));
        },
        getMotivosEgreso(){
            axios.get('/api/getListMotivosEgreso').then(response => {
                this.motivos_egreso = response.data;
            });
        },
        openDialog(){
            this.editedIndex = -1;
            this.form.fecha_egreso = new Date().toISOString().substr(0, 10);
            this.dialog=true;
        },
        closeDialog(){
            this.dialog = false;
            this.loading = false;
            this.$refs.form.reset();
            this.direcciones = [{id_direccion: "", direccion: ""}];
        },
        editItem (item) {
            this.editedIndex = this.data.data.indexOf(item)
            this.form = Object.assign({}, item)
            this.dialog = true;
        },
        validate(){
            let no = [null,''];
            if(no.includes(this.form.monto) || no.includes(this.form.id_motivo_egreso)){
                return false;
            }else{
                return true;
            }
        },
        async deleteItem(item){
            console.log(item);
            this.$swal({
                title: '¿Estas seguro de borrar este egreso?',
                text: "Esta accion no se puede revertir!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#AD91FD',
                cancelButtonColor: '#FF5252',
                confirmButtonText: 'Borrar',
                cancelButtonText: 'Cancelar',
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try{
                        const res = await axios.delete('/api/expenses/'+item.id_egreso);
                        console.log(res);
                         this.$swal({
                            toast: true,
                            position: 'top-end',
                            icon:'success',
                            title:'Eliminado correctamente',
                            showConfirmButton:false,
                            timerProgressBar:true,
                            timer:2500
                        });
                    }catch(e){
                        console.error(e);
                        this.$swal({
                            toast: true,
                            position: 'top-end',
                            icon:'error',
                            title:'Hubo algun error',
                            showConfirmButton:false,
                            timerProgressBar:true,
                            timer:2500
                        });
                    }
                }
            }).finally(()=> this.getlist());
        },
        async save () {
            this.loading = true;
            this.form.rubro = this.$route.params.id;
            console.log(this.form);
            if(!this.validate()){
                this.$swal({
                    toast: true,
                    position: 'top-end',
                    icon:'warning',
                    title:'Llene todos los campos obligatorios',
                    showConfirmButton:false,
                    timerProgressBar:true,
                    timer:2500
                });
            }else{
                try{
                    const response = await axios.post('/api/expenses', this.form);
                    console.log(response);
                    if(response.data.status == false){
                        this.$swal({
                            title: 'No existe ninguna caja abierta',
                            text: "¿Desea abrir un ahora?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Si, abrir!',
                            cancelButtonText: 'No, cancelar!',
                            reverseButtons: false,
                            confirmButtonColor: '#AD91FD',
                            cancelButtonColor: '#FF5252',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.$router.push("/branchSystem");
                            }
                        });
                    }else{
                        this.getlist();
                        this.$swal({
                            toast: true,
                            position: 'top-end',
                            icon:'success',
                            title:'Eliminado correctamente',
                            showConfirmButton:false,
                            timerProgressBar:true,
                            timer:2500
                        });
                    }
                    this.closeDialog();
                }catch(e){
                    console.error(e)
                    this.loading = false;
                    this.closeDialog();
                }
            }
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
        close
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
