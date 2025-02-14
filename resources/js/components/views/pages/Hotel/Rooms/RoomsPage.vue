<template>
     <v-container fluid>
          <div class="d-flex align-center py-3">
               <div>
               <h2 style="color:  #AD91FD">Habitaciones</h2> 
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
               item-key="id_habitacion"
               show-select
               class="elevation-1"
               loading-text="Cargando... Por favor espere"
          >
               <template v-slot:top>
                    <data-header
                         :cargando="cargando"
                         :eliminar="auxiliares.verEliminarVarios"
                         :headers="cabeceras"
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
                    />
               </template>
               <template v-slot:[`item.actions`]="{ item }">
                    <v-tooltip bottom>
                         <template v-slot:activator="{ on, attrs }">
                              <v-icon
                                   
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
               </template>
               <template v-slot:[`item.id_reservas`]="{ item }">
                    <v-chip v-if="item.id_reservas" small class="ma-2" color="success">{{ item.id_reservas }}</v-chip>
                    <v-chip v-else small class="ma-2" color="warning">No</v-chip>
               </template>
               <template v-slot:[`item.id_zona_hotel`]="{ item }">
                    <v-chip small class="ma-2" color="success">{{ mostrarZona(item.id_zona_hotel) }}</v-chip>
               </template>
               <template v-slot:[`item.id_categoria`]="{ item }">
                    <v-chip small class="ma-2" color="info">{{ mostrarCategoria(item.id_categoria) }}</v-chip>
               </template>
               <template v-slot:no-data>
                    <v-btn color="primary" @click="recargar"> Recargar </v-btn>
               </template>
          </v-data-table>
     </v-container>
</template>

