<template>
    <div class="flex-grow-1">
        <div class="d-flex align-center py-3">
            <h2 style="color: #37474F">Caja</h2>
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
                            <v-text-field v-model="formatoFechaInicio" label="Fecha de Inicio" prepend-icon="mdi-calendar" readonly v-bind="attrs" v-on="on"></v-text-field>
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
                            <v-text-field v-model="formatoFechaFin" label="Fecha Fin" prepend-icon="mdi-calendar" readonly v-bind="attrs" v-on="on"></v-text-field>
                        </template>
                        <v-date-picker no-title v-model="filter.fecha_fin"></v-date-picker>
                    </v-menu>
                </v-col>
                <v-col class="text-right">
                    <v-btn color="primary" class="mr-2" @click="getlist">
                    <v-icon>mdi-magnify</v-icon>Buscar</v-btn>            
                </v-col>
            </v-row>
        </v-card>
        <v-card class="mb-4" light style="padding: 15px">
            <v-data-table
                :headers="headers"
                :items="data.data"
                class="elevation-1"
                :server-items-length="data.total"
                :page="page"
                :options.sync="options"
                :items-per-page="itemsperpage"
                :footer-props="{itemsPerPageOptions: [25,50,100,1000],}"
                :loading="loading" loading-text="Cargando... Por favor, espere">
                <template slot="no-data">
                    Aún no se han agregado registros.
                </template>
                <template v-slot:item.fecha_cierre="{ item }">
                    <div v-if="item.fecha_cierre==null">
                        Sin Cierre  {{item.fecha_cierre==null ? '' : item.fecha_cierre | formatTime}}
                    </div>
                    <div v-else>
                        {{ item.fecha_cierre | formatDate}}  {{item.fecha_cierre==null ? '' : item.fecha_cierre | formatTime}}
                    </div>
                </template>
                <template v-slot:item.fecha_apertura="{ item }">
                    <div>
                        {{item.fecha_apertura | formatDate}} {{item.fecha_apertura | formatTime}}
                    </div>
                </template>
                <template v-slot:item.usuario="{ item }">
                    <div>
                        {{item.usuario.name}}
                    </div>
                </template>
                <template v-slot:item.monto_cierre="{ item }">
                    <div v-if="item.monto_cierre==null"> Sin Cierre  </div>
                    <div v-else> {{item.monto_cierre}} </div>
                </template>
                <template v-slot:[`item.actions`]="{ item }">
                    <v-btn v-if="item.fecha_cierre == null" small icon @click="getCash(item.id_sucursal)">
                        <v-icon small> mdi-lock-outline</v-icon>
                    </v-btn>
                    <v-btn small icon @click="verDetalleCaja(item)">
                        <v-icon small> mdi-file-eye-outline</v-icon>
                    </v-btn>
                    <v-btn small icon @click="verpdf(item.id_caja)">
                        <v-icon small>mdi-file-pdf</v-icon>
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>

        <!-- Dialog Abrir Caja -->
        <v-dialog v-model="dialog" max-width="400px">
            <v-card>
                <v-card-title>
                    <span class="headline">Abrir Caja</span>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>
                        <v-col cols="12" sm="12"  md="12" >
                            <v-text-field v-model="editedItem.fecha" label="Fecha" :rules="requiredRules" readonly></v-text-field>
                            <v-text-field v-model="editedItem.hora" readonly label="Hora" :rules="requiredRules"></v-text-field>
                            <v-text-field v-model="editedItem.monto_apertura" label="Monto de Apertura" placeholder="0.00" type="number" class="inputPrice" autocomplete="off" :rules="requiredRules"></v-text-field>
                        </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>

                <v-card-actions>
                    <v-col class="text-right">
                        <v-btn color="error" dark @click="close" >Cancelar</v-btn>
                        <v-btn color="primary" @click="save"> Guardar </v-btn>
                    </v-col>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!-- Fin Dialog -->

        <!-- Dialog Cerrar Caja -->
        <v-dialog v-model="dialogCerrarCaja" max-width="450px">
            <v-card>
                <v-card-title>
                    <span class="headline">Cerrar Caja</span>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12" sm="12" md="6" >
                                <v-text-field v-model="closeItem.fecha" label="Fecha" readonly></v-text-field>
                                <v-text-field v-model="opencaja.monto_apertura" label="Monto de Apertura" placeholder="0.00" type="number" class="inputPrice" autocomplete="off"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="12" md="6" >
                                <v-text-field v-model="closeItem.hora" readonly label="Hora"></v-text-field>
                                <v-text-field v-model="monto_final" readonly label="Monto de Cierre" placeholder="0.00" type="number" class="inputPrice" autocomplete="off"></v-text-field>
                            </v-col>

                            <v-col cols="12" sm="12" md="6"  v-for="mt in montos" :key="mt.id_medio_pago">
                                <v-text-field v-model="mt.monto" :label="mt.medio_pago" readonly></v-text-field>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-col class="text-right">
                        <v-btn color="error" dark @click="closeDelete">Cancelar</v-btn>
                        <v-btn color="primary" @click="cerrarCajaConfirm">Aceptar</v-btn>
                    </v-col>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!-- Fin Dialog -->

        <!-- Dialog Ver Detalle Caja -->
        <v-dialog v-model="dialogVerCaja" max-width="450px">
            <v-card>
                <v-card-title>
                    <span class="headline">Ver Detalle Caja</span>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12" sm="12" md="6" >
                                <v-text-field v-model="ver_caja.fecha_apertura" label="Fecha" readonly></v-text-field>
                                <v-text-field v-model="ver_caja.monto_apertura" label="Monto de Apertura" placeholder="0.00" type="number" class="inputPrice" autocomplete="off"></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="12" md="6" >
                                <v-text-field v-model="ver_caja.fecha_cierre" readonly label="Hora"></v-text-field>
                                <v-text-field v-model="ver_caja.monto_cierre" readonly label="Monto de Cierre" placeholder="0.00" type="number" class="inputPrice" autocomplete="off"></v-text-field>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-dialog>
        <!-- Fin Dialog -->

        <v-dialog v-model="verCajaDialog" max-width="500px">
          <v-card>
              <iframe :src="urlComprobante" frameborder="0" height="700px" width="100%">
              </iframe>
          </v-card>
        </v-dialog>

        <v-dialog v-model="cerrarCajaDialog" max-width="500px" persistent>
            <v-card>
                <v-card-title class="subtitle-1">Cierre de Caja</v-card-title>
                <v-card-text>
                    <v-row dense>
                        <v-col cols="4" class="text-center">
                        -- Abierto por
                        <h4>{{caja.usuario.name}}</h4>
                        </v-col>
                        <v-col cols="4" class="text-center">
                        -- Caja Chica
                        <h4>{{caja.monto_apertura}}</h4>
                        </v-col>
                        <v-col cols="4" class="text-center">
                        -- Egresos de Caja
                        <h4>{{caja.egresos}}</h4>
                        </v-col>
                    </v-row>
                    <br>
                    <v-divider></v-divider>
                    <br>
                    <h3>Resumen de Pago</h3>
                    <v-simple-table>
                        <template v-slot:default>
                        <thead>
                            <tr>
                                <th class="text-left"> Método de pago </th>
                                <th class="text-left"> Ingresos </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in  caja.detalle" :key="index">
                                <td>{{item.medio_pago}}</td>
                                <td>{{item.monto}}</td>
                            </tr>
                        </tbody>
                        </template>
                    </v-simple-table>
                    <!--<v-textarea
                        name="input-7-1"
                        rows="2"
                        label="Nota"
                        hide-details="auto"
                    ></v-textarea>-->
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="error" dark @click="cerrarCajaDialog = false"
                    >Cancelar</v-btn
                    >
                    <v-btn color="primary" @click="closeCash"
                    >Cerrar Caja</v-btn
                    >
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>

    </div>
