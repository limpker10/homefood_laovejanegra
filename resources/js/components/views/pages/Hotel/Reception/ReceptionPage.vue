<template>
     <div class="flex-grow-1">
          <snackbar
               :activo="snackbar.activo"
               :color="snackbar.color"
               :contenido="snackbar.contenido"
               :cerrar="activarSnackbar"
          />
          <v-toolbar
               class="elevation-0"
               color="transparent"
               style="height: 50px;"
          >
               <h2 style="color:  #AD91FD">Recepción</h2> 
               <v-spacer></v-spacer>
               <!--<v-tooltip bottom class="mr-4">
                    <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on">
                         <v-icon>mdi-reload</v-icon>
                    </v-btn>
                    </template>
                    Actualizar
               </v-tooltip>-->
               <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on" to="/rooms_sales">
                         <v-icon>mdi-post</v-icon>
                    </v-btn>
                    </template>
                    Realizar Venta
               </v-tooltip>
               <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on" @click="openReservations">
                         <v-icon>mdi-book-multiple-outline</v-icon>
                    </v-btn>
                    </template>
                    Ver Reservaciones
               </v-tooltip>
               <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on" to="/branchHotelSystem">
                         <v-icon>mdi-office-building-outline</v-icon>
                    </v-btn>
                    </template>
                    Cambiar Sucursal
               </v-tooltip>
               <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on" @click="getCash">
                         <v-icon>mdi-lock-outline</v-icon>
                    </v-btn>
                    </template>
                    Cerrar Caja
               </v-tooltip>
          </v-toolbar>
          <div class="my-2">
               <div v-for="(zona,i) in zonas_hotel"
                    :key="i">
                    <v-app-bar flat dense color="rgba(151, 136, 235, 0.18)" style="border-radius: 10px;">
                         <v-toolbar-title>
                              <b style="color:  #AD91FD;">{{ zona.field }}</b> 
                         </v-toolbar-title>
                    </v-app-bar>


                    <v-row dense class="pa-2">
                         <template v-for="(habitacion, i) in zona.groupList">
                              <v-col :key="i" :cols="cols" sm="4" md="3" xl="1" v-if="habitacion.id_zona === zona.id_zona"  class="pa-2">
                                   <v-card height="160px" :color="color(habitacion)">
                                        <v-app-bar flat dense color="rgba(0, 0, 0, 0)">
                                             <v-toolbar-title v-text="estado(habitacion)" class="white--text" />
                                             <v-spacer />
                                             <!--<div class="icon--grid--info " v-if="habitacion.estado==1">
                                                  <v-icon @click="verHabitacion(habitacion.id_reservas)" color="white"> mdi-information</v-icon>
                                             </div>-->
                                             <div class="icon--grid">
                                                  <v-icon @click="iconAction(habitacion)" color="white"> {{ icon(habitacion) }} </v-icon>
                                             </div>
                                             
                                        </v-app-bar>
                                        <v-divider />
                                        <v-list-item three-line>
                                             <v-list-item-avatar tile size="80">
                                                  <v-img :src="room_icon(habitacion)"></v-img>
                                             </v-list-item-avatar>
                                             <v-list-item-content>
                                                  <v-list-item-title class=" mb-1 white--text text-center">
                                                  <div class="headline">{{ habitacion.nombre_habitacion }}</div>
                                                  <div>{{ habitacion.category.nombre_categoria }}</div>
                                                  </v-list-item-title>
                                             </v-list-item-content>
                                        </v-list-item>
                                   </v-card>
                              </v-col>
                         </template>
                    </v-row>
               </div>
          </div>

          <v-dialog
               v-model="abierto"
               max-width="600px"
               max-height="500px"
               :persistent="true"
          >
               <v-card>
                    <div >
                         <v-card-title primary-title>
                         <p>Reservaciones para el dia</p>
                         </v-card-title>
                         <v-data-table
                              :headers="headers_reservations"
                              :items="data_reservations"
                              :items-per-page="5"
                              class="elevation-0"
                              >
                                   <template v-slot:[`item.rooms`]="{ item }">
                                        <div>{{ getRooms(item.habitaciones)}}</div>
                                   </template>
                                   <template v-slot:[`item.customer`]="{ item }">
                                        <div>{{ item.cliente.nombre}}</div>
                                   </template>
                                   <template v-slot:[`item.actions`]="{ item }">
                                        <v-tooltip bottom>
                                             <template v-slot:activator="{ on, attrs }">
                                                  <v-icon
                                                       v-on="on"
                                                       v-bind="attrs"
                                                       class="mr-2"
                                                       @click="reservation_checkin(item.id_reservas)"
                                                  >
                                                       mdi-arrow-left-circle
                                                  </v-icon>
                                             </template>
                                             Registar
                                        </v-tooltip>
                                   </template>
                              </v-data-table>
                         <v-card-actions>
                         <v-spacer />
                         <v-btn @click="abierto = false;">Cerrar</v-btn>
                         </v-card-actions>
                    </div>
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
//import DataDialog from '../Reception/components/ReceptionDialog'
import Snackbar from '../Bookings/components/BookingSnack'
export default {
     components: { 
          Snackbar,
          //DataDialog
     },
     data() {
          return {
               cargando: false,
               zonas: [],
               habitaciones: [],
               total: { habitaciones: 0, zonas: 0 },
               zonaSeleccionada: '',
               apisDisponibles: {
                    zones: () => '/api/zonehotel',
                    rooms: () => '/api/rooms',
               },
               snackbar: { activo: false, color: 'success', contenido: null },
               zonas_hotel: [],
               roomBooking:{},
               headers_reservations:[
                    { text: 'Reservación', sortable: false, value: 'id_reservas',align: 'center'  },
                    { text: 'Cliente', sortable: false,  value: 'customer' },
                    { text: 'Habitación(es)', sortable: false,  value: 'rooms' },
                    { text: 'Fecha de Alojamiento', sortable: false,  value: 'fecha_alojamiento' },
                    { text: 'Fecha de Salida (estimado)', sortable: false,  value: 'fecha_salida_estimado' },
                    { text: 'Acciones', sortable: false,  value: 'actions', align: 'center' },
               ],
               data_reservations: [],
               abierto: false,
               cerrarCajaDialog:false,
               
               caja: { usuario:{} },
               id_sucursal:0,
          }
     },
     computed: {
          getRooms(){
                return function (rooms) {
                    
                    const data = rooms.map(({ habitaciones }) => habitaciones.nombre_habitacion);
                    return data.join(', ')
               }    
          },
          cols() {
               switch (this.$vuetify.breakpoint.name) {
                    case 'xs':
                         return 12
                    case 'sm':
                         return 6                   
               }
          },
          color() {
               return function (habitacion) {
                    if (habitacion.estado === 0) return '#16a086'
                    else if (habitacion.estado === 1) return '#d9544f'
                    else if (habitacion.estado === 2) return '#5bc0de'
                    else return '#FB8C00'
               }
          },
          estado() {
               return function (habitacion) {
                    if (habitacion.estado === 0) return 'Disponible'
                    else if (habitacion.estado === 1) return 'Ocupada'
                    else if (habitacion.estado === 2) return 'Limpieza'
                    else return 'Reservada'
               }
          },
          room_icon() {
               return function (habitacion) {
                    if (habitacion.estado === 0) return '../assets/images/room_free_256px.png'
                    else if (habitacion.estado === 1) return '../assets/images/room_occuped_256px.png'
                    else if (habitacion.estado === 2) return '../assets/images/room_clean_256px.png'
                    else return '../assets/images/room_booking_256px.png'
               }
          },
          nombre() {
               return function (habitacion) {
                    if (habitacion.estado === 0 || habitacion.estado === 2) return habitacion.nombre_habitacion
                    else return habitacion.booking.client.nombre
               }
          },
          icon() {
               return function (habitacion) {
                    if (habitacion.estado === 0) return 'mdi-arrow-left-circle'
                    else if (habitacion.estado === 1) return 'mdi-arrow-right-circle'
                    else if (habitacion.estado === 2) return 'mdi-spray-bottle'
                    else return 'mdi-book-outline'
               }
          },
          
     },
     created() {
          this.$store.commit('LOADER',true);
          Array.prototype.groupBy = function(field){
               let groupedArr = [];
               this.forEach(function(e){
               //look for an existent group
               let group = groupedArr.find(g => g['field'] === e[field]);
               if (group == undefined){
                    //add new group if it doesn't exist
                    group = {field: e[field], groupList: []};
                    groupedArr.push(group);
               }
               
               //add the element to the group
               group.groupList.push(e);
               });
               
               return groupedArr;
          };
          this.encapsularLlamada({ metodo: 'get', api: this.apisDisponibles.zones() }, (response) => {
               this.zonas = response;
               console.log(response)
               this.recargar();
          });

          this.verReservaciones();
     },
     methods: {
          openReservations(){
               this.abierto = true;
               this.verReservaciones();
          },
          reservation_checkin(idReservation){
               this.$router.push('/checkin_booking/'+idReservation);
          },
          iconAction(habitacion){
               if (habitacion.estado === 0) this.$router.push('/checkin/'+habitacion.id_habitacion)
               if (habitacion.estado === 1) this.$router.push('/checkout/'+habitacion.id_habitacion)
               if (habitacion.estado === 2) this.updateRoom(habitacion.id_habitacion);
               if (habitacion.estado === 3) this.$router.push('/checkin_booking/'+habitacion.id_reservas)
          },
          async updateRoom(id){
               try{
                    const data = await API.rooms.updateField(id, { field:'estado', value:0 });
                    this.recargar();
                    this.$swal({
                         toast: true,
                         position: 'top-end',
                         icon:'success',
                         title:'Habitacion Disponible',
                         showConfirmButton:false,
                         timerProgressBar:true,
                         timer:2500
                    });
               }catch(error){
                    console.error(error);
               }
          },
          activarSnackbar(activo, contenido = null, color = 'success') {
               this.snackbar = { activo, contenido, color }
          },
          recargar() {
               this.encapsularLlamada(
                    {
                         metodo: 'get',
                         api: this.apisDisponibles.rooms(),
                         additional: {
                              page: 1,
                              rows: -1,
                         },
                    },
                    (response) => {
                         this.habitaciones = response.data
                         this.total.habitaciones = response.total
                         for (let index = 0; index < this.habitaciones.length; index++) {
                              this.habitaciones[index].name_zona = this.habitaciones[index].zone.nombre_zona;
                         }
                         this.zonas_hotel = this.habitaciones.groupBy('name_zona');
                         this.$store.commit('LOADER',false);
                    },
               )
          },
          construirUrl(api, id, additional) {
               var begin
               if (additional != null) {
                    begin = '?'
                    Object.keys(additional).forEach((param, index) => {
                         begin = begin.concat(param, '=', additional[param])
                         if (index != Object.keys(additional).length - 1) begin = begin.concat('&')
                    })
               } else begin = ''
               return `${api}${id != null ? `/${id}` : begin}`
          },
          async encapsularLlamada({ api, metodo, id, additional, data, noauth }, next) {
               this.cargando = true
               await axios[metodo](
                    this.construirUrl(api, id, additional),
                    metodo != 'get' && metodo != 'delete'
                         ? data
                              ? data
                              : this.parametrosAPI
                         : metodo === 'get'
                         ? {
                                transformRequest: (data, headers) => {
                                     if (noauth) delete headers.common['Authorization']
                                },
                           }
                         : {},
               )
                    .then(({ data, statusText }) => {
                         /*if (statusText == 'OK' || statusText == 'Created') next(data)
                         else throw new Error(data.message || 'Sucedió un error')*/
                         console.log(data);
                         console.log(statusText);
                         next(data);
                    })
                    .catch((error) => {
                         console.error(error);
                         this.activarSnackbar(true, error, 'error')
                         // this.error = error
                    })
                    .finally(() => (this.cargando = false))
          },
          async verReservaciones(){
               try{
                    const data = await API.bookings.pending_bookings();
                    console.log(data);
                    this.data_reservations = data.data;
               }catch(error){
                    console.error(error);
               }
          },

          async verHabitacion(idReservation){
               try{
                    const data = await API.bookings.read(idReservation);
                    this.roomBooking = data.data;
               }catch(error){
                    console.error(error);
               }
          },

          //CAJA
          async getCash(){
               try{
                    const detail = await API.cash.detail(this.$route.params.id, "hotel", 1);
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
                    this.$router.push('/branchHotelSystem');
               }catch(error){
                    console.error(error.response.status);
                    this.dialogDelete = false;
               }
          },
          seleccionMesaDrop(){
               let mesa = this.mesa_sele;
               //console.log(this.mesa_sele);
               this.$router.push('/posSystem/'+mesa.id_mesa)
          }
     },
}
</script>

<style lang="scss" scoped>
.icon--grid {
     display: flex;
     position: absolute;
     right: 0px;
     top: 0px;
     place-items: center;
     place-content: center;
     height: 48px;
     width: 50px;
     background-color: rgba(0, 0, 0, 0.2);
     border-radius: 4px;
}
.icon--grid--info {
     display: flex;
     position: absolute;
     right: 50px;
     top: 0px;
     place-items: center;
     place-content: center;
     height: 48px;
     width: 50px;
     background-color: rgba(0, 0, 0, 0.2);
     border-radius: 4px;
}
.place--center {
     height: 100px;
     display: flex;
     flex-direction: column;
     place-items: center;
     place-content: center;
}
</style>
