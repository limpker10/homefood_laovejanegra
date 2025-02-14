<template>
    <div>
        <v-container>
            <div class="d-flex align-center py-1">
                <div>
                    <h2 style="color:  #AD91FD">Registar Salida</h2> 
                    <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
                </div>
            </div>
            <v-row dense>
                <v-col 
                cols="12"
                >
                    <v-card >
                        <v-list-item three-line>
          
                            <v-list-item-content>
                                <v-list-item-title class="headline mb-1">
                                    Detalle Habitación
                                </v-list-item-title>
                                <v-row
                                    no-gutters
                                    style="font-size: 14px;"
                                    >
                                    <v-col cols="4" class="pa-2" style="margin: auto;">
                                        <b>Nro. Habitacion:</b> {{room.nombre_habitacion}}
                                    </v-col>
                                    <!--<v-col cols="4" class="pa-2" style="margin: auto;">
                                        <b>Tipo de Habitación:</b> {{room.category.nombre_categoria}}
                                    </v-col>-->
                                    <v-col cols="4" class="pa-2">
                                        <b>Zona del Hotel:</b> {{room.zone.nombre_zona}}
                                    </v-col>
                                    <v-col cols="4" class="pa-2" style="margin: auto;">
                                        <b>Cliente:</b> {{customer.nombre}}
                                    </v-col>
                                    <v-col cols="8" class="pa-2">
                                        <b>Detalle:</b> {{room.detalle}}
                                    </v-col>
                                    <v-col cols="4" class="pa-2">
                                        <b>Fecha Ingreso:</b> {{booking.fecha_alojamiento}}
                                    </v-col>
                                </v-row> 
                            </v-list-item-content>

                            <v-list-item-avatar
                                tile
                                size="65"
                                style="    margin: auto;"
                            >
                                <v-img :src="'../assets/images/room_occuped_gray.png'"></v-img>
                            </v-list-item-avatar>
                        </v-list-item>
                    </v-card>
                </v-col>
                <v-col 
                cols="12"
                >
                    <v-card >
                        <div>
                            <div>
                                <v-card-title
                                class="headline mb-1"
                                >Alojamiento</v-card-title>
                                <v-card-text>
                                    <v-simple-table>
                                        <template v-slot:default>
                                            <thead>
                                                <tr>
                                                    <th class="text-left"> Costo Alojamiento </th>
                                                    <th class="text-left"> Descuento </th>
                                                    <th class="text-left"> Adelanto </th>
                                                    <th class="text-left"> Mora/Penalidad </th>
                                                    <th class="text-left"> Por Pagar </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <v-text-field
                                                            readonly
                                                            value="0.00"
                                                            hide-details="auto"
                                                            prefix="S/."
                                                            v-model="booking.costo_total"
                                                        ></v-text-field>
                                                    </td>
                                                    <td>
                                                        <v-text-field
                                                            readonly
                                                            value="0.00"
                                                            hide-details="auto"
                                                            prefix="S/."
                                                            v-model="booking.descuento"
                                                        ></v-text-field>
                                                    </td>
                                                    <td>
                                                        <v-text-field
                                                            readonly
                                                            value="0.00"
                                                            hide-details="auto"
                                                            prefix="S/."
                                                            v-model="booking.adelanto"
                                                        ></v-text-field>
                                                    </td>
                                                    <td>
                                                        <v-text-field
                                                            value="0.00"
                                                            hide-details="auto"
                                                            prefix="S/."
                                                            v-model="booking.mora"
                                                        ></v-text-field>
                                                    </td>
                                                    <td>
                                                        <v-text-field
                                                            readonly
                                                            :value="calculateTotalAlojamiento"
                                                            hide-details="auto"
                                                            prefix="S/."
                                                        ></v-text-field>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </template>
                                    </v-simple-table>
                                    <!--<v-row>
                                        <v-col
                                        cols="6"
                                        md="3"
                                        style="padding: 0px 15px;"
                                        >
                                            <v-text-field
                                                label="Costo Total"
                                                placeholder="0.00"
                                                prefix="S/."
                                                v-model="form.costo_total"

                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                        cols="6"
                                        md="3"
                                        style="padding: 0px 15px;"
                                        >
                                            <v-text-field
                                                label="Adelanto"
                                                placeholder="0.00"
                                                prefix="S/."
                                                v-model="form.adelanto"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                        cols="6"
                                        md="3"
                                        style="padding: 0px 15px;"
                                        >
                                            <v-text-field
                                                label="Descuento"
                                                placeholder="0.00"
                                                prefix="S/."
                                                v-model="form.descuento"
                                            ></v-text-field>
                                        </v-col>

                                        <v-col
                                        cols="6"
                                        md="3"
                                        style="padding: 0px 15px;"
                                        >
                                            <v-text-field
                                                label="Total Alojamiento"
                                                placeholder="0.00"
                                                prefix="S/."
                                                v-model="form.total"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>-->
                                </v-card-text>
                            </div>
                        </div>
                    </v-card>
                </v-col>
                <v-col 
                cols="12"
                >
                    <v-card >
                        <div>
                            <div>
                                <v-card-title
                                class="headline mb-1"
                                >Consumo</v-card-title>
                                <v-card-text>
                                <v-simple-table>
                                    <template v-slot:default>
                                        <thead>
                                            <tr>
                                                <th class="text-left"> Producto </th>
                                                <th class="text-left"> Cantidad </th>
                                                <th class="text-left"> Estado </th>
                                                <th class="text-left"> P.U (S/.) </th>
                                                <th class="text-left"> P.T (S/.) </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in booking.detail" :key="index">
                                                <td>{{item.nombre_producto_servicio}}</td>
                                                <td>{{item.cantidad}}</td>
                                                <td>
                                                    <v-chip small v-if="item.pago==0" color="red darken-2" text-color="white">Falta Pagar</v-chip>
                                                    <v-chip small v-if="item.pago==1" color="green darken-1" text-color="white">Pago</v-chip>
                                                </td>
                                                <td>S/. {{item.precio_unitario}}</td>
                                                <td>S/. {{item.precio_total}}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" style="text-align:right;"><b>Total: </b></td>
                                                <td colspan="1">S/. {{calculateTotaDetalle}}</td>
                                            </tr> 
                                        </tfoot>
                                    </template>
                                </v-simple-table>
                                </v-card-text>
                            </div>
                        </div>
                    </v-card>
                </v-col>
                <v-col 
                cols="12"
                style="padding: 0 10px;"
                >
                    <v-card tile>
                        <v-card
                        outlined
                        flat
                        tile
                        class="pa-2 d-flex justify-end mb-4"
                        >
                            <v-simple-table>
                                <template v-slot:default>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" style="text-align:right;"><b>Subtotal: </b></td>
                                            <td colspan="1">
                                                <v-text-field
                                                    readonly
                                                    hide-details="auto"
                                                    prefix="S/."
                                                    v-model="form.header.subtotal"
                                                ></v-text-field>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="text-align:right;"><b>IGV (18%): </b></td>
                                            <td colspan="1">
                                                <v-text-field
                                                    readonly
                                                    hide-details="auto"
                                                    prefix="S/."
                                                    v-model="form.header.igv"
                                                ></v-text-field>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="text-align:right;"><b>Total: </b></td>
                                            <td colspan="1">
                                                <v-text-field
                                                    readonly
                                                    hide-details="auto"
                                                    prefix="S/."
                                                    v-model="form.header.total"
                                                ></v-text-field>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </template>
                            </v-simple-table>
                        </v-card>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="primary"
                                @click="preLoad"
                            >
                                Registrar Salida
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>

            <v-dialog v-model="verComprobanteDialog" max-width="500px">
                <v-card>
                    <iframe :src="urlComprobante" frameborder="0" height="700px" width="100%">
                    </iframe>
                </v-card>
            </v-dialog>
            

            <v-dialog v-model="comprobanteDialog" max-width="550px" persistent>
                <v-card>
                    <v-card-title class="subtitle-1">Procesar Comprobante</v-card-title>
                    <v-card-text>
                        <v-row dense>
                            <v-col cols="6" >
                                <v-select
                                    :items="tipo_comprobante"
                                    label="Comprobante"
                                    placeholder="Selecciona un tipo de comprobante"
                                    item-text="tipo_comprobante"
                                    item-value="id_tipo_comprobante"
                                    v-model="form.header.id_tipo_comprobante"
                                    hide-details="auto"
                                    @change="getserie"
                                ></v-select>
                            </v-col>
                            <v-col cols="6" >
                                <v-select
                                    :items="serie"
                                    label="Serie"
                                    item-text="serie"
                                    item-value="id_serie"
                                    v-model="form.header.id_serie"
                                    hide-details="auto"
                                ></v-select>
                            </v-col>
                        </v-row>

                        <!--<v-text-field
                            label="Cliente"
                            hide-details="auto"
                            readonly
                            v-model="form.header.nombre_cliente"
                        ></v-text-field>-->

                        <v-row dense>
                            <v-col cols="4" >
                                <v-text-field
                                    label="Nro. Documento"
                                    hide-details="auto"
                                    v-model="form.header.nro_documento"
                                    append-icon="mdi-magnify"
                                    @click:append="toggleSearchCsutomer"
                                    :rules="[rules.required,rules.only_numbers]"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="8" >
                                <v-text-field
                                    label="Cliente"
                                    hide-details="auto"
                                    v-model="form.header.nombre_cliente"
                                    :rules="[rules.required]"
                                ></v-text-field>
                            </v-col>
                        </v-row>

                        <v-text-field
                            label="Dirección"
                            hide-details="auto"
                            v-model="form.header.direccion_cliente"
                        ></v-text-field>

                        <v-simple-table>
                            <template v-slot:default>
                            <thead>
                                <tr>
                                    <th class="text-left"> Producto </th>
                                    <th class="text-left"> Cantidad </th>
                                    <th class="text-left"> P.U </th>
                                    <th class="text-left"> P.T </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Alojamiento {{room.category.nombre_categoria}}</td>
                                    <td>{{booking.dias_alojamiento}}</td>
                                    <td>S/. {{room.costo}}</td>
                                    <td>S/. {{booking.costo_total}}</td>
                                </tr>
                                <tr v-for="(item,index) in  form.detail" :key="index">
                                    <template v-if="item.pago==0">
                                        <td>{{item.nombre_producto_servicio}}</td>
                                        <td>{{item.cantidad}}</td>
                                        <td>S/. {{item.precio_unitario}}</td>
                                        <td>S/. {{item.precio_total}}</td>
                                    </template>
                                </tr>
                                
                            </tbody>
                            <tfoot>
                                <tr class="py-0">
                                    <td colspan="3" class="py-0" style="text-align:right;"><b>Descuento: </b></td>
                                    <td colspan="1" class="py-0">- S/.{{booking.descuento}}</td>
                                </tr>
                                <tr class="py-0">
                                    <td colspan="3" class="py-0" style="text-align:right;"><b>Adelanto: </b></td>
                                    <td colspan="1" class="py-0">- S/.{{booking.adelanto}}</td>
                                </tr>
                                <tr class="py-0">
                                    <td colspan="3" class="py-0" style="text-align:right;"><b>Mora: </b></td>
                                    <td colspan="1" class="py-0">- S/.{{booking.mora}}</td>
                                </tr>
                                <tr class="py-0">
                                    <td colspan="3" style="text-align:right;"><b>Subtotal: </b></td>
                                    <td colspan="1" class="py-0"> S/. {{form.header.subtotal}}</td>
                                </tr>
                                <tr class="py-0">
                                    <td colspan="3" style="text-align:right;"><b>IGV (18%): </b></td>
                                    <td colspan="1" class="py-0"> S/. {{form.header.igv}}</td>
                                </tr>
                                <tr class="py-0">
                                    <td colspan="3" style="text-align:right;"><b>Total: </b></td>
                                    <td colspan="1" class="py-0"> S/. {{form.header.total}}</td>
                                </tr> 
                            </tfoot>
                            </template>
                        </v-simple-table>
                        <v-select
                            :items="metodospago"
                            label="Metodo de Pago"
                            placeholder="Selecciona un método de pago"
                            item-text="medio_pago"
                            item-value="id_medio_pago"
                            v-model="form.header.id_medio_pago"
                            hide-details="auto"
                        ></v-select>
                        <v-select
                            :items="monedas"
                            label="Moneda"
                            placeholder="Seleccione moneda"
                            item-text="nombre"
                            item-value="id_moneda"
                            v-model="form.header.id_moneda"
                            hide-details="auto"
                        ></v-select>
                        <v-text-field
                            v-if="form.header.id_medio_pago==3"
                            label="Nro. Operación"
                            hide-details="auto"
                            v-model="form.header.nro_operacion"
                        ></v-text-field>
                        <v-textarea
                            name="input-7-1"
                            rows="2"
                            label="Comentario"
                            v-model="form.header.comentario"
                            hide-details="auto"
                        ></v-textarea>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="error" dark @click="comprobanteDialog=false"
                        >Cancelar</v-btn
                        >
                        <v-btn color="primary" @click="saveComprobante"
                        >Guardar</v-btn
                        >
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-container>
    </div>