</template>
 
<script>
export default {
    data: () => ({
        filter: {
            //new Date().toISOString().substr(0, 10)
            fecha_inicio: null,
            fecha_fin: null,
        },
        menuFechInicio: false,
        menuFechFin: false,

        dialog: false,
        dialogCerrarCaja: false,
        dialogVerCaja: false,

        headers: [
            { text: 'ID', sortable: false, value: 'id_caja' },
            { text: 'Apertura', sortable: false, value: 'fecha_apertura' },
            { text: 'Cierre', sortable: false, value: 'fecha_cierre' },
            { text: 'Monto Apertura', sortable: false, value: 'monto_apertura' },
            { text: 'Monto Cierre', sortable: false, value: 'monto_cierre' },
            { text: 'Usuario', sortable: false, value: 'usuario' },
            { text: 'Acciones', value: 'actions', align: 'center', sortable: false, align:'right' }
        ],
        data: {
            data: [],
            total: 0,
        },
        options: {},
        searchData: {},
        itemsperpage: 25,
        page:1,
        loading:false,
        editedIndex: -1,

        defaultItem: {
            fecha: new Date().toISOString().substr(0, 10),
            hora: new Date().toISOString().substr(11, 5),
            monto_apertura: '',
        },
        editedItem: {
            fecha: new Date().toISOString().substr(0, 10),
            hora: new Date().toISOString().substr(11, 5),
            monto_apertura: '',
        },
        closeItem: {
            fecha: new Date().toISOString().substr(0, 10),
            hora: new Date().toISOString().substr(11, 5),
            monto_apertura: '',
        },
        ver_caja:{
            fecha_apertura: '',
            monto_apertura: '',
            fecha_cierre: '',
            monto_cierre: 0,
        },
        
        datos:{},
        montos: [],
        monto_final: 0,

        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
        
        opencaja: {id_caja:0},
        verCajaDialog:false,
        urlComprobante:'',
        
        cerrarCajaDialog: false,
        caja: { usuario:{} },
    }),

    watch: {
        options(event) {
            this.itemsperpage=event.itemsPerPage;
            this.getlist(event.page, event.itemsPerPage);
        },
    },

    mounted () {
        //this.consultarCaja();
        //this.getMontosDia();
    },

    methods: {
        getlist(page = 1,perpage = 25) {
            this.$store.commit('LOADER',true);
            this.data.data= [];
            this.data.total= 0;
            this.loading = true;
            this.searchData = {
                perpage: perpage,
                data: this.filter,
            };
            this.page = page;
            axios.post('api/getCajasAll?page=' + this.page, this.searchData).then(response => {
                this.data = response.data;
                this.page = page;//response.data.current_page;
                this.loading = false;
                this.$store.commit('LOADER',false);
            });
        },
        
        //--- Caja Abierta pendiente de Cierre ---
        consultarCaja(){
            axios.get('/api/cashAbierta').then(response => {
                if(response.data.length>0){
                    this.opencaja=response.data[0];
                }
                
            }).finally(() => (this.cargando = false));
        },
        /*getMontosDia(){
            axios.get('api/getMontosDelDia').then(response => {
                this.montos = response.data;
                response.data.forEach((val)  => {
                    this.monto_final+=parseFloat(val.monto);
                });
            }).finally(() => (this.cargando = false));
        },*/
        cerrarCaja (item) {
            this.editedIndex = this.data.data.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialogCerrarCaja = true
        },
        cerrarCajaConfirm () {
            this.loading = true;
            this.datos.caja=this.opencaja;
            this.datos.caja_detalle=this.montos;
            axios.put('/api/cash/'+this.opencaja.id_caja,this.datos).then(response => {
                this.$router.go();
            }).finally(() => (this.loading = false));

            this.closeDelete();
        },
        closeDelete () {
            this.dialogCerrarCaja = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },
        updateCaja(){
            this.datos.caja=this.opencaja;
            this.datos.caja_detalle=this.montos;
            axios.put('api/cash/'+this.opencaja.id_caja,this.datos).then(() => {
                // success
                this.opencaja= {id_caja:0};
                this.consultarCaja();
                this.getCajasLista();
                $('#cerrarCajaModal').modal('hide');
                Toast.fire({
                    icon: 'success',
                    title: 'Caja cerrada correctamente!'
                });
            });
        },
        //--- Fin ---

        //--- Ver Detalle Caja ---
        verDetalleCaja(item){
            this.dialogVerCaja = true;
            this.ver_caja = Object.assign({}, item)
        },
        close () {
            this.dialog = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },
        save () {
            this.loading = true;
            axios.post('/api/cash', this.editedItem).then(response => {
                this.$router.go();
            }).finally(() => (this.loading = false));
        },
        //--- Fin ---

        //--- Date Formatting ---
        formatDate (date) {
            if (!date) return null

            const [year, month, day] = date.split('-')
            return `${day}-${month}-${year}`
        },
        //--- Fin Date Formatting ---

        verpdf(item){
            this.$store.commit('LOADER',true);
            this.urlComprobante =  '/generarTicketCajaPDF/'+item
            this.verCajaDialog = true;
            this.$store.commit('LOADER',false);
            //window.open('/generarCajaPDF/'+item.id_caja);
        },

        
        async getCash(id){
            try{
                const detail = await API.cash.detail(id);
                this.caja = detail.data;
                this.cerrarCajaDialog = true;
                console.log(detail);
            }catch(error){
                console.error(error.response.status);
                this.dialogDelete = false;
            }
        },

        async closeCash(){
            try{
                const detail = await API.cash.update(this.caja.id_caja, { detalle: this.caja.detalle });
                this.caja = detail.data;
                this.cerrarCajaDialog = false;
                this.$router.push('/branchSystem');
            }catch(error){
                console.error(error.response.status);
                this.dialogDelete = false;
            }
        },
    },
    computed: {
        //--- Date Formatting ---
        formatoFechaInicio () {
            return this.formatDate(this.filter.fecha_inicio);
        },
        //--- Date Formatting ---
        formatoFechaFin () {
            return this.formatDate(this.filter.fecha_fin);
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
        //font-size: 14px !important;
        //font-family:'Quicksand', sans-serif  !important;
    }
</style>
