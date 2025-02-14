<template>
     <v-dialog v-model="abierto" max-width="650px" :persistent="cargando" @click:outside="cerrar">
          <v-card>
               <v-card-title>
                    <p class="ma-0">{{ dialog.titulo }}</p>
               </v-card-title>
               <v-container v-if="dialog.modo != 2 && dialog.modo != 4" class="px-6 py-0">
                    <v-stepper v-model="reservaStep" vertical class="boxshadow py-0">
                         <!-- STEP1 -->
                         <v-stepper-step
                              :complete="dialog.modo === 0 ? reservaStep > 1 : true"
                              step="1"
                              :editable="dialog.modo === 1"
                         >
                              Selecciona un cliente
                              <small>Crealo si es necesario</small>
                         </v-stepper-step>
                         <v-stepper-content step="1">
                              <v-container class="containerS">
                                   <v-card class="mb-3" elevation="0">
                                        <v-container v-if="!step1" class="d-flex">
                                             <v-card
                                                  class="ma-auto card-display"
                                                  height="100px"
                                                  width="100px"
                                                  rounded
                                                  outlined
                                                  hover
                                                  :disabled="cargando"
                                                  :loading="cargando"
                                                  @click="abrirBuscarDialogo(0)"
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
                                                  :disabled="cargando"
                                                  @click="abrirCrearDialogo(0)"
                                             >
                                                  <v-icon size="40">mdi-plus</v-icon>
                                                  Crear
                                             </v-card>
                                        </v-container>

                                        <v-card elevation="3" outlined shaped v-else>
                                             <v-card-title v-if="clienteSeleccionado.nombre">{{
                                                  clienteSeleccionado.nombre
                                             }}</v-card-title>
                                             <v-card-subtitle v-if="clienteSeleccionado.nro_doc">{{
                                                  clienteSeleccionado.nro_doc
                                             }}</v-card-subtitle>
                                             <v-card-text v-if="clienteSeleccionado.direccion">{{
                                                  clienteSeleccionado.direccion
                                             }}</v-card-text>
                                             <v-card-actions v-if="clienteSeleccionado.id_cliente">
                                                  <v-btn  plain @click="clienteSeleccionado = {}"
                                                       >Eliminar</v-btn
                                                  >
                                             </v-card-actions>
                                        </v-card>
                                   </v-card>
                                   <v-btn
                                        color="primary"
                                        @click="reservaStep = 2"
                                        :disabled="!step1"
                                        v-if="dialog.modo === 0"
                                        class="fit"
                                   >
                                        Continuar
                                   </v-btn>
                              </v-container>
                         </v-stepper-content>
                         <!-- STEP2 -->
                         <v-stepper-step
                              :complete="dialog.modo === 0 ? reservaStep > 2 : true"
                              step="2"
                              :editable="dialog.modo === 1"
                         >
                              Selecciona las fechas de alojamiento
                         </v-stepper-step>
                         <v-stepper-content step="2">
                              <v-card class="mb-12" elevation="0">
                                   <v-date-picker v-model="fechas_reservadas" range full-width :min="minimumDate" />
                              </v-card>

                              <v-btn
                                   color="primary"
                                   @click="reservaStep = 3"
                                   :disabled="!step2"
                                   v-if="dialog.modo === 0"
                              >
                                   Continuar
                              </v-btn>
                              <v-btn text @click="reservaStep = 1" v-if="dialog.modo === 0"> Anterior </v-btn>
                         </v-stepper-content>
                         <!-- STEP3 -->
                         <v-stepper-step
                              :complete="dialog.modo === 0 ? reservaStep > 3 : true"
                              step="3"
                              :editable="dialog.modo === 1"
                         >
                              Selecciona la habitación
                              <small>Creala si es necesario</small>
                         </v-stepper-step>
                         <v-stepper-content step="3">
                              <v-card class="mb-12" elevation="0" :loading="cargando">
                                   <v-app-bar flat color="rgba(0, 0, 0, 0)">
                                        <v-spacer />
                                        <v-tooltip bottom>
                                             <template v-slot:activator="{ on, attrs }">
                                                  <v-btn
                                                       color="primary"
                                                       icon
                                                       v-bind="attrs"
                                                       v-on="on"
                                                       @click="abrirBuscarDialogo(1)"
                                                       :disabled="cargando"
                                                  >
                                                       <v-icon>mdi-magnify</v-icon>
                                                  </v-btn>
                                             </template>
                                             Importar Habitación
                                        </v-tooltip>
                                        <v-tooltip bottom>
                                             <template v-slot:activator="{ on, attrs }">
                                                  <v-btn
                                                       color="primary"
                                                       icon
                                                       v-bind="attrs"
                                                       v-on="on"
                                                       @click="abrirCrearDialogo(1)"
                                                       :disabled="cargando"
                                                  >
                                                       <v-icon>mdi-plus</v-icon>
                                                  </v-btn>
                                             </template>
                                             Crear Habitación
                                        </v-tooltip>
                                   </v-app-bar>
                                   <div style="text-align: center;" v-if="habitaciones_seleccionadas.length == 0">
                                        Busque una habitación o cree una nueva
                                   </div>
                                   <v-list v-else>
                                        <v-list-item-group v-model="habitaciones_seleccionadas">
                                             <template v-for="(shabitacion, i) in habitaciones_disponibles">
                                                  <v-list-item
                                                       :key="`item-${i}`"
                                                       :value="shabitacion"
                                                       readonly
                                                  >
                                                       <template>
                                                            <v-list-item-content>
                                                                 <v-list-item-title v-text="roomdenom(shabitacion)" />
                                                            </v-list-item-content>

                                                            <!--<v-list-item-action>
                                                                 <v-checkbox
                                                                      :input-value="active"
                                                                      color="deep-purple lighten-2"
                                                                 />
                                                            </v-list-item-action>-->
                                                       </template>
                                                  </v-list-item>
                                             </template>
                                        </v-list-item-group>
                                   </v-list>
                                   <v-row v-if="habitaciones_seleccionadas.length > 0">
                                        <v-col
                                        cols="6"
                                        style="padding: 0px 15px;"
                                        >
                                            <v-select
                                                :items="categorias"
                                                label="Tipo de Habitación"
                                                @change="changeCategoria"
                                                item-value="id_categoria"
                                                :item-text="getItemText"
                                                required
                                                return-object
                                                v-model="categoria_habitacion_reserva"
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
                                                v-model="categoria_habitacion_reserva.precio"
                                                v-on:keyup="changeCategoria"
                                            ></v-text-field>
                                        </v-col>
                                   </v-row>
                              </v-card>
                              <v-btn
                                   color="primary"
                                   @click="reservaStep = 4"
                                   :disabled="!step3"
                                   v-if="dialog.modo === 0"
                              >
                                   Continuar
                              </v-btn>
                              <v-btn text @click="reservaStep = 2" v-if="dialog.modo === 0"> Anterior </v-btn>
                         </v-stepper-content>
                         <!-- STEP4 -->
                         <v-stepper-step step="4" :editable="dialog.modo === 1"> Resumen </v-stepper-step>
                         <v-stepper-content step="4">
                              <v-card class="mb-12" elevation="0">
                                   <v-text-field
                                        v-model="parametros.nombre"
                                        label="Cliente"
                                        :disabled="cargando"
                                        type="text"
                                        readonly
                                        required
                                   />
                                   <v-text-field
                                        :value="parametros.fecha_alojamiento + ' ~ '+ parametros.fecha_salida_estimado"
                                        label="Fechas de Alojamiento"
                                        :disabled="cargando"
                                        type="text"
                                        readonly
                                        required
                                   />
                                   <!--<v-text-field
                                        v-model="parametros.fecha_salida_estimado"
                                        label="Fecha de Alojamiento"
                                        :disabled="cargando"
                                        type="text"
                                        readonly
                                        required
                                   />-->
                                   <v-text-field
                                        v-model="parametros.dias_alojamiento"
                                        label="Dias de Alojamiento"
                                        :disabled="cargando"
                                        type="text"
                                        readonly
                                        required
                                   />
                                   <v-card-text class="px-0">
                                        <span class="subheading">Habitación reservada</span>
                                        <v-chip-group>
                                             <template v-for="(habitacion, i) in parametros.habitaciones">
                                                  <v-chip :key="i" color="primary">{{ habitacion.nombre_habitacion }}, Zona: {{habitacion.zone.nombre_zona}}</v-chip>
                                             </template>
                                        </v-chip-group>
                                   </v-card-text>
                                   <v-text-field
                                        v-model="costoTotal"
                                        label="Costo Total"
                                        :disabled="cargando"
                                        type="text"
                                        prefix="S/."
                                        readonly
                                   />
                              </v-card>
                              <v-btn text @click="reservaStep = 3" v-if="dialog.modo === 0"> Anterior </v-btn>
                         </v-stepper-content>
                    </v-stepper>
               </v-container>

               <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="purple lighten-3" text @click="cerrar" :disabled="cargando">
                         {{ dialog.cancelarLabel }}
                    </v-btn>
                    <v-btn  color="primary" @click="salvar" :disabled="cargando || step4">
                         {{ dialog.aceptarLabel }}
                    </v-btn>
               </v-card-actions>
          </v-card>

          <v-dialog
               v-model="buscarDialogo"
               max-width="600px"
               max-height="500px"
               :persistent="cargando"
               @click:outside="cerrarDialogo"
          >
               <v-card>
                    <v-card-title primary-title>
                         <p>Buscar {{ dialogModo === 0 ? 'Cliente' : 'Habitacion' }}</p>
                    </v-card-title>
                    <v-data-table
                         :headers="cabeceras"
                         :items="items"
                         :loading="cargando"
                         :options.sync="opciones"
                         :items-per-page="5"
                         :server-items-length="total"
                         :footer-props="{
                              itemsPerPageOptions: [5, 10],
                              itemsPerPageText: 'Filas por pagina',
                              itemsPerPageAllText: 'Todo',
                         }"
                         @item-selected="seleccionarFila"
                         @click:row="clickearFila"
                         :item-key="itemKey"
                         :show-select="showSelect"
                         class="elevation-0"
                         loading-text="Cargando... Por favor espere"
                         max-height="400px"
                         :single-select="singleSelect"
                    >
                         <template v-slot:top>
                              <v-btn-toggle
                                   v-if="dialogModo === 1"
                                   v-model="tipoHabitacion"
                                   tile
                                   color="deep-purple lighten-2" 
                                   group
                                   rounded
                              >
                                   <v-btn :value="-1" :disabled="cargando"> {{ tabfromDisponibles }} </v-btn>
                                   <v-btn value="filled" :disabled="cargando"> Proximamente disponibles </v-btn>
                              </v-btn-toggle>
                         </template>
                         <template v-slot:[`item.booking`]="{ item }">
                              {{ fechaDisponible(item.booking) }}
                         </template>
                         <template v-slot:[`item.id_zona`]="{ item }">
                              <div v-if="item.zone">{{ item.zone.nombre_zona}}</div>
                         </template>
                         <template v-slot:[`item.id_categoria`]="{ item }">{{
                              mostrarCategoria(item.id_categoria)
                         }}</template>

                         <template v-slot:no-data>
                              <p v-if="dialogModo === 1">No hay habitaciones disponibles</p>
                              <v-btn color="primary" @click="recargarBuscar"> Recargar </v-btn>
                         </template>
                    </v-data-table>
                    <v-card-actions v-if="tipoHabitacion != 'filled'">
                         <v-spacer />
                         <v-btn  plain @click="buscarDialogo = false">Cancelar</v-btn>
                         <v-btn color="primary" :disabled="!step1" @click="buscarDialogo = false">Seleccionar</v-btn>
                    </v-card-actions>
               </v-card>
          </v-dialog>
          <v-dialog
               v-model="crearDialogo"
               max-width="800px"
               max-height="500px"
               :persistent="cargando"
               @click:outside="cerrarDialogo"
          >
               <v-card>
                    <v-card-title primary-title>
                         <p>Crear {{ dialogModo === 0 ? 'Cliente' : 'Habitacion' }}</p>
                    </v-card-title>
                    <v-container class="px-10">
                         <v-form v-model="formulario" ref="form" lazy-validation>
                              <template v-if="dialogModo === 0">
                                   <v-row>
                                        <v-col cols="6">
                                             <v-select
                                                  v-model="doc"
                                                  :items="docs"
                                                  :rules="docReglas"
                                                  :disabled="cargando"
                                                  item-text="tipo_documento"
                                                  item-value="id_tipo_doc"
                                                  label="Tipo de documento"
                                                  return-object
                                                  @change="cambiarDocType"
                                             />
                                        </v-col>
                                        <v-col cols="6">
                                             <v-text-field
                                                  v-model="nuevoCliente.nro_doc"
                                                  label="Nro. documento"
                                                  type="number"
                                                  :rules="nrodocReglas"
                                                  :disabled="cargando"
                                             >
                                                  <template v-slot:append>
                                                       <v-icon
                                                            color="primary"
                                                            class="mr-2"
                                                            @click="obtenerDataDocumentos(doc.tipo_documento)"
                                                            :disabled="cargando || busquedaDesactivada"
                                                       >
                                                            mdi-magnify
                                                       </v-icon>
                                                  </template>
                                             </v-text-field>
                                        </v-col>
                                        <v-col cols="6">
                                             <v-text-field
                                                  v-model="nuevoCliente.nombre"
                                                  label="Nombre"
                                                  :rules="nombreReglas"
                                                  :disabled="cargando"
                                                  type="text"
                                                  autocomplete="name"
                                             />
                                        </v-col>
                                        <v-col cols="6">
                                             <v-text-field
                                                  v-model="nuevoCliente.direccion"
                                                  label="Dirección"
                                                  :rules="direccionReglas"
                                                  :disabled="cargando"
                                                  type="text"
                                                  autocomplete="address"
                                             />
                                        </v-col>
                                        <v-col cols="6">
                                             <v-text-field
                                                  v-model="nuevoCliente.email"
                                                  label="Correo Electrónico"
                                                  :disabled="cargando"
                                                  type="text"
                                                  autocomplete="email"
                                             />
                                        </v-col>
                                        <v-col cols="6">
                                             <v-text-field
                                                  v-model="nuevoCliente.telefono"
                                                  label="Telefono"
                                                  :disabled="cargando"
                                                  type="number"
                                                  autocomplete="tel"
                                             />
                                        </v-col>
                                   </v-row>
                              </template>
                              <template v-else-if="dialogModo === 1">
                                   <!-- NOMBRE -->
                                   <v-row dense>
                                        <v-text-field
                                             v-model="nuevaHabitacion.nombre_habitacion"
                                             label="Nombre"
                                             :disabled="cargando"
                                             :rules="nombreReglas"
                                             type="text"
                                             autocomplete="name"
                                             required
                                        />
                                   </v-row>
                                   <!-- ZONA -->
                                   <v-row dense>
                                        <v-select
                                             :items="zonas"
                                             :rules="zonaReglas"
                                             :value="zona.id_zona"
                                             :disabled="cargando"
                                             @change="cambioZona"
                                             v-model="zona"
                                             label="Zona"
                                             item-value="id_zona"
                                             item-text="nombre_zona"
                                             required
                                             return-object
                                        />
                                   </v-row>
                                   <!-- CATEGORIA -->
                                   <!--<v-row dense>
                                        <v-select
                                             :items="categorias"
                                             :rules="categoriaReglas"
                                             :value="categoria.id_categoria"
                                             :disabled="cargando"
                                             @change="cambioCategoria"
                                             v-model="categoria"
                                             label="Categoria"
                                             item-value="id_categoria"
                                             item-text="nombre_categoria"
                                             required
                                             return-object
                                        />
                                   </v-row>-->
                                   <!-- DETALLE -->
                                   <v-row dense>
                                        <v-text-field
                                             v-model="nuevaHabitacion.detalle"
                                             label="Detalle"
                                             :disabled="cargando"
                                             :rules="detalleReglas"
                                             type="text"
                                             required
                                        />
                                   </v-row>
                                   <!-- COSTO -->
                                   <!--<v-row dense>
                                        <v-text-field
                                             v-model="nuevaHabitacion.costo"
                                             label="Costo"
                                             :disabled="cargando"
                                             :rules="costoReglas"
                                             prepend-inner-icon="mdi-currency-usd"
                                             type="number"
                                             required
                                             @blur="precioBlur"
                                        />
                                   </v-row>-->
                              </template>
                         </v-form>
                    </v-container>

                    <v-card-actions>
                         <v-spacer />
                         <v-btn  :disabled="cargando" text @click="crearDialogo = false">Cancelar</v-btn>
                         <v-btn  :disabled="cargando" @click="guardar" color="primary">Crear</v-btn>
                    </v-card-actions>
               </v-card>
          </v-dialog>
     </v-dialog>
