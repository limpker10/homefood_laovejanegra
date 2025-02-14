<template>
    <div>
        <v-container>
            <div class="d-flex align-center py-1">
                <div>
                    <h2 style="color:  #AD91FD">Registrar Ingreso de Reservacion</h2> 
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
                                    <v-col cols="6" class="pa-2" style="margin: auto;">
                                        <b>Nro. Habitacion:</b> {{room.nombre_habitacion}}
                                    </v-col>
                                    <!--<v-col cols="4" class="pa-2" style="margin: auto;">
                                        <b>Tipo de Habitación:</b> {{room.category.nombre_categoria}}
                                    </v-col>-->
                                    <v-col cols="6" class="pa-2" style="margin: auto;">
                                        <b>Estado: </b>
                                        <v-chip
                                        :color="colorEstado(room.estado)"
                                        text-color="white"
                                        small
                                        >
                                        {{estado(room.estado)}}
                                        </v-chip>
                                    </v-col>
                                    <v-col cols="6" class="pa-2">
                                        <b>Detalle:</b> {{room.detalle}}
                                    </v-col>
                                    <v-col cols="6" class="pa-2">
                                        <b>Zona del Hotel:</b> {{room.zone.nombre_zona}}
                                    </v-col>
                                    <!--<v-col cols="4" class="pa-2"><b>Precio:</b> {{room.costo}}
                                    </v-col>-->
                                </v-row> 
                            </v-list-item-content>

                            <v-list-item-avatar
                                tile
                                size="80"
                                style="    margin: auto;"
                            >
                                <v-img :src="'../assets/images/room_occuped_gray.png'"></v-img>
                            </v-list-item-avatar>
                        </v-list-item>
                    </v-card>
                </v-col>
                <v-col 
                cols="6"
                >
                    <v-card>
                        <div>
                            <v-card-title
                            class="headline mb-1"
                            >Cliente 
                            <v-spacer></v-spacer>

                                <v-btn
                                icon
                                @click="clearCustomer"
                                v-if="customer.id_cliente"
                                >
                                <v-icon>mdi-delete</v-icon>
                                </v-btn></v-card-title>
                            <v-card class="mb-3" elevation="0" style="height: 30vh;">
                                <v-card-text v-if="customer.id_cliente" style="color: #000; padding:0">
                                    <v-list-item three-line>
                                        <v-list-item-content>
                                            <p class="pa-2"><b>Nombre:</b> {{customer.nombre}}</p>
                                            <p class="pa-2"><b>Documento:</b> {{customer.tipo_doc.tipo_documento}}{{customer.nro_doc}}</p>
                                            <p class="pa-2"><b>Dirección:</b> {{customer.direccion}}</p>
                                        </v-list-item-content>
                                        <v-list-item-avatar
                                            tile
                                            size="80"
                                            style="    margin: auto;"
                                        >
                                            <v-img :src="'../assets/images/user.png'"></v-img>
                                        </v-list-item-avatar>
                                    </v-list-item>
                                </v-card-text>
                                <v-container class="d-flex" v-else>
                                    <v-card
                                        class="ma-auto card-display"
                                        height="100px"
                                        width="100px"
                                        rounded
                                        outlined
                                        hover
                                        @click="openDilaog('search')"
                                    >
                                        <v-icon size="40">mdi-magnify</v-icon>
                                        Buscar
                                    </v-card>
                                    <v-card
                                        class="ma-auto card-display"
                                        height="100px"
                                        width="100px"
                                        rounded
                                        outlined
                                        hover
                                        @click="openDilaog('create')"
                                    >
                                        <v-icon size="40">mdi-plus</v-icon>
                                        Crear
                                    </v-card>
                                </v-container>
                            </v-card>
                        </div>
                    </v-card>
                </v-col>
                <v-col 
                cols="6"
                >
                    <v-card >
                        <div class="d-flex flex-no-wrap justify-space-between">
                            <div>
                                <v-card-title
                                class="headline mb-1"
                                >Registrar</v-card-title>
                                <v-card-text>
                                    <v-row>
                                        <v-col
                                        cols="6"
                                        style="padding: 0px 15px;"
                                        >
                                            <v-select
                                                :items="categorias"
                                                label="Tipo de Habitación"
                                                item-value="id_categoria"
                                                :item-text="getItemText"
                                                v-model="form.categoria"
                                                required
                                                return-object
                                            />
                                        </v-col>
                                        <v-col
                                        cols="6"
                                        style="padding: 0px 15px;"
                                        >
                                            <v-text-field
                                                label="Costo Habitación"
                                                placeholder="0.00"
                                                prefix="S/."
                                                v-model="form.categoria.precio"
                                                v-on:keyup="changeprice"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                        cols="12"
                                        md="6"
                                        style="padding: 0px 15px;"
                                        >
                                            <v-menu
                                                ref="menu"
                                                v-model="menu"
                                                :close-on-content-click="false"
                                                :return-value.sync="dates"
                                                transition="scale-transition"
                                                offset-y
                                                min-width="auto"
                                                >
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-text-field
                                                    v-model="dateRangeText"
                                                    label="Fechas de Alojamiento"
                                                    prepend-icon="mdi-calendar"
                                                    readonly
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    v-model="dates"
                                                    no-title
                                                    range
                                                    scrollable
                                                    :min="minimumDate"
                                                >
                                                    <v-spacer></v-spacer>
                                                    <v-btn
                                                    text
                                                    color="primary"
                                                    @click="menu = false"
                                                    >
                                                    Cancelar
                                                    </v-btn>
                                                    <v-btn
                                                    text
                                                    color="primary"
                                                    @click="$refs.menu.save(dates)"
                                                    >
                                                    OK
                                                    </v-btn>
                                                </v-date-picker>
                                                </v-menu>
                                        </v-col>
                                        <v-col
                                        cols="12"
                                        md="6"
                                        style="padding: 0px 15px;"
                                        >
                                            <v-text-field
                                                label="Costo Total"
                                                placeholder="0.00"
                                                prefix="S/."
                                                v-model="form.costo_total"
                                                readonly
                                            ></v-text-field>
                                        </v-col>
                                        <v-col
                                        cols="12"
                                        md="6"
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
                                        cols="12"
                                        md="6"
                                        style="padding: 0px 15px;"
                                        >
                                            <v-text-field
                                                label="Descuento"
                                                placeholder="0.00"
                                                prefix="S/."
                                                v-model="form.descuento"
                                            ></v-text-field>
                                        </v-col>
                                    </v-row>
                                </v-card-text>
                            </div>
                        </div>
                    </v-card>
                </v-col>
                <v-col 
                cols="12"
                >
                    <v-card class="pa-3">
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="primary"
                                @click="save"
                            >
                                Registrar Ingreso
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
        <data-dialog
            :customer.sync="customer"
            :abierto.sync="dialog"
            :dialog.sync="typeDialog"
            :cargando.sync="loading"
        >
        </data-dialog>
    </div>
