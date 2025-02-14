<template>
     <v-container fluid>
          <div class="d-flex align-center py-3">
               <div>
               <h2 style="color:  #AD91FD">Reservas de Habitaciones</h2>
               <!--<v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>-->
               </div>
               <v-spacer></v-spacer>
          </div>
          <snackbar
               :activo="snackbar.activo"
               :color="snackbar.color"
               :contenido="snackbar.contenido"
               :cerrar="activarSnackbar"
          />

          <v-data-table
               :headers="cabeceras"
               :items="items"
               :loading="cargando"
               :options.sync="opciones"
               :items-per-page="10"
               :server-items-length="total"
               :footer-props="{ 'items-per-page-text': 'Filas por pagina', 'items-per-page-all-text': 'Todo' }"
               @item-selected="seleccionarFila"
               @click:row="clickearFila"
               @toggle-select-all="seleccionarTodos"
               item-key="id_reservas"
               show-select
               class="elevation-1"
               loading-text="Cargando... Por favor espere"
          >
               <template v-slot:top>
                    <data-header
                         :cargando="cargando"
                         :eliminar="auxiliares.verEliminarVarios"
                         :headers="cabecerasActivas"
                         :buscador="buscador"
                         :gestionarVistaColumna="gestionarVistaColumna"
                         :gestionarBusqueda="gestionarBusqueda"
                         :modal="modal"
                         :recargar="recargar"
                    />
                    <data-dialog
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
                    />
               </template>
               <template v-slot:[`item.actions`]="{ item }">
                    <v-tooltip bottom>
                         <template v-slot:activator="{ on, attrs }">
                              <v-icon
                                   v-if="item.estado == 0"
                                   v-on="on"
                                   v-bind="attrs"
                                   small
                                   class="mr-2"
                                   @click.stop="modal(1, item)"
                                   :disabled="cargando"
                              >
                                   mdi-pencil
                              </v-icon>
                         </template>
                         Editar
                    </v-tooltip>
                    <v-tooltip bottom>
                         <template v-slot:activator="{ on, attrs }">
                              <v-icon
                                   v-if="item.estado == 0"
                                   v-on="on"
                                   v-bind="attrs"
                                   small
                                   @click.stop="modal(2, item)"
                                   :disabled="cargando"
                              >
                                   mdi-delete
                              </v-icon>
                         </template>
                         Eliminar
                    </v-tooltip>
                    <v-tooltip bottom>
                         <template v-slot:activator="{ on, attrs }">
                              <v-icon
                                   v-on="on"
                                   v-bind="attrs"
                                   small
                                   :disabled="cargando"
                                   @click="viewDetail(item.id_reservas)"
                              >
                                   mdi-eye
                              </v-icon>
                         </template>
                         Ver Detalle
                    </v-tooltip>
                   <v-tooltip bottom>
                       <template v-slot:activator="{ on, attrs }">
                           <v-icon
                               v-on="on"
                               v-bind="attrs"
                               small
                               :disabled="cargando"
                               @click="getPDF(item.id_reservas)"
                           >
                               mdi-file-pdf-box
                           </v-icon>
                       </template>
                       Constancia de reserva
                   </v-tooltip>

               </template>
               <template v-slot:[`item.created_at`]="{ item }">
                    {{ mostrarFechaCreacion(item.created_at) }}
               </template>
               <template v-slot:[`item.cliente`]="{ item }">
                    <div v-if="item.cliente">{{ item.cliente.nombre }}</div>
                    <div v-else>CLIENTES VARIOS</div>
               </template>
               <template v-slot:[`item.estado`]="{ item }">
                    <v-chip
                         small
                         class="ma-2"
                         text-color="white"
                         :color="colorEstado(item.estado)"
                    >
                    {{estado(item.estado)}}
                    </v-chip>
               </template>
               <template v-slot:[`item.dias_alojamiento`]="{ item }"> {{ item.dias_alojamiento }} dias </template>
               <template v-slot:[`item.habitaciones`]="{ item }">
                    <v-tooltip bottom>
                         <template v-slot:activator="{ on, attrs }">
                              <v-chip
                                   small
                                   class="ma-2"
                                   color="accent"
                                   v-on="on"
                                   v-bind="attrs"
                                   @click.stop="
                                        auxiliares.verHabitaciones = true
                                        auxiliares.habitaciones = item.habitaciones
                                   "
                              >
                                   <template v-if="item.habitaciones">{{ item.habitaciones.length }} habitaciones</template>
                                   <template v-else>0 habitaciones</template>
                              </v-chip>
                         </template>
                         Ver Habitaciones
                    </v-tooltip>
               </template>
               <template v-slot:no-data>
                    <v-btn color="primary" @click="recargar"> Recargar </v-btn>
               </template>
          </v-data-table>
          <v-dialog v-model="auxiliares.verHabitaciones" max-width="500px">
               <v-card>
                    <v-card-title>
                         <p class="mb-0">Habitaciones</p>
                    </v-card-title>
                    <v-container class="pa-3">
                         <v-card outlined shaped v-for="(habitacion, i) in auxiliares.habitaciones" :key="i">
                              <v-card-title v-if="habitacion.habitaciones.nombre_habitacion">
                                   {{ habitacion.habitaciones.nombre_habitacion }}
                              </v-card-title>
                              <v-card-subtitle v-if="habitacion.habitaciones.id_zona_hotel && habitacion.habitaciones.id_categoria">
                                   Zona: {{ mostrarZona(habitacion.habitaciones.id_zona_hotel) }}, Categoria:
                                   {{ mostrarCategoria(habitacion.habitaciones.id_categoria) }}
                              </v-card-subtitle>
                              <v-card-text v-if="habitacion.habitaciones.detalle">
                                   {{ habitacion.habitaciones.detalle }}
                              </v-card-text>
                         </v-card>
                    </v-container>
               </v-card>
          </v-dialog>
     </v-container>
