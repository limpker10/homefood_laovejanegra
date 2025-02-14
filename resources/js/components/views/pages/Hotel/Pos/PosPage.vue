<template>
    <div >
        <v-container>
            <div class="d-flex align-center py-3" style="padding: 10px;">
            <div>
                <h2 style="color:  #AD91FD">Agregar nueva venta</h2>
            </div>
            <v-spacer></v-spacer>
            <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on" :to="'/rooms_sales/'"> 
                    <v-icon>mdi-room-service-outline</v-icon>
                    </v-btn>
                </template>
                Volver a Recepción
                </v-tooltip>
            </div>

            <v-row no-gutters >
                 <v-col 
                cols="12"
                style="padding: 0 10px;"
                >
                    <v-card >
                        <v-list-item three-line>
          
                            <v-list-item-content>
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

                <v-col cols="12" sm="7" md="7"
                style="padding: 10px;">
                    
                    <v-dialog v-model="observationDialog" max-width="300px">
                        <v-card>
                        <v-card-title class="subtitle-1">Comentario / Observaciones</v-card-title>
                        <v-card-text>
                        <v-textarea  v-if="form.orden.detalle.length>0"
                            name="input-7-1"
                            rows="2"
                            label="Comentario / Observación"
                            v-model="form.orden.detalle[indxobservation].observaciones"
                        ></v-textarea>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="error" dark @click="observationDialog=false"
                            >Cancelar</v-btn
                            >
                            <v-btn color="primary" @click="updateObservation"
                            >Guardar</v-btn
                            >
                            <v-spacer></v-spacer>
                        </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <v-card style="height:350px; overflow-y: auto;">
                        <v-flex column py-0>
                            <v-card flat style="height:100%;margin-top: 10px;">
                                
                                <v-simple-table>
                                    <template v-slot:default>
                                        <thead>
                                            <tr>
                                                <th style="width:12%;" class="text-left"></th>
                                                <th class="text-left">Producto</th>
                                                <th style="width:25%;" class="text-left">CNT</th>
                                                <th style="width:15%;" class="text-left">P.U (S/.)</th>
                                                <th style="width:15%;" class="text-left">P.T (S/.)</th>
                                            </tr>
                                        </thead>
                                        <tbody>  
                                            <tr
                                            v-for="(item,index) in form.orden.detalle"
                                            :key="index"
                                            >
                                                <td class="px-2">
                                                        <v-icon  @click="deleteItem(index)"> mdi-delete</v-icon>
                                                        <v-icon  @click="editObservation(index)"> mdi-comment-edit</v-icon>
                                                </td>
                                                <td class="px-1">
                                                    {{ item.nombre_producto }}</td>
                                                <td> 
                                                    <v-row no-gutters>
                                                        <v-col
                                                            cols="12"
                                                            sm="4"
                                                        >
                                                            <v-btn @click="removeItem(index)"
                                                                icon
                                                                color="primary"
                                                                >
                                                                <v-icon>mdi-minus-box</v-icon>
                                                            </v-btn>
                                                        </v-col>
                                                        <v-col
                                                            cols="12"
                                                            sm="4"
                                                        >
                                                            <v-text-field
                                                                style="padding: 0px; margin: auto; text-align: center;"
                                                                v-on:keyup="calcularTotalFila(index)"
                                                                v-model="item.cantidad"
                                                                hide-details="auto"
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col
                                                            cols="12"
                                                            sm="4"
                                                        >
                                                            <v-btn @click="addItem(index)"
                                                                icon
                                                                color="primary"
                                                                >
                                                                <v-icon>mdi-plus-box</v-icon>
                                                            </v-btn>
                                                        </v-col>
                                                    </v-row>
                                                </td>
                                                <td class="px-1">
                                                    <v-text-field style="padding: 0px; margin: auto;"
                                                        v-on:keyup="calcularTotalFila(index)"
                                                        v-model="item.precio"
                                                        prefix="S/."
                                                        hide-details="auto"
                                                    ></v-text-field>
                                                </td>
                                                <td class="px-1">
                                                    <v-text-field style="padding: 0px; margin: auto;"
                                                        v-model="item.total"
                                                        readonly
                                                        prefix="S/."
                                                        hide-details="auto"
                                                    ></v-text-field>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </template>
                                </v-simple-table>
                                <div  v-if="form.orden.detalle.length==0" style="text-align: center;height: 250px;display: flex;align-items: center;">
                                    <h2 style="margin:auto; color: rgba(0,0,0,.38);">Productos no seleccionados</h2>
                                </div>
                            </v-card>
                        </v-flex>
                    </v-card>
                </v-col>

                <!--ITEMS-->
                <v-col  cols="12" sm="5" md="5"
                style="padding: 10px;">
                    
                    <v-card style="height:350px; overflow-y: auto;">
                        <v-sheet
                            class="mx-auto"
                            max-width="1250"
                        >
                            <v-slide-group
                            show-arrows
                            >
                            <v-slide-item
                                v-for="(n,index) in categorias"
                                :key="index"
                                v-slot="{ active, toggle }"
                            >
                                <v-card
                                class="ma-1"
                                :color="active ? 'black' : n.color"
                                height="50"
                                width="120"
                                @click="toggle(); getProductos(n.id_categoria_sucursal)"
                                >
                                
                                    <v-card-title class="white--text subtitle-2" v-text="n.categoria.nombre"></v-card-title>

                                </v-card>
                            </v-slide-item>
                            </v-slide-group>
                        </v-sheet>
                        <v-divider></v-divider>
                        <v-container fluid>
                            <v-row dense>
                            <v-col
                                v-for="(card,index) in productos"
                                :key="index"
                                cols="3"
                            >
                                <v-card @click="addItemOrder(card)">
                                    <v-img
                                        :src="'../storage/files/uploads/'+card.path+'/'+card.filename"
                                        class="white--text align-end"
                                        gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.8)"
                                        height="130px"
                                    >
                                        <v-card-title class="body-2" v-text="card.nombre_producto"></v-card-title>
                                    </v-img>
                                </v-card>
                            </v-col>
                            </v-row>
                        </v-container>
                    </v-card>
                </v-col>
                <v-col cols="12" style="padding: 0 10px;">
                    <v-card tile >
                        <v-card
                        outlined
                        flat
                        tile
                        class="pa-2 d-flex justify-end mb-4"
                        >
                            <div style="width: 250px;">
                                <v-radio-group v-model="tipo_pago">
                                    <v-radio label="Pagar ahora"
                                        value="1"
                                    ></v-radio>
                                    <v-radio label="Pagar despúes"
                                        value="0"
                                    ></v-radio>
                                </v-radio-group>
                            </div>
                            <v-simple-table>
                                <template v-slot:default>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" style="text-align:right;"><b>Subtotal: </b></td>
                                            <td colspan="1">
                                                <v-text-field
                                                    v-model="form.orden.subtotal"
                                                    prefix="S/."
                                                    readonly
                                                    hide-details="auto"
                                                ></v-text-field>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="text-align:right;"><b>IGV (18%): </b></td>
                                            <td colspan="1">
                                                <v-text-field
                                                    v-model="form.orden.igv"
                                                    prefix="S/."
                                                    readonly
                                                    hide-details="auto"
                                                ></v-text-field>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="text-align:right;"><b>Total: </b></td>
                                            <td colspan="1">
                                                <v-text-field
                                                    v-model="form.orden.total"
                                                    prefix="S/."
                                                    readonly
                                                    hide-details="auto"
                                                ></v-text-field>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </template>
                            </v-simple-table>
                        </v-card>
                        <v-card-actions class="justify-end" >
                            <v-spacer></v-spacer>
                            <v-btn color="error" to="/rooms_sales">
                                <v-icon>mdi-close</v-icon> Cancelar
                            </v-btn>
                            <v-btn color="warning" @click="printCocina">
                                <v-icon>mdi-chef-hat</v-icon> Enviar Cocina</v-btn>
                            <v-btn color="success" @click="preLoadData">
                                <v-icon>mdi-printer-pos</v-icon> Procesar Venta
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
            
            <v-dialog v-model="comprobanteDialog" max-width="500px" persistent>
                <v-card>
                    <v-card-title class="subtitle-1">Procesar Venta</v-card-title>
                    <v-card-text>
                        <v-form
                            ref="form"
                            v-model="valid2"
                            lazy-validation
                        >
                            <v-row dense>
                                <v-col cols="6" >
                                    <v-select
                                        :items="tipo_comprobante"
                                        label="Comprobante"
                                        placeholder="Selecciona un tipo de comprobante"
                                        item-text="tipo_comprobante"
                                        item-value="id_tipo_comprobante"
                                        v-model="generate_sales.header.id_tipo_comprobante"
                                        hide-details="auto"
                                        @change="getserie"
                                        :rules="requiredRules"
                                    ></v-select>
                                </v-col>
                                <v-col cols="6" >
                                    <v-select
                                        :items="serie"
                                        label="Serie"
                                        item-text="serie"
                                        item-value="id_serie"
                                        v-model="generate_sales.header.id_serie"
                                        hide-details="auto"
                                        :rules="requiredRules"
                                    ></v-select>
                                </v-col>
                            </v-row>

                            <v-text-field
                                label="Cliente"
                                hide-details="auto"
                                readonly
                                v-model="generate_sales.header.nombre_cliente"
                                :rules="requiredRules"
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
                                    <tr v-for="(item,index) in form.orden.detalle" :key="index">
                                        <td>{{item.nombre_producto}}</td>
                                        <td>{{item.cantidad}}</td>
                                        <td>{{item.precio}}</td>
                                        <td>{{item.total}}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" style="text-align:right;"><b>Subtotal: </b></td>
                                        <td colspan="1">S/. {{form.orden.subtotal}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="text-align:right;"><b>IGV (18%): </b></td>
                                        <td colspan="1">S/. {{form.orden.igv}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="text-align:right;"><b>Total: </b></td>
                                        <td colspan="1">S/. {{form.orden.total}}</td>
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
                                v-model="generate_sales.header.id_medio_pago"
                                hide-details="auto"
                                :rules="requiredRules"
                            ></v-select>
                            <v-select
                                :items="monedas"
                                label="Moneda"
                                placeholder="Seleccione moneda"
                                item-text="nombre"
                                item-value="id_moneda"
                                v-model="generate_sales.header.id_moneda"
                                hide-details="auto"
                                :rules="requiredRules"
                            ></v-select>
                            <v-text-field
                                v-if="generate_sales.header.id_medio_pago==3"
                                label="Nro. Operación"
                                hide-details="auto"
                                v-model="generate_sales.header.nro_operacion"
                            ></v-text-field>
                            <v-textarea
                                name="input-7-1"
                                rows="2"
                                label="Comentario"
                                v-model="generate_sales.header.comentario"
                                hide-details="auto"
                            ></v-textarea>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="error" dark @click="close"
                        >Cancelar</v-btn
                        >
                        <v-btn color="primary" :loading="loading" @click="saveComprobante"
                        >Guardar</v-btn
                        >
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="dialogDelete" max-width="400px">
                <v-card>
                    <v-card-title class="subtitle-1">¿Desea cancelar esta Orden?</v-card-title>
                    <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="error" dark @click="dialogDelete=false">Cancelar</v-btn>
                    <v-btn color="primary" @click="deleteOrdenConfirm">Aceptar</v-btn>
                    <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="dialogPayment" max-width="400px">
                <v-card>
                    <v-card-title class="subtitle-1">Esta Venta se cobrará al momento de la salida del alojamiento como Consumo, ¿Esta seguro que desea continuar?</v-card-title>
                    <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="error" dark @click="dialogPayment=false">Cancelar</v-btn>
                    <v-btn color="primary" @click="deleteOrdenConfirm">Aceptar</v-btn>
                    <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="nuevoClienteDialog" max-width="600px" >
            <v-card>
                <v-card-title>
                <span class="headline">Agregar Nuevo Cliente</span>
                </v-card-title>
                <v-card-text>
                    <v-form
                        ref="formCliente"
                        v-model="valid"
                        lazy-validation
                    >
                        <v-container>
                            <v-row>
                            <v-col cols="12" sm="6"  md="6" >
                                <v-select :items="tipos_doc" label="Tipo Documento" placeholder="Selecciona un tipo de Documento" v-model="nuevoCliente.id_tipo_doc" required item-text="tipo_documento" item-value="id_tipo_doc"></v-select>
                                <v-text-field  v-model="nuevoCliente.nombre" label="Nombre / Razón Social Cliente" placeholder="Nombre / Razón Social Cliente" :rules="requiredRules"
                                ></v-text-field>
                            <v-text-field v-model="nuevoCliente.email" label="Correo Electrónico" placeholder="Correo Electrónico"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6"  md="6" ><!--v-on:keyup.enter="buscarDoc()"-->
                                <v-text-field  v-model="nuevoCliente.nro_doc" label="Nro. Documento" placeholder="Nro. Documento" :rules="requiredRules" :append-outer-icon="iconSearch" @click:append-outer="SearchApi()"></v-text-field>
                                <v-text-field v-model="nuevoCliente.direccion" label="Dirección" placeholder="Dirección" :rules="requiredRules"
                                ></v-text-field>
                                <v-text-field v-model="nuevoCliente.telefono" label="Teléfono" placeholder="Teléfono"
                                ></v-text-field>
                            </v-col>
                            </v-row>
                        </v-container>
                    </v-form>
                </v-card-text>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error"  >Cancelar</v-btn>
                <v-btn color="primary" :disabled="!valid" @click="guardarCliente()"> Guardar </v-btn>
                </v-card-actions>
            </v-card>
            </v-dialog>
        </v-container>
    </div>
</template>
 
<script>
export default {
  components: {
  },
  data: () => ({
        ordenLoading:false,
        tab:0,
        tabs: [1],
        active_tab: null,
        mesa:{zona:{nombre:''}},
        breadcrumbs: [{
                text: 'Mesas',
                disabled: true,
                //to: '/tablesSystem/'+this.
            },
            {
                text: 'Ordenes Mesa',
                disabled: true,
            }
        ],
        valid: true,
        valid2: true,
        form:{
            header:{ fecha_emision: new Date().toISOString().substr(0, 10), },
            detail:[],
            orden:{
                igv: 0.00,
                subtotal: 0.00,
                total: 0.00,
                detalle:[]}
        },
        tabs_form: [
        ],
        rules: {
          required: value => !!value || 'Campo Requerido.',
        },
        descriptionLimit: 60,
        entries: [],
        isLoading: false,
        search_provider: null,
        categorias: [
            {"id_categoria_sucursal":0,"id_categoria":0,"color":"#ffca40","tipo_producto_insumo":1,"categoria":{"id_categoria":0,"nombre":"TODOS","color":"#ffca40"}}
        ],
        productos: [],
        search_productos: null,
        get_producto: {
            unidad_medida: {}
        },
        entries_productos: [],


        descriptionLimit: 60,
        entries: [],
        isLoading: false,
        search_provider: null,
        get_customer: {},

        id_mesa:null,
        customers:[],
        observationDialog:false,
        indxobservation:0,

        comprobanteDialog:false,
        metodospago:[],
        monedas:[],
        tipo_comprobante:[],
        serie:[],

        generate_sales:{
            header:{},
            detail:[]
        },

        verComprobanteDialog:false,
        urlComprobante:'',

        dialogDelete:false,
        deleteOrder:0,

        nuevoClienteDialog: false,
        tipos_doc: [],
        nuevoCliente: {
            id_tipo_doc: '',
            nombre:'',
            nro_doc:'',
            email: '',
            direccion: '',
            telefono: '',
        },
        requiredRules: [
            v => !!v || 'Campo obligatorio',
        ],
        emailRules: [
            v => !!v || 'Campo obligatorio',
            v => /.+@.+\..+/.test(v) || 'Correo Electrónico debe ser válido',
        ],
        iconSearch:"mdi-magnify",
        datosBus: {
            tipoDoc: '',
            numDoc: ''
        },
        id_mozo_sel: null,
        mozos: [],
        nombre_mozo: '',



        room:{ category:{},zone:{}},
        booking:{ cliente:{}},
        customer:{},
        idRoom:'',
        tipo_pago:"1",
        dialogPayment: false,
        loading: false,
  }),

 computed: {
    items() {
      return this.entries.map((entry) => {
        const Description = entry.nombre;
        return Object.assign({}, entry, { Description });
      });
    },

  },

  watch: {
    search_provider(val) {
      if (this.items.length > 0) return;
      if (this.isLoading) return;
      this.isLoading = true;
      fetch("/api/searchCustomer")
        .then((res) => res.json())
        .then((res) => {
          const { count, entries } = res;
          this.count = count;
          this.entries = entries;
        })
        .catch((err) => {
          console.log(err);
        })
        .finally(() => (this.isLoading = false));
    },
    get_customer(val){
        this.form.header.id_cliente = val.id_cliente;
        this.form.header.nombre = val.nombre;
        this.form.header.tipo_documento = val.tipo_doc.tipo_documento;
        this.form.header.nro_doc = val.nro_doc;
        this.form.header.direccion = val.direccion;
    },
  },


  created () {
    this.$store.commit('LOADER',true);
    this.id_mesa = this.$route.params.id;

    this.idRoom = this.$route.params.id;
        this.readRoom();
    this.getCategorias();
    this.getProductos(0);
    this.getMetodoPago();
    this.getMoneda();
    this.getTiposComprobante();
    this.getTiposDoc();
  },

  methods: {
        async readRoom(){
            try{
                const data = await API.rooms.read(this.idRoom);
                this.room = data.data;
                this.idReservation = this.room.id_reservas;
                this.readReservation();
            }catch(error){
                console.error(error);
            }
        },
        async readReservation(){
            try{
                const data = await API.bookings.read(this.idReservation);
                this.booking = data.data;
                this.customer = this.booking.cliente;
            }catch(error){
                console.error(error);
            }
        },

        crearOrdenMozo(id_mozo){
            let mozos = this.mozos;
            let obj = mozos.filter(item => item.id === id_mozo);
            this.nombre_mozo = obj[0].name;
            this.add();
        },
        getMozos(){
            let mozosLocal = JSON.parse(localStorage.getItem('listamozos'));

            if(mozosLocal == null){
                axios.get('/api/getMozos')
                .then((response) => {
                    this.mozos = response.data;
                    localStorage.setItem('listamozos', JSON.stringify(this.mozos));
                })
                .catch( resonse => {
                    console.log(resonse);
                })
            }else{
                this.mozos = mozosLocal;
            }
        },
        getMetodoPago(){
            axios.get('/api/getMetodosPago')
            .then((response) => {
                this.metodospago = response.data;
                localStorage.setItem('metodospago', JSON.stringify(this.metodospago));
            })
            .catch( resonse => {
                console.log(resonse);
            }) 
            
        },
        getMoneda(){
            axios.get('/api/getMoneda')
            .then((response) => {
                this.monedas = response.data;
                localStorage.setItem('monedas', JSON.stringify(this.monedas));
            })
            .catch( resonse => {
                console.log(resonse);
            })
            
            
        },
        getTiposComprobante(){
            axios.get('/api/getTiposComprobante')
            .then((response) => {
                this.tipo_comprobante = response.data;
                localStorage.setItem('tiposcomprobante', JSON.stringify(response.data));
                this.$store.commit('LOADER',false);
            })
            .catch( resonse => {
                console.log(resonse);
                //this.$store.commit('LOADER',false);
            })
        },

        getserie(){
            axios.get('/api/getSerieComprobante/'+this.generate_sales.header.id_tipo_comprobante)
            .then((response) => {
                this.serie = response.data;
                //this.generate_sales.header.id_medio_pago = this.metodospago[0].id_medio_pago;
                this.generate_sales.header.id_serie = this.serie[0].id_serie;
            })
            .catch( resonse => {
                console.log(resonse);
            }) 
        },

        getCategorias(){
            axios.get('/api/getCategoriasTipoSucursal/1')
            .then((response) => {
                var array1 = response.data;
                this.categorias = this.categorias.concat(array1);

                localStorage.setItem('categorias', JSON.stringify(this.categorias));
            })
            .catch( resonse => {
                console.log(resonse);
            });
        },

        getProductos(id_categoria){
            axios.get('/api/getProductosSucursal/'+id_categoria)
            .then((response) => {
                this.productos = response.data;
                localStorage.setItem('productos', JSON.stringify(response.data));
            })
            .catch( resonse => {
                console.log(resonse);
            }) 
            
        },

        addItemOrder(item){
            this.$store.commit('LOADER',true);
            const ind = this.form.orden.detalle.findIndex((element) => element.id_producto == item.id_producto);
            if(ind==-1){
                let detalle={
                    id_producto: item.id_producto,
                    cantidad: 1,
                    nombre_producto: item.nombre_producto,
                    precio: Number(item.precio).toFixed(2),
                    total: Number(item.precio).toFixed(2),
                    observaciones: "",
                    id_reservas: this.idReservation,
                    id_unidad_medida: item.id_unidad_medida,
                    cocina:item.cocina,
                    codigo_producto: item.codigo_producto,
                    tipo_producto : item.tipo_producto_combo
                }
            console.log(detalle)
                this.form.orden.detalle.push(detalle);
                this.calcularTotalFila(this.form.orden.detalle.length-1);
                this.calcularTotal();
                this.$store.commit('LOADER',false);
            }
            else{
                this.addItem(ind);
                this.$store.commit('LOADER',false);
            }
            // let detalle={
            //     id_producto: item.id_producto,
            //     cantidad: 1,
            //     precio: Number(item.precio).toFixed(2),
            //     observaciones: "",
            //     id_orden: form.id_orden
            // }
            // form.orden.detalle.push(JSON.parse(JSON.stringify(response.data)));
            // this.calcularTotalFila(form.orden.detalle.length-1);
            // this.calcularTotal();
            
        },
        calcularTotalFila(indx){
            this.form.orden.detalle[indx].total = Number(this.form.orden.detalle[indx].precio * this.form.orden.detalle[indx].cantidad).toFixed(2);
            let id_detalle=this.form.orden.detalle[indx].id_detalle_orden;
            let val = this.form.orden.detalle[indx].cantidad;
            this.calcularTotal();

        },
        calcularTotal() {
            var sumaTotal;
            //sumaTotal = this.form.detail.reduce(function (sum, product) {
            sumaTotal = this.form.orden.detalle.reduce(function (sum, product) {
                var total_fila = parseFloat(product.total);
                if (!isNaN(total_fila)) {
                    return sum + total_fila;
                }
            }, 0);
            
            if(!isNaN(sumaTotal)){
                this.form.orden.subtotal = parseFloat(sumaTotal/1.18).toFixed(2);
                this.form.orden.igv = parseFloat(sumaTotal -  this.form.orden.subtotal).toFixed(2);
                this.form.orden.total = parseFloat(sumaTotal).toFixed(2);
            }else{
                this.form.orden.igv = '';
                this.form.orden.subtotal = '';
                this.form.orden.totall = '';
            }
            console.log(this.form.orden)
        },
        //PUT ITEM
        addItem(indx){
            this.form.orden.detalle[indx].cantidad++;
            this.calcularTotalFila(indx);
        },
        removeItem(indx){
            if(this.form.orden.detalle[indx].cantidad==1){ return }
            this.form.orden.detalle[indx].cantidad--;
            this.calcularTotalFila(indx);
        },

        deleteItem(indx){
                this.form.orden.detalle.splice(indx,1);
                //DELETE ITEM
                this.calcularTotal();
        },

        editObservation(indx){
            this.observationDialog=true;
            this.indxobservation=indx;
        },

        updateObservation(){
            let id_detalle=this.form.orden.detalle[this.indxobservation].id_detalle_orden;
            let val = this.form.orden.detalle[this.indxobservation].observaciones;
            this.observationDialog=false;
        },

        //GENERAR COMPROBANTE
        async preLoadData(){
            //this.dialogPayment = true;

            this.$store.commit('LOADER',true);
            
            let data = [];
            for (let index = 0; index < this.form.orden.detalle.length; index++) {
                const element = this.form.orden.detalle[index];
                data.push({
                    id_producto_servicio: element.id_producto,
                    nombre_producto_servicio: element.nombre_producto,
                    cantidad: element.cantidad,
                    precio_unitario: element.precio,
                    precio_total: element.total,
                    observaciones: element.observaciones,
                    pago: 0,
                    fecha_hora: new Date().toISOString().substr(0, 10),
                    id_reservas: this.idReservation,
                });
            }

            if(this.tipo_pago==0){
                try{
                    const response = await API.bookings.detail({detalle:data});
                    this.$swal({
                        toast: true,
                        position: 'top-end',
                        icon:'success',
                        title:'Venta registrada correctamente',
                        showConfirmButton:false,
                        timerProgressBar:true,
                        timer:2500
                    });
                    this.$store.commit('LOADER',false);
                    this.$router.push('/rooms_sales/')
                }catch(error){
                    this.$store.commit('LOADER',false);
                    console.error(error);
                }
            }
            if(this.tipo_pago==1){
                //PRECARGAR DATA DE HOSPEDAJE
                //this.generate_sales.header = Object.assign(this.tabs_form[this.tab].orden, response.data);
                this.generate_sales.header.id_tipo_comprobante = this.tipo_comprobante[2].id_tipo_comprobante;
                this.generate_sales.header.id_medio_pago = this.metodospago[0].id_medio_pago;
                this.generate_sales.header.id_moneda = this.monedas[0].id_moneda;
                this.generate_sales.header.nombre_cliente = this.customer.nombre;
                this.generate_sales.header.id_cliente = this.customer.id_cliente;
                this.generate_sales.header.id_reserva = this.idReservation;
                this.getserie();

                this.comprobanteDialog=true;
                this.$store.commit('LOADER',false);
            }
        },

        saveComprobante(){
            if(this.$refs.form.validate()){
                this.generate_sales.header = { ...this.generate_sales.header, ...this.form.orden}
                this.generate_sales.detail = [];
                this.loading=true;
                this.$store.commit('LOADER',true);
                let id_orden=this.form.id_orden;
                const element = this.form.orden.detalle;
                for (let index = 0; index < element.length; index++) {
                    const orden_detalle = element[index];
                    this.generate_sales.detail.push({
                        id_producto: orden_detalle.id_producto,
                        id_unidad_medida: orden_detalle.id_unidad_medida,
                        nombre_producto: orden_detalle.nombre_producto,
                        cantidad: orden_detalle.cantidad,
                        precio_unitario: orden_detalle.precio,
                        codigo_producto: orden_detalle.codigo_producto,
                        precio_total: orden_detalle.total,
                        tipo_producto: orden_detalle.tipo_producto
                    });
                    
                }
                console.log(this.generate_sales)
                axios.post('/api/sales',  this.generate_sales)
                .then((response) => {
                    if(response.data.success){
                        this.generate_sales.header = {};
                        this.generate_sales.detail = [];
                        this.comprobanteDialog = false;
                        if(response.data.print == 1){
                            printJS({printable:response.data.pdf, type:'pdf', maxWidth:80});
                            // this.urlComprobante = response.data.pdf
                            // this.verComprobanteDialog = true;
                            this.$store.commit('LOADER',false);
                        }
                        else{
                            window.open(response.data.pdf);
                            this.$store.commit('LOADER',false);
                        }
                        this.$router.push('/rooms_sales/');
                    }
                    else{
                        this.$store.commit('LOADER',false);
                        const error = JSON.parse(response.data.error);
                        this.$swal({
                            icon:'error',
                            title:'Opps!',
                            text:error.message,
                            timerProgressBar:true,
                            timer:2500
                        });
                    }
                })
                .catch( resonse => {
                    this.$store.commit('LOADER',false);
                    console.log(resonse);
                });
            }
            else{
                return;
            }
        },
        close(){
            this.generate_sales.header = {};
            this.generate_sales.detail = [];
            this.comprobanteDialog = false; 
        },

        deleteConsumo(id){
            this.dialogDelete = true;
        },
        async deleteOrdenConfirm(id){
            this.dialogDelete = false;
            this.$router.push('/rooms_sales/')
        },

        printCocina(){
            
            let headers_send = {headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Access-Control-Allow-Origin': '*'
            }};

            this.$store.commit('LOADER',true);

            axios.get('/api/user').then(response => {
                console.log(response);
                const data = response.data;
                let datos_print = {
                    restaurante: data.sucursal.nombre_sucursal,
                    mesa: 'Hab.' + this.room.nombre_habitacion,
                    mozo: 'Recepción',
                    url_print: data.sucursal.url_print_cocina,
                    items:[]
                };
                let filter_data = this.form.orden.detalle.filter((item) => item.cocina == 1);
                for (let index = 0; index < filter_data.length; index++) {
                    const element = filter_data[index];
                    datos_print.items.push({
                        producto: element.nombre_producto,
                        cantidad: element.cantidad,
                        observaciones: element.observaciones
                    });
                }
                console.log(datos_print);
                //Imprimimos en cocina, apuntamos al WS instalado localmente
                axios.post(datos_print.url_print,  datos_print, headers_send)
                .then((response2) => {
                   
                })
                .catch( resonse2 => {
                    console.log(resonse2);
                });

                this.$store.commit('LOADER',false);
            }).catch(error => {
                console.error(error)
            });
        },

        printCuenta(id_orden){
            this.$store.commit('LOADER',true);
            this.urlComprobante = '/generarTicketCuentaPDF/'+id_orden
            this.verComprobanteDialog = true;
            this.$store.commit('LOADER',false);
        },

        getTiposDoc(){
            let tiposDocLocal = JSON.parse(localStorage.getItem('tiposdoc'));

            if(tiposDocLocal == null){
                axios.get('/api/getTiposDoc')
                .then((response) => {
                    this.tipos_doc = response.data;
                    localStorage.setItem('tiposdoc', JSON.stringify(this.tipos_doc));
                })
                .catch( resonse => {
                    console.log(resonse);
                })
            }else{

                this.tipos_doc = tiposDocLocal;
                
            }
            
        },

        async SearchApi(){
            try {
                this.$store.commit('LOADER',true);

                this.datosBus.tipoDoc = this.nuevoCliente.id_tipo_doc;
                this.datosBus.numDoc = this.nuevoCliente.nro_doc;
                ///console.log(this.nuevoCliente); 
                const rpta = await axios.post("/api/buscarDniRuc",this.datosBus);
                if(rpta.data.success === true){
                    if(this.datosBus.tipoDoc === 2){
                        this.nuevoCliente.nombre = rpta.data.nombre_o_razon_social;
                        this.nuevoCliente.direccion = rpta.data.direccion_completa;
                        this.$store.commit('LOADER',false);
                        this.setTextSnack("Encontrado","green");
                    }else if(this.datosBus.tipoDoc === 1){
                        console.log(rpta.data.Nombres);
                        this.nuevoCliente.nombre = rpta.data.result.Nombres + " " + rpta.data.result.Apellidos;
                        this.$store.commit('LOADER',false);
                        this.setTextSnack("Encontrado","green");
                    }
                }else{
                    this.$store.commit('LOADER',false);
                    this.setTextSnack("No encontrado","red");
                }

                this.$store.commit('LOADER',false);

            } catch (error) {
                this.$store.commit('LOADER',false);
            }
        },

        addNuevoCliente(){
            
            this.nuevoClienteDialog = true;
        },
        async guardarCliente(){
            try{
                this.$store.commit('LOADER',true);
                const response = await axios.post("/api/customer",this.nuevoCliente);
                this.getCustomers();
                this.nuevoClienteDialog = false;
                this.$store.commit('LOADER',false);
            }catch(error){
                this.loading = false;
                this.$swal({
                  icon:'error',
                  title:'Error ',
                  text:error.response.data.message,
                  timerProgressBar:true,
                  timer:2500
                });
                this.$store.commit('LOADER',false);
            }
            
        }

  }
}
</script>
<style lang="scss" scoped>
  .btn-actions{
    background-color: #fff !important;
    color: #121212;
  }
*{
    text-transform: none !important;
    font-family:'Quicksand', sans-serif  !important;
}
.v-application .subtitle-2 {

    font-family:'Quicksand', sans-serif  !important;
}
  .theme--light.v-data-table > .v-data-table__wrapper > table > tbody > tr:hover:not(.v-data-table__expanded__content):not(.v-data-table__empty-wrapper) {
    background: #f8e2b6;
}
.v-application  .body-2{
    font-size: 13px !important;
     font-family:'Quicksand', sans-serif  !important;
}
</style>