</template>

<script>
import moment from 'moment'
export default {
     props: {
          cargando: { type: Boolean },
          abierto: { type: Boolean },
          dialog: { type: Object },
          cancelar: { type: Function },
          aceptar: { type: Function },
          zonas: { type: Array },
          categorias: { type: Array },
          parametrosAPI: { type: Object },
          encapsularLlamada: { type: Function },
          activarSnackbar: { type: Function },
          recargar: { type: Function },
          mostrarZona: { type: Function },
          mostrarCategoria: { type: Function },
     },
     data() {
          return {
               formulario: false,
               parametros: {
                    id_reservas: 0,
                    id_cliente: 0,
                    nombre: '',
                    fecha_alojamiento: '2021-03-19',
                    dias_alojamiento: 0,
                    habitaciones: [],
                    costo_total: 0,
               },
               fechas_reservadas: [],
               habitaciones_disponibles: [],
               habitaciones_seleccionadas: [],
               reservaStep: 1,

               dialogModo: -1,
               buscarDialogo: false,
               crearDialogo: false,
               singleSelect: true,
               showSelect: true,
               itemKey: '',
               cabecerasCliente: [
                    { text: 'ID', value: 'id_cliente', sortable: false },
                    { text: 'Nombre', value: 'nombre', sortable: false },
                    { text: 'Correo Electrónico', value: 'email', sortable: false },
                    { text: 'Nro. Documento', value: 'nro_doc', sortable: false },
               ],
               cabecerasHabitacion: [
                    { text: 'Nombre', value: 'nombre_habitacion', sortable: false },
                    { text: 'Zona', value: 'id_zona', sortable: false },
                    //{ text: 'Categoria', value: 'id_categoria', sortable: false },
                    //{ text: 'Costo', value: 'costo', sortable: false },
                    { text: 'Disponibilidad', value: 'booking', sortable: false },
               ],
               apisDisponibles: {
                    clients: () => '/api/getClientes',
                    pclients: () => '/api/customer',
                    rooms: () => '/api/rooms',
                    docs: () => '/api/getTiposDoc',
                    available: () => '/api/availableRooms',
                    dni: (doc) => `http://bytesoluciones.com/apidnix/apidni.php?dni=${doc}`,
                    ruc: (doc) => `http://144.217.215.6/sunat/libre.php?ruc=${doc}`,
               },
               cabeceras: [],
               items: [],
               opciones: { itemsPerPage: 5 },
               total: 0,
               clienteSeleccionado: {},
               habitacionSeleccionada: {},
               delimitador: { from: '', to: '' },
               tipoHabitacion: -1,
               busquedaDesactivada: false,
               doc: { tipo_documento: '', id_tipo_doc: 0, codigo_sunat: '0', caracteres: 0 },
               docs: [],
               zona: { id_zona: 0, nombre_zona: '' },
               categoria: { id_categoria: 0, nombre_categoria: '' },

               docReglas: [({ tipo_documento }) => !!tipo_documento || 'Selecciona un tipo de documento'],
               nrodocReglas: [
                    (v) => !!v || `El campo Nro. ${this.doc.tipo_documento} es obligatorio`,
                    (v) => {
                         const validate = () => new RegExp('^\\d{' + this.doc.caracteres + '}?$').test(v)
                         if (validate()) this.busquedaDesactivada = false
                         else this.busquedaDesactivada = true
                         return validate() || `${this.doc.tipo_documento} invalido`
                    },
               ],
               nombreReglas: [(v) => !!v || 'El campo Nombres es obligatorio'],
               correoReglas: [
                    (v) => !!v || 'El campo Correo es obligatorio',
                    (v) => /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(v) || 'Correo invalido',
               ],
               direccionReglas: [
                    (v) => (this.doc.tipo_documento === 'RUC' ? !!v : true) || 'El campo Dirección es obligatorio',
               ],

               nombreReglas: [(v) => !!v || 'Campo Nombre obligatorio'],
               zonaReglas: [({ nombre_zona }) => !!nombre_zona || 'Selecciona una zona'],
               categoriaReglas: [({ nombre_categoria }) => !!nombre_categoria || 'Selecciona una categoria'],
               detalleReglas: [(v) => !!v || 'Campo Detalle obligatorio'],
               costoReglas: [(v) => !!v || 'Campo Costo obligatorio', (v) => parseInt(v) > 0 || 'Costo muy bajo'],
               nuevoCliente: {
                    id_cliente: 0,
                    id_tipo_doc: 0,
                    nombre: '',
                    nro_doc: 0,
                    direccion: '',
                    email: '',
                    telefono: 0,
               },
               nuevaHabitacion: {
                    id_habitacion: 0,
                    nombre_habitacion: '',
                    id_zona: 1,
                    id_categoria: 1,
                    detalle: '',
                    costo: 0.0,
               },
               costoTotal: 0.0,

               categoria_habitacion_reserva:{
                    precio: 0.00
               }
          }
     },
     computed: {
          step1() {
               return Object.keys(this.clienteSeleccionado).length > 0
          },
          step2() {
               return this.fechas_reservadas.length === 2
          },
          step3() {
               return this.habitaciones_seleccionadas.length > 0
          },
          step4() {
               return this.dialog.modo === 0 ? this.reservaStep < 4 : false
          },
          roomdenom() {
               return (habitacion) =>
                    `Habitación: ${habitacion.nombre_habitacion},
                Zona: ${habitacion.zone.nombre_zona}, Detalle: ${habitacion.detalle}`
               // Categoria: ${habitacion.id_categoria},
               // Costo: ${habitacion.costo}`
          },
          minimumDate() {
               return moment().format('YYYY-MM-DD')
          },
          tabfromDisponibles() {
               return `Disponibles ${moment(this.delimitador.from).format('D MMMM')}-${moment(
                    this.delimitador.to,
               ).format('D MMMM')}`
          },
          fechaDisponible() {
               return (booking) => {
                    if (booking) {
                         let fecha = booking.fecha_alojamiento.split('-')
                         moment.locale('es')
                         let fechaFinal = moment([fecha[0], parseInt(fecha[1]) - 1, fecha[2]])
                              .add(parseInt(booking.dias_alojamiento) + 1, 'days')
                              .format('MMMM, D')
                         return `Disponible desde ${fechaFinal}`
                    } else return 'Disponible ahora'
               }
          },
     },
     watch: {
          dialog: {
               handler() {
                    this.parametros = this.parametrosAPI
                    if (this.dialog.modo === 0) {
                         // console.log('uwu');
                         this.resetear()
                    }
                    if (this.dialog.modo === 1) {
                         console.log(this.parametros)
                         this.clienteSeleccionado = this.parametros.cliente
                         this.parametros.nombre = this.parametros.cliente.nombre
                         let fecha = this.parametros.fecha_alojamiento.split('-')
                         let fechaFinal = moment([fecha[0], parseInt(fecha[1]) - 1, fecha[2]])
                              .add(parseInt(this.parametros.dias_alojamiento), 'days')
                              .format('YYYY-MM-DD')
                    
                         this.fechas_reservadas = [fecha.join('-'), fechaFinal]
                         this.categoria_habitacion_reserva = this.parametros.habitaciones[0].categoria

                         this.habitaciones_disponibles = [this.parametros.habitaciones[0].habitaciones]
                         this.habitaciones_seleccionadas = [this.parametros.habitaciones[0].habitaciones]

                         // 
                         // console.log(this.parametros.dias_alojamiento);
                         // this.calcularCosto()
                    }
               },
               deep: true,
          },
          opciones: {
               handler() {
                    console.log(this.opciones)
                    var api, metodo, data, additional
                    if (this.dialogModo === 0) {
                         api = this.apisDisponibles.clients()
                         metodo = 'post'
                         data = { perpage: this.opciones.itemsPerPage }
                         additional = { page: this.opciones.page }
                    } else if (this.dialogModo === 1) {
                         api = this.apisDisponibles[this.tipoHabitacion === -1 ? 'available' : 'rooms']()
                         metodo = 'get'
                         additional = {
                              rows: this.opciones.itemsPerPage,
                              page: this.opciones.page,
                         }
                         if (this.tipoHabitacion === -1) {
                              additional['from'] = this.delimitador.from
                              additional['to'] = this.delimitador.to
                         } else {
                              additional['where'] = 'id_reservas'
                              additional['like'] = this.tipoHabitacion
                         }
                    }
                    this.encapsularLlamada(
                         {
                              api,
                              metodo,
                              data,
                              additional,
                         },
                         (response) => {
                              this.items = response.data
                              this.total = response.total
                         },
                    )
               },
               deep: true,
          },
          fechas_reservadas(p, n) {
               
               if (Array.from(p).length === 2) {
                    // let dias = parseInt(p[1].split('-')[2]) - parseInt(p[0].split('-')[2])
                    console.log(p[1]);
                    let dias = moment(p[1]).diff(moment(p[0]), 'days')
                    // console.log(dias)
                    let delimitador = {}
                    if (dias < 0) {
                         this.parametros.fecha_alojamiento = p[1]
                         this.parametros.fecha_salida_estimado = p[1]
                         delimitador['from'] = p[1]
                         delimitador['to'] = p[0]
                    } else {
                         this.parametros.fecha_alojamiento = p[0]
                         this.parametros.fecha_salida_estimado = p[1]
                         delimitador['from'] = p[0]
                         delimitador['to'] = p[1]
                    }
                    this.delimitador = delimitador
                    // this.parametros.fecha_alojamiento = dias < 0 ? p[1] : p[0]
                    this.parametros.dias_alojamiento = dias < 0 ? dias * -1 : dias
                    if (this.habitaciones_seleccionadas.length > 0) this.calcularCosto()
               } else {
                    this.parametros.fecha_alojamiento = moment().format('YYYY-MM-DD')
                    this.parametros.dias_alojamiento = 0
               }
          },
          habitaciones_seleccionadas(p) {
               this.parametros.habitaciones = p
               this.calcularCosto()
          },
          tipoHabitacion(p) {
               this.items = []
               this.manejarHabitacionesReservada(p)
               if (p === -1) this.showSelect = true
               else this.showSelect = false
          },
     },

     methods: {
          getItemText(item) {
               return `${item.nombre_categoria} - S/.${item.precio}`;
          },
          changeCategoria(){
               this.parametros.categoria = this.categoria_habitacion_reserva;
               this.calcularCosto();
          },
          /*async getCategorias(){
               try{
                    const data = await API.typeroom.list();
                    this.categorias = data.data;
                    this.$store.commit('LOADER',false);
               }catch(error){
                    console.error(error);
                    this.$store.commit('LOADER',false);
               }
          },*/
          resetear() {
               this.zona = { id_zona: 0, nombre_zona: '' }
               this.categoria = { id_categoria: 0, nombre_categoria: '' }
               this.doc = { tipo_documento: '', id_tipo_doc: 0, codigo_sunat: '0', caracteres: 0 }
               this.parametros = {
                    id_reservas: 0,
                    id_cliente: 0,
                    nombre_cliente: '',
                    fecha_alojamiento: '2021-03-19',
                    dias_alojamiento: 0,
                    habitaciones: [],
                    costo_total: 0,
               }
               this.formulario = false
               this.fechas_reservadas = []
               this.habitaciones_disponibles = []
               this.habitaciones_seleccionadas = []
               this.reservaStep = 1
               this.nuevoCliente = {
                    id_cliente: 0,
                    id_tipo_doc: 0,
                    nombre: '',
                    nro_doc: 0,
                    direccion: '',
                    email: '',
                    telefono: 0,
               }
               this.nuevaHabitacion = {
                    id_habitacion: 0,
                    nombre_habitacion: '',
                    id_zona: 1,
                    id_categoria: 1,
                    detalle: '',
                    costo: 0.0,
               }
               this.clienteSeleccionado = {}
               this.habitacionSeleccionada = {}
          },
          cerrar() {
               this.cancelar()
               this.resetear()
          },
          salvar() {
               if (this.dialog.modo === 0 || this.dialog.modo === 1) {
                    console.log(this.parametros)
               }
               this.aceptar(this.dialog.modo, this.parametros)
          },
          obtenerDataDocumentos(doc) {
               if (doc != '') {
                    const api =
                         doc === 'DNI'
                              ? this.apisDisponibles.dni(this.nuevoCliente.nro_doc)
                              : this.apisDisponibles.ruc(this.nuevoCliente.nro_doc)
                    this.encapsularLlamada({ api, metodo: 'get', noauth: true }, (response) => {
                         console.log(response)
                         if (doc === 'DNI') {
                              const data = response.result
                              const { Apellidos, Nombres } = data
                              if (Apellidos && Nombres) {
                                   this.nuevoCliente.nombre = `${Nombres} ${Apellidos}`
                                   this.nuevoCliente.direccion = ''
                                   this.parametros.nombre = this.nuevoCliente.nombre
                              } else {
                                   this.activarSnackbar(true, '¡Un error sucedio en el servidor!', 'warning')
                                   return
                              }
                         } else {
                              const data = response
                              const { nombre_o_razon_social, direccion } = data
                              if (nombre_o_razon_social && direccion) {
                                   this.nuevoCliente.nombre = nombre_o_razon_social
                                   this.nuevoCliente.direccion = direccion
                                   this.parametros.nombre = this.nuevoCliente.nombre
                              } else {
                                   this.activarSnackbar(true, '¡Un error sucedio en el servidor!', 'warning')
                                   return
                              }
                         }
                         this.activarSnackbar(true, '¡Datos extraidos exitosamente!', 'success')
                    })
               }
          },
          cambioZona({ id_zona }) {
               this.nuevaHabitacion.id_zona = id_zona
          },
          cambioCategoria({ id_categoria }) {
               this.nuevaHabitacion.id_categoria = id_categoria
          },
          precioBlur(value) {
               // console.log(value.srcElement.value)
               if (value.srcElement.value) this.nuevaHabitacion.costo = parseFloat(value.srcElement.value).toFixed(2)
               else this.nuevaHabitacion.costo = 0.0
          },
          recargarBuscar() {
               const api = this.apisDisponibles[this.dialogModo === 0 ? 'clients' : 'rooms']()
               const metodo = this.dialogModo === 0 ? 'post' : 'get'
               this.encapsularLlamada({ api, metodo, data: {}, additional: {} }, (response) => {
                    console.log(response)
               })
          },
          async manejarHabitacionesReservada(tipoHabitacion) {
               let api = this.apisDisponibles[tipoHabitacion === -1 ? 'available' : 'rooms']()
               this.opciones.page = 1
               let additional = {
                    rows: this.opciones.itemsPerPage,
                    page: this.opciones.page,
               }
               if (tipoHabitacion === -1) {
                    additional['from'] = this.delimitador.from
                    additional['to'] = this.delimitador.to
               } else {
                    additional['where'] = 'id_reservas'
                    additional['like'] = tipoHabitacion
               }
               await this.encapsularLlamada(
                    {
                         api,
                         metodo: 'get',
                         additional,
                    },
                    (response) => {
                         this.items = response.data
                         this.total = response.total
                         // console.log(response)
                    },
               )
          },
          async abrirBuscarDialogo(modo) {
               this.dialogModo = modo
               if (modo === 0) {
                    this.cabeceras = this.cabecerasCliente
                    this.itemKey = 'id_cliente'
                    this.singleSelect = true
                    this.showSelect = true
                    await this.encapsularLlamada(
                         { api: this.apisDisponibles.clients(), metodo: 'post', data: { perpage: 5 } },
                         (response) => {
                              this.items = response.data
                              this.total = response.total
                              // console.log(response)
                         },
                    )
               } else {
                    this.singleSelect = true;//false
                    this.itemKey = 'id_habitacion'
                    this.cabeceras = this.cabecerasHabitacion
                    this.manejarHabitacionesReservada(this.tipoHabitacion)
               }
               this.buscarDialogo = true
          },
          async abrirCrearDialogo(modo) {
               this.dialogModo = modo
               if (modo === 0) {
                    await this.encapsularLlamada({ api: this.apisDisponibles.docs(), metodo: 'get' }, (response) => {
                         this.docs = response
                    })
               }
               this.crearDialogo = true
          },
          cerrarDialogo() {
               this.dialogModo = -1
               this.tipoHabitacion = -1
               this.items = []
               this.cabeceras = []
               // this.opciones = { itemsPerPage: 5 }
               this.total = 0
               this.itemKey = ''
               this.nuevoCliente = {
                    id_cliente: 0,
                    id_tipo_doc: 0,
                    nombre: '',
                    nro_doc: 0,
                    direccion: '',
                    email: '',
                    telefono: 0,
               }
               this.nuevaHabitacion = {
                    id_habitacion: 0,
                    nombre_habitacion: '',
                    id_zona: 1,
                    id_categoria: 1,
                    detalle: '',
                    costo: 0.0,
               }
          },
          seleccionarFila(row) {
               console.log(row)
               if (this.tipoHabitacion != 'filled') {
                    this.habitaciones_disponibles = [];
                    this.habitaciones_seleccionadas = [];
                    if (row.value) {
                         if (this.dialogModo === 0) {
                              this.clienteSeleccionado = row.item
                              this.parametros.id_cliente = row.item.id_cliente
                              this.parametros.nombre = row.item.nombre
                         } else if (this.dialogModo === 1) {
                              this.habitacionSeleccionada = row.item
                              this.habitaciones_disponibles.push(row.item);
                              this.habitaciones_seleccionadas.push(row.item);
                         }
                    } else {
                         if (this.dialogModo === 0) {
                              this.clienteSeleccionado = {}
                              this.parametros.id_cliente = 0
                              this.parametros.nombre = ''
                         } else if (this.dialogModo === 1) {
                              this.habitacionSeleccionada = {}
                              let find = this.habitaciones_disponibles.findIndex(
                                   (room) => room.id_habitacion === row.item.id_habitacion,
                              )
                              if (find != -1) this.habitaciones_disponibles.splice(find, 1)
                         }
                    }
               }
          },
          clickearFila(row, other) {
               other.select(!other.isSelected)
          },
          cambiarDocType({ id_tipo_doc }) {
               this.nuevoCliente.id_tipo_doc = id_tipo_doc
          },
          guardar() {
               if (this.$refs.form.validate()) {
                    if (this.dialogModo === 0) {
                         this.encapsularLlamada(
                              {
                                   api: this.apisDisponibles.pclients(),
                                   metodo: 'post',
                                   data: this.nuevoCliente,
                              },
                              (response) => {
                                   this.clienteSeleccionado = response
                                   this.crearDialogo = false
                                   this.parametros.id_cliente = this.clienteSeleccionado.id_cliente
                                   this.parametros.nombre_cliente = this.clienteSeleccionado.nombre_cliente
                                   this.activarSnackbar(true, '¡Cliente creado exitosamente!', 'success')
                                   // console.log(response)
                              },
                         )
                    } else if (this.dialogModo === 1) {
                         this.encapsularLlamada(
                              { api: this.apisDisponibles.rooms(), metodo: 'post', data: this.nuevaHabitacion },
                              (response) => {
                                   // console.log(response)'
                                   this.habitacionSeleccionada = response.room
                                   this.crearDialogo = false
                                   this.habitaciones_disponibles.push(response.room)
                                   this.activarSnackbar(true, '¡Habitacion creada exitosamente!', 'success')
                              },
                         )
                    }
               }
          },
          calcularCosto() {
               // let costo = Array.from(this.parametros.habitaciones)
               //      .map((room) => room.costo)
               //      .reduce((total, costo) => total + parseFloat(costo), 0);
               let costo = this.categoria_habitacion_reserva.precio;
               this.costoTotal = parseFloat(parseFloat(costo) * this.parametros.dias_alojamiento).toFixed(2)
               this.parametros.costo_total = this.costoTotal
          },
     },
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