</template>
<script>
import DataDialog from '../Reception/components/CheckInDialog'

import moment from 'moment'
export default {
    components: { 
        DataDialog
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
                    text: 'Registrar Ingreso',
                    disabled: true,
                }
            ],
            menu: false,
            
            dates: [new Date().toISOString().substr(0, 10),new Date().toISOString().substr(0, 10)],
            idRoom:'',
            idBooking:'',
            room:{ category:{},zone:{}},
            customer:{},
            dialog: false,
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
                categoria:{
                    precio: 0.00
                }
            },
            id_sucursal: localStorage.getItem('SYSTEM_ID_BRANCH'),
            categorias:[]
        }
    },
     watch:{
        customer: {
            handler() {
                console.log(this.customer);
            },
        },
        /*
        dates:{
            handler(){
                var a = moment(this.dates[1]);
                var b = moment(this.dates[0]);
                if((a.diff(b, 'days')==0)){
                    this.form.dias_alojamiento = 1;
                }
                else{
                    this.form.dias_alojamiento = a.diff(b, 'days');
                }
                this.form.costo_total = this.form.dias_alojamiento * this.room.costo;
            }
        }*/
    },
    created(){
        this.$store.commit('LOADER',true);
        this.idBooking = this.$route.params.id;
        this.readReservation();
        this.getCategorias();
    },
    computed:{
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
                if(estado == 3) return 'Reservado'
            }
        },
        colorEstado(){
            return function (estado) {
                if(estado == 0) return 'green darken-1'
                if(estado == 1) return 'red darken-2'
                if(estado == 2) return 'amber darken-2'
                if(estado == 3) return 'orange darken-1'
            }
        }
    },
    methods:{
        getItemText(item) {
            return `${item.nombre_categoria} - S/.${item.precio}`;
        },
        changeprice(){
            var a = moment(this.dates[1]);
            var b = moment(this.dates[0]);
            this.form.dias_alojamiento = a.diff(b, 'days');
            this.form.costo_total = this.form.dias_alojamiento * this.form.categoria.precio; //this.room.costo;
            console.log(this.form.costo_total)
        },
        async readReservation(){
            try{
                const data = await API.bookings.read(this.idBooking);
                this.booking = data.data;
                this.customer = this.booking.cliente;
                this.room = this.booking.habitaciones[0].habitaciones;
                this.idRoom = this.room.id_habitacion;
                this.form.adelanto = data.data.adelanto;
                this.form.descuento = data.data.descuento;
                this.dates = [this.booking.fecha_alojamiento, this.booking.fecha_salida_estimado];
                this.form.dias_alojamiento = data.data.dias_alojamiento;
                this.form.categoria = this.booking.habitaciones[0].categoria;
                this.form.categoria.precio =  data.data.habitaciones[0].precio;
                this.form.costo_total = data.data.dias_alojamiento * this.form.categoria.precio;
                console.log(data.data);
                this.$store.commit('LOADER',false);
            }catch(error){
                this.$store.commit('LOADER',false);
                console.error(error);
            }
        },
        async getCategorias(){
            try{
                const data = await API.typeroom.list();
                this.categorias = data.data;
                this.$store.commit('LOADER',false);
            }catch(error){
                console.error(error);
                this.$store.commit('LOADER',false);
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
                estado: 1,
                categoria: this.form.categoria
            }
            try{
                const data = await API.checkin.booking(this.idBooking,data_form);
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
        }
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