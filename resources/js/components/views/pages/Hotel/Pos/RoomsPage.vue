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
               <h2 style="color:  #AD91FD">Habitaciones</h2> 
               <v-spacer></v-spacer>
               <v-tooltip bottom class="mr-4">
                    <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on" :to="'/reception/'+id_sucursal">
                         <v-icon>mdi-room-service-outline</v-icon>
                    </v-btn>
                    </template>
                    Volver a Recepción
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

          <!--<data-dialog
               :cargando="cargando"
               :dialog="dialog"
               :cancelar="cancelar"
               :aceptar="aceptar"
               :abierto="auxiliares.modalAbierto"
               :zonas="zonas"
               :categorias="categorias"
               :parametrosAPI="parametrosAPI"
               :encapsularLlamada="encapsularLlamada"
               :activarSnackbar="activarSnackbar"
               :recargar="recargar"
               :mostrarZona="mostrarZona"
               :mostrarCategoria="mostrarCategoria"
          />-->
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
               id_sucursal: localStorage.getItem('SYSTEM_ID_BRANCH'),
          }
     },
     computed: {
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
                    else if (habitacion.estado === 1) return '#16a086'
                    else return '#5bc0de'
               }
          },
          estado() {
               return function (habitacion) {
                    if (habitacion.estado === 0) return 'Realizar Venta'
                    else if (habitacion.estado === 1) return 'Realizar Venta'
                    else return 'Limpieza'
               }
          },
          room_icon() {
               return function (habitacion) {
                    if (habitacion.estado === 0) return '../assets/images/room_free_256px.png'
                    else if (habitacion.estado === 1) return '../assets/images/room_occuped_256px.png'
                    else return '../assets/images/room_clean_256px.png'
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
                    else return 'mdi-spray-bottle'
               }
          },
          
     },
     created() {
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
          }

          this.encapsularLlamada({ metodo: 'get', api: this.apisDisponibles.zones() }, (response) => {
               this.zonas = response;
               this.recargar();
          });

          this.verReservaciones();
     },
     methods: {
          iconAction(habitacion){
               this.$router.push('/room_pos/'+habitacion.id_habitacion)
          },
          updateRoom(){
               this.$swal({
                    toast: true,
                    position: 'top-end',
                    icon:'success',
                    title:'Habitacion Disponible',
                    showConfirmButton:false,
                    timerProgressBar:true,
                    timer:2500
               });
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
                         const data = response.data;
                         this.habitaciones =data.filter((room) => room.estado == 1)
                         //this.habitaciones = response.data
                         this.total.habitaciones = response.total
                         for (let index = 0; index < this.habitaciones.length; index++) {
                              this.habitaciones[index].name_zona = this.habitaciones[index].zone.nombre_zona;
                         }
                         this.zonas_hotel = this.habitaciones.groupBy('name_zona');
                         console.log(this.zonas_hotel);

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
               }catch(error){
                    console.error(error);
               }
          },
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
.place--center {
     height: 100px;
     display: flex;
     flex-direction: column;
     place-items: center;
     place-content: center;
}
</style>