</template>

<script>
import Snackbar from './components/BookingSnack'
import DataHeader from './components/BookingsHeader'
import DataDialog from './components/BookingsDialog'
import moment from 'moment'
export default {
     data() {
          return {
               snackbar: { activo: false, color: 'success', contenido: null },
               dialog: { titulo: '', modo: 0, cancelarLabel: 'Cancelar', aceptarLabel: 'Aceptar' },
               auxiliares: {
                    verEliminarVarios: false,
                    eliminarVarios: {},
                    modalAbierto: false,
                    verHabitaciones: false,
                    habitaciones: [],
               },
               cabeceras: [
                    { text: 'Cliente', value: 'cliente' },
                    { text: 'Fecha Alojamiento', value: 'fecha_alojamiento' },
                    { text: 'Estadia', value: 'dias_alojamiento' },
                    { text: 'Fecha de Creacion', value: 'created_at' },
                    { text: 'Habitaciones', value: 'habitaciones' },
                    { text: 'Costo', value: 'costo_total' },
                    { text: 'Estado', value: 'estado' },
                    { text: 'Acciones', value: 'actions', align: 'center', sortable: false },
               ],
               cabecerasActivas: [
                    { name: 'Cliente', checked: true },
                    { name: 'Fecha Alojamiento', checked: true },
                    { name: 'Estadia', checked: true },
                    { name: 'Fecha de Creacion', checked: false },
                    { name: 'Habitaciones', checked: true },
               ],
               cabecerasBusqueda: [
                    { name: 'Cliente', value: 'id_cliente' },
                    { name: 'Fecha Alojamiento', value: 'fecha_alojamiento' },
                    { name: 'Estadia', value: 'dias_alojamiento' },
                    { name: 'Fecha de Creacion', value: 'created_at' },
               ],
               cabecerasOcultas: [],
               items: [],
               zonas: [],
               categorias: [],
               apisDisponibles: {
                    zones: () => '/api/zonehotel',
                    categories: () => '/api/typeroom',
                    bookings: () => '/api/bookings',
                    dbookings: () => '/api/deleteBookings',
               },
               parametrosAPI: {
                    id_reservas: 0,
                    id_cliente: 0,
                    nombre_cliente: '',
                    fecha_alojamiento: '2021-03-19',
                    fecha_salida_estimado: '2021-03-19',
                    dias_alojamiento: 0,
                    habitaciones: [],
                    costo_total: 0,
               },
               opciones: { sortBy: ['created_at'], sortDesc: [true], where: -1, like: '' },
               total: 0,
               cargando: false,
          }
     },
     computed: {
          mostrarZona(vm) {
               return function (zona) {
                    let find = vm.zonas.find((zone) => String(zone.id_zona_hotel) === String(zona))
                    return find ? find.nombre_zona : ''
               }
          },
          mostrarCategoria(vm) {
               return function (categoria) {
                    let find = vm.categorias.find((category) => String(category.id_categoria) === String(categoria))
                    return find ? find.nombre_categoria : ''
               }
          },
          mostrarFechaCreacion() {
               return function (fechaCreacion) {
                    return moment(fechaCreacion).format('YYYY-MM-DD HH:mm')
               }
          },
          estado(){
               return function (estado) {
                    if(estado == 0) return 'Pendiente'
                    if(estado == 1) return 'Ingreso'
                    if(estado == 2) return 'Salida'
                    if(estado == 3) return 'Cancelado'
               }
          },
          colorEstado(){
               return function (estado) {
                    if(estado == 0) return 'blue lighten-2'
                    if(estado == 1) return 'green darken-1'
                    if(estado == 2) return 'amber lighten-2'
                    if(estado == 3) return 'red darken-2'
               }
          }
     },
     components: { Snackbar, DataHeader, DataDialog },
     watch: {
          opciones: {
               handler() {
                    this.encapsularLlamada(
                         {
                              api: this.apisDisponibles.bookings(),
                              metodo: 'get',
                              additional: {
                                   rows: this.opciones.itemsPerPage,
                                   page: this.opciones.page,
                                   order: this.opciones.sortBy[0] || 'id_reservas',
                                   oby: this.opciones.sortDesc[0] ? 'desc' : 'asc',
                                   where: this.opciones.where || -1,
                                   like: this.opciones.like || '',
                              },
                         },
                         (response) => {
                              this.items = response.data
                              this.total = response.total
                         },
                    )
               },
               deep: true,
          },
     },
     mounted() {
          this.cabecerasActivas.forEach((header, index) => {
               if (!header.checked) this.gestionarVistaColumna(header, index)
          })
     },
     created() {
          this.encapsularLlamada({ metodo: 'get', api: this.apisDisponibles.zones() }, (response) => {
               this.zonas = response
               this.encapsularLlamada({ metodo: 'get', api: this.apisDisponibles.categories() }, (response) => {
                    this.categorias = response
                    this.recargar()
               })
          })
     },
     methods: {
          viewDetail(id_reservas){
               this.$router.push('/booking/'+id_reservas)
          },
          activarSnackbar(activo, contenido = null, color = 'success') {
               this.snackbar = { activo, contenido, color }
          },
          getPDF(id){
              axios({
                 url: `api/generate-pdf-reservation/${id}`,
                 method: 'GET',
                 responseType: 'blob',
              }).then((response) => {
                 var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                 var fileLink = document.createElement('a');
                 fileLink.href = fileURL;
                 fileLink.setAttribute('download', `${id}.pdf`);
                 document.body.appendChild(fileLink);
                 fileLink.click();
              });
          },
          recargar() {
               this.encapsularLlamada(
                    {
                         metodo: 'get',
                         api: this.apisDisponibles.bookings(),
                         additional: {
                              page: this.opciones.page || 1,
                              rows: this.opciones.itemsPerPage || 10,
                              order: this.opciones.sortBy[0] || 'id_reservas',
                              oby: this.opciones.sortDesc[0] ? 'desc' : 'asc' || 'asc',
                              where: this.opciones.where || -1,
                              like: this.opciones.like || '',
                         },
                    },
                    (response) => {
                         this.items = response.data
                         this.total = response.total
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
          buscador(word, where) {
               let likeFind = this.cabecerasBusqueda.find((h) => h.name === where)
               // if (likeFind === undefined) likeFind = this.cabecerasOcultas.find((h) => h.text === where)
               this.opciones.where = likeFind ? likeFind.value : 'id_cliente'
               this.opciones.like = word
          },
          gestionarBusqueda(toHide) {
               if (toHide) {
                    this.opciones.where = -1
                    // this.auxiliares.word = ''
               }
               // this.auxiliares.buscando = !toHide
          },
          modal(modo, item) {
               this.dialog = {
                    titulo:
                         modo === 2 || modo === 4
                              ? '¿Seguro que quieres eliminar est(a)(as) reserva(s)?'
                              : modo === 1
                              ? 'Editar Reserva'
                              : 'Crear Reserva',
                    modo,
                    cancelarLabel: 'Cancelar',
                    aceptarLabel: modo === 0 ? 'Crear' : modo === 1 ? 'Guardar' : 'Aceptar',
               }
               this.auxiliares.modalAbierto = true,
               console.log(item)
               this.parametrosAPI = Object.assign({}, item)
          },
          cancelar() {
               this.auxiliares.modalAbierto = false
               this.dialog = { titulo: '', modo: -1, cancelarLabel: 'Cancelar', aceptarLabel: 'Aceptar' }
               this.parametrosAPI = {
                    id_reservas: 0,
                    id_cliente: 0,
                    nombre_cliente: '',
                    fecha_alojamiento: '2021-03-19',
                    fecha_salida_estimado:'2021-03-19',
                    dias_alojamiento: 0,
                    habitaciones: [],
                    costo_total: 0,
               }
          },
          async aceptar(modo, parametros) {
               const api = this.apisDisponibles.bookings()
               this.parametrosAPI = parametros
               switch (modo) {
                    case 0: {
                         await this.encapsularLlamada({ api, metodo: 'post' }, () => {
                              this.activarSnackbar(true, '¡Reserva creada!', 'success')
                              this.recargar()
                         })
                         break
                    }
                    case 1: {
                         await this.encapsularLlamada(
                              { api, id: this.parametrosAPI.id_reservas, metodo: 'patch' },
                              () => {
                                   this.activarSnackbar(true, 'Reserva actualizada!', 'success')
                                   this.recargar()
                              },
                         )
                         break
                    }
                    case 2: {
                         await this.encapsularLlamada(
                              { api, id: this.parametrosAPI.id_reservas, metodo: 'delete' },
                              () => {
                                   this.activarSnackbar(true, 'Reserva eliminada!', 'success')
                                   this.recargar()
                              },
                         )
                         break
                    }
                    case 4: {
                         await this.encapsularLlamada(
                              {
                                   api: this.apisDisponibles.dbookings(),
                                   metodo: 'post',
                                   data: { ids: Object.keys(this.auxiliares.eliminarVarios) },
                              },
                              () => {
                                   this.activarSnackbar(true, 'Reservas eliminadas!', 'success')
                                   this.recargar()
                                   this.auxiliares.verEliminarVarios = false
                                   this.auxiliares.eliminarVarios = {}
                              },
                         )
                         break
                    }
                    default:
                         break
               }
               this.cancelar()
          },
          seleccionarFila(row) {
               if (row.value) this.auxiliares.eliminarVarios[row.item.id_reservas] = true
               else delete this.auxiliares.eliminarVarios[row.item.id_reservas]
               if (Object.keys(this.auxiliares.eliminarVarios).length > 0) this.auxiliares.verEliminarVarios = true
               else this.auxiliares.verEliminarVarios = false
          },
          clickearFila(row, other) {
               other.select(!other.isSelected)
          },
          seleccionarTodos(row) {
               if (row.value) {
                    Array.from(row.items).forEach(
                         (booking) => (this.auxiliares.eliminarVarios[booking.id_reservas] = true),
                    )
                    this.auxiliares.verEliminarVarios = true
               } else {
                    this.auxiliares.eliminarVarios = {}
                    this.auxiliares.verEliminarVarios = false
               }
          },
          gestionarVistaColumna(header, index) {
               if (header.checked) {
                    let cabeceraEncontrada = this.cabecerasOcultas.findIndex((h) => h.text === header.name)
                    let indexCalculado = index
                    if (this.cabeceras.length <= index) indexCalculado = this.cabeceras.length - 1

                    this.cabeceras.splice(indexCalculado, 0, this.cabecerasOcultas[cabeceraEncontrada])
                    this.cabecerasOcultas.splice(cabeceraEncontrada, 1)
               } else {
                    let cabeceraEncontrada = this.cabeceras.findIndex((h) => h.text === header.name)
                    this.cabecerasOcultas.push(this.cabeceras[cabeceraEncontrada])
                    this.cabeceras.splice(cabeceraEncontrada, 1)
               }
          },
     },
}
</script>
