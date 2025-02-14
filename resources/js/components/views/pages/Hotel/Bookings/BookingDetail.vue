<template>
    <div>
        <v-container>
            <div class="d-flex align-center py-1">
                <div>
                    <h2 style="color:  #AD91FD">Detalle de Alojamiento/Reserva</h2> 
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
                                    <v-col cols="4" class="pa-2" style="margin: auto;">
                                        <b>Tipo de Habitación:</b> {{room.category.nombre_categoria}}
                                    </v-col>
                                    <v-col cols="4" class="pa-2" style="margin: auto;">
                                        <b>Cliente:</b> {{customer.nombre}}
                                    </v-col>
                                    <v-col cols="4" class="pa-2">
                                        <b>Detalle:</b> {{room.detalle}}
                                    </v-col>
                                    <v-col cols="4" class="pa-2">
                                        <b>Zona del Hotel:</b> {{room.zone.nombre_zona}}
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
                                                            readonly
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
                                                <th class="text-left"> P.U </th>
                                                <th class="text-left"> P.T </th>
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
                                                <td>{{item.precio_unitario}}</td>
                                                <td>{{item.precio_total}}</td>
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
                    </v-card>
                </v-col>
            </v-row>

        </v-container>
    </div>
</template>
<script>
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
                    to: '/reception/'+localStorage.getItem('SYSTEM_ID_BRANCH'),
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
            urlComprobante:""
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
        this.idReservation = this.$route.params.id;
        //this.readRoom();
        this.readReservation();
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
                this.idRoom = this.booking.habitaciones[0].habitaciones.id_habitacion;
                const data = await API.rooms.read(this.idRoom);
                this.room = data.data;
                //this.idReservation = this.room.id_reservas;
                //this.readReservation();
                this.$store.commit('LOADER',false);
            }catch(error){
                console.error(error);
                this.$store.commit('LOADER',false);
            }
        },
        async readReservation(){
            try{
                const data = await API.bookings.read(this.idReservation);
                this.booking = data.data;
                this.customer = this.booking.cliente;
                this.form.detail = this.booking.detail;
                this.readRoom();
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
                        precio_total: element.precio_total
                    });
                }
            }
            const booking={
                id_producto: 0,
                id_unidad_medida: 2,
                nombre_producto: 'Alojamiento '+this.room.category.nombre_categoria,
                cantidad: this.booking.dias_alojamiento,
                precio_unitario: this.room.costo,
                precio_total: this.booking.costo_total
            }
            this.form.header.id_reserva = this.idReservation;
            this.form.header.id_reserva = this.idReservation; 
            try{
                const data = await API.invoice.create({header: this.form.header, detail:detail, alojamiento: booking});
                //this.urlComprobante = data.data.pdf;
                await API.checkout.create(this.idReservation,{habitaciones:[{id_habitacion: this.idRoom}]});

                this.$store.commit('LOADER',false);                window.open(data.data.pdf);
                this.$router.push('/reception/'+localStorage.getItem('SYSTEM_ID_BRANCH'));

            }catch(error){
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