</template>
<script>
import print from 'print-js';
import moment from 'moment'
export default {
    components: { 
    },
    data() {
        return{
            breadcrumbs: [
                {
                    text: 'Recepcion',
                    disabled: false,
                    to: '/reception/'+localStorage.getItem('SYSTEM_ID_BRANCH')
                },
                {
                    text: 'Registar Salida',
                    disabled: true,
                }
            ],
            menu: false,
            dates: ['', ''],
            idRoom:'',
            idReservation:'',
            room:{ category:{},zone:{}},
            booking:{ cliente:{}},
            customer:{},
            dialog: false,
            comprobanteDialog:false,
            typeDialog: '',
            loading: false,
            form:{
                id_reservas: 0,
                id_cliente: 0,
                nombre_cliente: '',
                fecha_alojamiento: '',
                fecha_salida_estimado: '',
                dias_alojamiento: 0,
                habitaciones: [],
                costo_total: 0.00,
                adelanto:0.00,
                descuento:0.00,
                total:0.00
            },
            preload:{
                header:{},
                detail:[]
            },
            form:{
                header:{igv:0.00,total:0.00,subtotal:0.00},
                detail:[]
            },
            
            metodospago: [],
            monedas: [],
            tipo_comprobante: [],
            serie: [],

            verComprobanteDialog: false,
            urlComprobante:"",
            id_sucursal: localStorage.getItem('SYSTEM_ID_BRANCH'),

            rules: {
                required: (value) => !!value || 'Campo obligatorio',
                only_numbers: UTILS.rules.only_numbers,
            },
        }
    },
     watch:{
        customer: {
            handler() {
                console.log(this.customer);
            },
        },
        dates:{
            handler(){
                var a = moment(this.dates[1]);
                var b = moment(this.dates[0]);
                this.form.dias_alojamiento = a.diff(b, 'days');
                this.form.costo_total = this.form.dias_alojamiento * this.room.costo;
            }
        }
    },
    created(){
        this.$store.commit('LOADER',true);
        this.idRoom = this.$route.params.id;
        this.readRoom();

        this.getMetodoPago();
        this.getMoneda();
        this.getTiposComprobante();
    },
    computed:{
        calculateTotalAlojamiento(){
            return Number.parseFloat((Number(this.booking.costo_total) -(Number(this.booking.adelanto) + Number(this.booking.descuento)) + Number(this.booking.mora))).toFixed(2)
            //return (Number(this.booking.costo_total)) - (Number(this.booking.adelanto) + Number(this.booking.descuento)) + Number(this.booking.mora)
        },
        calculateTotaDetalle(){
            let total=0;
            if(this.booking.detail){
                for (let index = 0; index < this.booking.detail.length; index++) {
                    const element = this.booking.detail[index];
                    if(element.pago==0){
                        total = total + Number(element.precio_total);
                    }
                }
            }
            return Number.parseFloat(total).toFixed(2);   
        },
        dateRangeText () {
            return this.dates.join(' ~ ')
        },
        minimumDate() {
            return moment().format('YYYY-MM-DD')
        },
        estado(){
            return function (estado) {
                if(estado == 0) return 'Disponible'
                if(estado == 1) return 'Ocupado'
                if(estado == 2) return 'En Limpieza'
                if(estado == 3) return 'Inactivo'
            }
        },
        colorEstado(){
            return function (estado) {
                if(estado == 0) return 'green lighten-1'
                if(estado == 1) return 'red lighten-2'
                if(estado == 2) return 'amber darken-2'
                if(estado == 3) return 'grey lighten-1'
            }
        }
    },
    methods:{
        async readRoom(){
            try{
                const data = await API.rooms.read(this.idRoom);
                console.log(data,"data");
                this.room = data.data;
                this.idReservation = this.room.id_reservas;
                this.readReservation();
                this.$store.commit('LOADER',false);
            }catch(error){
                console.error(error);
                this.$store.commit('LOADER',false);
            }
        },

        async toggleSearchCsutomer(){
            try {
                this.$store.commit('LOADER',true);
                let tipodoc = 1;
                if(this.form.header.nro_documento.length==8){
                    tipodoc = 2;
                }
                else{
                    tipodoc = 1;
                }
                const rpta = await axios.post("/api/buscarDniRuc",{tipoDoc:tipodoc, numDoc:this.form.header.nro_documento});
                console.log(rpta)
                if(rpta.data.success === true){
                    if(tipodoc === 1){
                        this.form.header.nombre_cliente = rpta.data.nombre_o_razon_social;
                        this.form.header.direccion_cliente = rpta.data.direccion_completa;
                        this.form.header = JSON.parse(JSON.stringify(this.form.header))
                        this.$store.commit('LOADER',false);
                    }else if(tipodoc === 2){
                        this.form.header.nombre_cliente = rpta.data.result.Nombres + " " + rpta.data.result.Apellidos;
                        this.form.header = JSON.parse(JSON.stringify(this.form.header))
                        this.$store.commit('LOADER',false); 
                    }
                }else{
                    this.$store.commit('LOADER',false);
                    //this.setTextSnack("No encontrado","red");
                    this.$swal({ 
                        icon:'error',
                        title:'Opps!',
                        text:rpta.data.msg,
                        timerProgressBar:true,
                        timer:2500
                    });
                }
                console.log(rpta.data)
                this.$store.commit('LOADER',false);
            } catch (error) {
                this.$store.commit('LOADER',false);
            }
        },

        async readReservation(){
            try{
                const data = await API.bookings.read(this.idReservation);
                this.booking = data.data;
                this.customer = this.booking.cliente;
                this.form.detail = this.booking.detail;
                this.calcularTotal();
            }catch(error){
                console.error(error);
            }
        },
        openDilaog(type){
            this.typeDialog = type;
            this.dialog = true;
        },
        
        calcularCosto() {
            let costo = this.room.costo;
            this.costoTotal = parseFloat(parseFloat(costo) * this.parametros.dias_alojamiento).toFixed(2)
            this.parametros.costo_total = this.costoTotal
        },
        clearCustomer(){
            this.customer = {};
        },
        calcularTotal(){
            this.form.header
            var sumaTotal;
            let booking = this.booking.detail.filter((item)=> item.pago == 0)
            sumaTotal = booking.reduce(function (sum, product) {
                var total_fila = parseFloat(product.precio_total);
                if (!isNaN(total_fila)) {
                    return sum + total_fila;
                }
            }, 0);
            console.log(sumaTotal)
            console.log(this.calculateTotalAlojamiento)
            sumaTotal = Number(sumaTotal) + Number(this.calculateTotalAlojamiento);
            if(!isNaN(sumaTotal)){
                this.form.header.subtotal = parseFloat(sumaTotal/1.18).toFixed(2);
                this.form.header.igv = parseFloat(sumaTotal -  this.form.header.subtotal).toFixed(2);
                this.form.header.total = parseFloat(sumaTotal).toFixed(2);
            }else{
                this.form.header.igv = '';
                this.form.header.subtotal = '';
                this.form.header.totall = '';
            }
        },
        async save(){
            let data_form = {
                id_cliente: this.customer.id_cliente,
                fecha_alojamiento: this.dates[0],
                fecha_salida_estimado: this.dates[1],
                dias_alojamiento: this.form.dias_alojamiento,
                habitaciones: [{"id_habitacion":this.idRoom}],
                costo_total: this.form.costo_total,
                adelanto: this.form.adelanto,
                descuento: this.form.descuento,
                estado: 1
            }
            console.log(data_form)

            try{
                const data = await API.checkin.create(data_form);
                this.$swal({
                    toast: true,
                    position: 'top-end',
                    icon:'success',
                    title:'Ingreso registrado correctamente',
                    showConfirmButton:false,
                    timerProgressBar:true,
                    timer:2500
                });
                this.$router.push('/reception/'+this.id_sucursal)
            }catch(error){
                console.error(error);
            }
        },
        async preLoad(){
            try{
                const data = await API.checkout.preload(this.idReservation);
                this.form.header = data.data;
                this.calcularTotal();
                this.form.header.id_tipo_comprobante = this.tipo_comprobante[0].id_tipo_comprobante;
                this.form.header.id_medio_pago = this.metodospago[0].id_medio_pago;
                this.form.header.id_moneda = this.monedas[0].id_moneda;
                this.form.header.nro_operacion = '';
                this.form.header.comentario = '';
                this.getserie();
                this.comprobanteDialog = true;
            }catch(error){
                console.error(error);
            }
        },
        async saveComprobante(){
            this.$store.commit('LOADER',true);
            let detail = [];
            for (let index = 0; index < this.form.detail.length; index++) {
                const element = this.form.detail[index];
                if(element.pago==0){
                    detail.push({
                        id_producto: element.id_producto_servicio,
                        id_unidad_medida: element.id_unidad_medida,
                        nombre_producto: element.nombre_producto_servicio,
                        precio_unitario: element.precio_unitario,
                        cantidad: element.cantidad,
                        codigo_producto: element.codigo_producto,
                        precio_total: element.precio_total
                    });
                }
            }
            const booking={           
                id_producto: 0,
                id_unidad_medida: 2,
                nombre_producto: 'Noche Alojamiento Hab.'+this.room.nombre_habitacion,
                cantidad: this.booking.dias_alojamiento,
                precio_unitario: this.room.costo,
                precio_total: this.booking.costo_total,
                codigo_producto: 'ALJ00-'+this.room.id_habitacion
            }
            this.form.header.id_reserva = this.idReservation;
            this.form.header.informacion_adicional = "NOMBRE: "+ this.customer.nombre + '  ' +"FECHA DE INGRESO: "+ this.booking.fecha_alojamiento + '  ' +"FECHA DE SALIDA: " + this.booking.fecha_salida_estimado ;
            try{
                const data = await API.invoice.create({header: this.form.header, detail:detail, alojamiento: booking});
                //this.urlComprobante = data.data.pdf;
                await API.checkout.create(this.idReservation,{habitaciones:[{id_habitacion: this.idRoom}]});

                this.$store.commit('LOADER',false);         
                if(data.data.success){  
                    this.$swal({
                        toast: true,
                        position: 'top-end',
                        icon:'success',
                        title:'Se genero comprobante correctamente',
                        showConfirmButton:false,
                        timerProgressBar:true,
                        timer:2500
                    });
                    if(data.data.print == 1){
                        this.comprobanteDialog = false;
                        printJS({printable:data.data.pdf_sunat, type:'pdf', maxWidth:80});
                        this.$router.push('/reception/'+this.id_sucursal);
                        this.loading=false;
                        this.$store.commit('LOADER',false);
                    }
                    else{
                        window.open(data.data.pdf_sunat);
                        this.$router.push('/reception/'+this.id_sucursal);
                        this.loading=false;
                        this.$store.commit('LOADER',false);
                    }     
                    
                }
                else{
                    this.$store.commit('LOADER',false);
                    this.loading=false;
                    this.$swal({
                        toast: true,
                        position: 'top-end',
                        icon:'error',
                        title:'Error al generar comprobante',
                        showConfirmButton:false,
                        timerProgressBar:true,
                        timer:2500
                    });
                }
            }catch(error){
                this.loading=false;
                console.error(error);
                this.$store.commit('LOADER',false);
            }
            
        },
        generateComprobante(){
            //PRELOAD DATA
            this.comprobanteDialog = true;
            
        },


        //Comprobante

        async getMetodoPago(){
            try{
                const data = await API.services.metodoPago();
                this.metodospago = data.data;
            }catch(error){
                console.error(error);
            }
        },
        async getMoneda(){
            try{
                const data = await API.services.moneda();
                this.monedas = data.data;
            }catch(error){
                console.error(error);
            }
        },
        async getTiposComprobante(){
            try{
                const data = await API.services.tipoComprobante();
                this.tipo_comprobante = data.data;
            }catch(error){
                console.error(error);
            }
        },

        async getserie(){
            try{
                const data = await API.services.serie(this.form.header.id_tipo_comprobante);
                this.serie = data.data;
                this.form.header.id_serie = this.serie[0].id_serie;
            }catch(error){
                console.error(error);
            }
        },
    }
}
</script>


<style lang="scss" scoped>
.boxshadow {
     box-shadow: none;
}
.card-display {
     display: flex;
     flex-direction: column;
     place-items: center;
     place-content: center;
     margin-bottom: 12px !important;
}
.containerS {
     height: 270px;
     justify-content: space-between;
     display: flex;
     flex-direction: column;
}
.fit {
     width: fit-content;
}
</style>