<script>
import Snackbar from './RoomSnack'
import DataHeader from './RoomsHeader'
import DataDialog from './RoomsDialog'
export default {
     data() {
          return {
               snackbar: { activo: false, color: 'success', contenido: null },
               dialog: { titulo: '', modo: 0, cancelarLabel: 'Cancelar', aceptarLabel: 'Aceptar' },
               auxiliares: {
                    verEliminarVarios: false,
                    eliminarVarios: {},
                    modalAbierto: false,
               },
               cabeceras: [
                    { text: 'Nombre', value: 'nombre_habitacion' },
                    { text: 'Zona', value: 'id_zona_hotel' },
                    //{ text: 'Tipo Habitación', value: 'id_categoria' },
                    { text: 'Detalle', value: 'detalle' },
                    //{ text: 'Costo', value: 'costo' },
                    //{ text: 'Reservada', value: 'id_reservas' },
                    { text: 'Acciones', value: 'actions', align: 'center', sortable: false },
               ],
               cabecerasActivas: [
                    { name: 'Nombre', checked: true },
                    { name: 'Zona', checked: true },
                    { name: 'Tipo Habitación', checked: true },
                    { name: 'Detalle', checked: false },
                    { name: 'Costo', checked: true },
                    { name: 'Reservada', checked: false },
               ],
               cabecerasOcultas: [],
               items: [],
               zonas: [
                    { id_zona_hotel: 1, nombre_zona: 'Zona 1' },
                    { id_zona_hotel: 2, nombre_zona: 'Zona 2' },
               ],
               categorias: [
                    { id_categoria: 1, nombre_categoria: 'Categoria 1' },
                    { id_categoria: 2, nombre_categoria: 'Categoria 2' },
               ],
               apisDisponibles: {
                    rooms: () => '/api/rooms',
                    zones: () => '/api/zonehotel',
                    categories: () => '/api/typeroom',
                    drooms: () => '/api/deleteRooms',
               },
               parametrosAPI: {
                    id_habitacion: 0,
                    nombre_habitacion: '',
                    id_zona_hotel: 1,
                    id_categoria: 1,
                    detalle: '',
                    costo: 0.0,
                    estado: 0,
               },
               opciones: { sortBy: ['nombre_habitacion'], sortDesc: [], where: -1, like: '' },
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
     },
     components: { Snackbar, DataHeader, DataDialog },
     watch: {
          opciones: {
               handler() {
                    this.encapsularLlamada(
                         {
                              api: this.apisDisponibles.rooms(),
                              metodo: 'get',
                              additional: {
                                   rows: this.opciones.itemsPerPage,
                                   page: this.opciones.page,
                                   order: this.opciones.sortBy[0] || 'nombre_habitacion',
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
          activarSnackbar(activo, contenido = null, color = 'success') {
               this.snackbar = { activo, contenido, color }
          },
          recargar() {
               this.encapsularLlamada(
                    {
                         metodo: 'get',
                         api: this.apisDisponibles.rooms(),
                         additional: {
                              page: this.opciones.page || 1,
                              rows: this.opciones.itemsPerPage || 10,
                              order: this.opciones.sortBy[0] || 'nombre_habitacion',
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
          async encapsularLlamada({ api, metodo, id, additional, data }, next) {
               this.cargando = true
               await axios[metodo](
                    this.construirUrl(api, id, additional),
                    metodo != 'get' && metodo != 'delete' ? (data ? data : this.parametrosAPI) : {},
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
               let likeFind = this.cabeceras.find((h) => h.text === where)
               if (likeFind === undefined) likeFind = this.cabecerasOcultas.find((h) => h.text === where)
               this.opciones.where = likeFind ? likeFind.value : 'nombre_habitacion'
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
                              ? '¿Seguro que quieres eliminar est(a)(as) habitacion(es)?'
                              : modo === 1
                              ? 'Editar Habitacion'
                              : 'Crear Habitacion',
                    modo,
                    cancelarLabel: 'Cancelar',
                    aceptarLabel: modo === 0 ? 'Crear' : modo === 1 ? 'Guardar' : 'Aceptar',
               }
               this.auxiliares.modalAbierto = true
               this.parametrosAPI = Object.assign({}, item)
          },
          cancelar() {
               this.auxiliares.modalAbierto = false
               this.dialog = { titulo: '', modo: -1, cancelarLabel: 'Cancelar', aceptarLabel: 'Aceptar' }
               this.parametrosAPI = { nombre_habitacion: '', id_zona_hotel: 1, id_categoria: 1, detalle: '' }
          },
          async aceptar(modo, parametros) {
               const api = this.apisDisponibles.rooms()
               this.parametrosAPI = parametros
               switch (modo) {
                    case 0: {
                         await this.encapsularLlamada({ api, metodo: 'post' }, () => {
                              this.activarSnackbar(true, '¡Habitacion creada!', 'success')
                              this.recargar()
                         })
                         break
                    }
                    case 1: {
                         await this.encapsularLlamada(
                              { api, id: this.parametrosAPI.id_habitacion, metodo: 'patch' },
                              () => {
                                   this.activarSnackbar(true, '¡Habitacion actualizada!', 'success')
                                   this.recargar()
                              },
                         )
                         break
                    }
                    case 2: {
                         await this.encapsularLlamada(
                              { api, id: this.parametrosAPI.id_habitacion, metodo: 'delete' },
                              () => {
                                   this.activarSnackbar(true, '¡Habitacion eliminada!', 'success')
                                   this.recargar()
                              },
                         )
                         break
                    }
                    case 4: {
                         await this.encapsularLlamada(
                              {
                                   api: this.apisDisponibles.drooms(),
                                   metodo: 'post',
                                   data: { ids: Object.keys(this.auxiliares.eliminarVarios) },
                              },
                              () => {
                                   this.activarSnackbar(true, '¡Habitaciones eliminadas!', 'success')
                                   this.recargar()
                                   this.auxiliares.eliminarVarios = {}
                                   this.auxiliares.verEliminarVarios = false
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
               if (row.value) this.auxiliares.eliminarVarios[row.item.id_habitacion] = true
               else delete this.auxiliares.eliminarVarios[row.item.id_habitacion]
               if (Object.keys(this.auxiliares.eliminarVarios).length > 0) this.auxiliares.verEliminarVarios = true
               else this.auxiliares.verEliminarVarios = false
          },
          clickearFila(row, other) {
               other.select(!other.isSelected)
          },
          seleccionarTodos(row) {
               if (row.value) {
                    Array.from(row.items).forEach((room) => (this.auxiliares.eliminarVarios[room.id_habitacion] = true))
                    this.auxiliares.verEliminarVarios = true
               } else {
                    this.auxiliares.eliminarVarios = {}
                    this.auxiliares.verEliminarVarios = false
               }
          },
          gestionarVistaColumna(header, index) {
               /*if (header.checked) {
                    let cabeceraEncontrada = this.cabecerasOcultas.findIndex((h) => h.text === header.name)
                    let indexCalculado = index
                    if (this.cabeceras.length <= index) indexCalculado = this.cabeceras.length - 1

                    this.cabeceras.splice(indexCalculado, 0, this.cabecerasOcultas[cabeceraEncontrada])
                    this.cabecerasOcultas.splice(cabeceraEncontrada, 1)
               } else {
                    let cabeceraEncontrada = this.cabeceras.findIndex((h) => h.text === header.name)
                    this.cabecerasOcultas.push(this.cabeceras[cabeceraEncontrada])
                    this.cabeceras.splice(cabeceraEncontrada, 1)
               }*/
          },
     },
}
</script>