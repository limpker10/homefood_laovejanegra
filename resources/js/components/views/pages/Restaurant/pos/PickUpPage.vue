<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color:  #AD91FD">{{mesa.nombre_sucursal}} - Crear Pedido</h2>
      </div>
      <v-spacer></v-spacer>
      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
            <v-btn icon v-bind="attrs" v-on="on" :to="'/tablesSystem/'+id_sucursal"> 
            <v-icon>mdi-store-outline</v-icon>
            </v-btn>
        </template>
        Volver a Tienda
        </v-tooltip>
    </div>

    <div class="my-2">
        <div>
            <v-row no-gutters >
                <v-col cols="12" sm="7" md="7"
                style="padding: 10px;">
                    
                    <v-dialog v-model="observationDialog" max-width="300px">
                        <v-card>
                        <v-card-title class="subtitle-1">Comentario / Observaciones</v-card-title>
                        <v-card-text v-if="tabs_form.length>0">
                        <v-textarea  v-if="tabs_form[tab].detalle.length>0"
                            name="input-7-1"
                            rows="2"
                            label="Comentario / Observación"
                            v-model="tabs_form[tab].detalle[indxobservation].observaciones"
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
                    <v-card>
                        <v-flex column py-0>
                            <v-layout align-center>

                                <v-flex style="width: 200px" py-0>
                                    <v-tabs v-model="tab" show-arrows>
                                        <v-tab v-for="(item,index) in tabs_form" :key="index">
                                            <v-icon>mdi-food-turkey</v-icon> Orden {{ index+1 }}
                                        </v-tab>
                                    </v-tabs>
                                </v-flex>

                                <v-flex shrink>
                                    <v-btn @click="add" text>
                                        <v-icon color="primary">mdi-plus-box</v-icon>
                                    </v-btn>
                                </v-flex>
                                
                            </v-layout>

                            
                            <div v-if="tabs_form.length==0" style="text-align: center;height: 250px;align-items: center; padding: auto">
                                    <h2 style="margin:auto; color: rgba(0,0,0,.38);">Crear Nueva Orden</h2>
                                    <br>
                                    <v-btn @click="add" >
                                        <v-icon color="primary">mdi-plus-box</v-icon>
                                        Crear Nueva Orden
                                    </v-btn>
                            </div>
                            <v-tabs-items v-model="tab">
                                <v-tab-item
                                v-for="(form,index) in tabs_form"
                                :key="index"
                                >
                                <v-card flat style="height:100%;margin-top: 10px;">
                                    <v-row dense class="pa-2">
                                        <v-col cols="8">
                                            <br>
                                            <v-autocomplete
                                            v-model="form.id_cliente"
                                            :items="customers"
                                            dense
                                            item-text="nombre"
                                            item-value="id_cliente"
                                            label="Elegir Cliente"
                                            placeholder="Elegir Cliente"
                                            prepend-icon="mdi-account-group"
                                            @change="updateCustomer(form.id_orden)"
                                            ></v-autocomplete>
                                        </v-col>
                                        <v-col cols="4" align="center">
                                            <v-btn @click="addNuevoCliente()" medium color="success">
                                                <v-icon small> mdi-plus</v-icon> Nuevo Cliente
                                            </v-btn>
                                        </v-col>
                                    </v-row>
                                    <v-divider></v-divider>
                                    <v-simple-table v-if="form.detalle.length>0">
                                        <template v-slot:default>
                                            <thead>
                                                <tr>
                                                    <th style="width:5%;" class="text-left"></th>
                                                    <th class="text-left">Producto</th>
                                                    <th style="width:27%;" class="text-left">CNT</th>
                                                    <th style="width:17%;" class="text-left">P.U (S/.)</th>
                                                    <th style="width:20%;" class="text-left">P.T (S/.)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                v-for="(item,index) in form.detalle"
                                                :key="index"
                                                >
                                                    <td>
                                                        <v-btn @click="deleteItem(index)" small icon>
                                                            <v-icon small> mdi-delete</v-icon>
                                                        </v-btn>
                                                        <v-btn @click="editObservation(index)" small icon>
                                                            <v-icon small> mdi-comment-edit-outline</v-icon>
                                                        </v-btn>
                                                    </td>
                                                    <td>{{ item.producto.nombre_producto }}</td>
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
                                                                    style="padding: 0px; margin: auto;"
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
                                                    <td>
                                                        <v-text-field style="padding: 0px; margin: auto;"
                                                            v-on:keyup="calcularTotalFila(index)"
                                                            v-model="item.precio"
                                                            readonly
                                                            hide-details="auto"
                                                        ></v-text-field>
                                                    </td>
                                                    <td>
                                                        <v-text-field style="padding: 0px; margin: auto;"
                                                            v-model="item.total"
                                                            readonly
                                                            hide-details="auto"
                                                        ></v-text-field>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td rowspan="3" colspan="3">
                                                        <v-text-field
                                                            v-model="nombre_cliente_cocina"
                                                            hint="Nombre del cliente para ser llamado en cocina."
                                                            label="Nombre de Cliente"
                                                        ></v-text-field>
                                                    </td>
                                                    <td style="text-align:right;"><b>Subtotal: </b></td>
                                                    <td colspan="1">
                                                        <v-text-field
                                                            v-model="form.subtotal"
                                                            readonly
                                                            hide-details="auto"
                                                        ></v-text-field>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><b>IGV (10%): </b></td>
                                                    <td colspan="1">
                                                        <v-text-field
                                                            v-model="form.igv"
                                                            readonly
                                                            hide-details="auto"
                                                        ></v-text-field>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right;"><b>Total: </b></td>
                                                    <td colspan="1">
                                                        <v-text-field
                                                            v-model="form.total"
                                                            readonly
                                                            hide-details="auto"
                                                        ></v-text-field>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </template>
                                    </v-simple-table>
                                    <div v-else style="text-align: center;height: 250px;display: flex;align-items: center;">
                                        <h2 style="margin:auto; color: rgba(0,0,0,.38);">Productos no seleccionados</h2>
                                    </div>
                                    
                                    <v-divider></v-divider>
                                    <v-card-actions class="justify-end" >
                                        <v-btn color="error" icon @click="deleteMesaOrden(form.id_orden)">
                                        <v-icon>mdi-delete-outline</v-icon>
                                        </v-btn> 
                                        <v-spacer></v-spacer>
                                        <v-btn color="error"  @click="deleteMesaOrden(form.id_orden)" v-if="form.detalle.length>0">
                                        <v-icon>mdi-close</v-icon> Cancelar
                                        </v-btn>
                                        <v-btn color="info" v-if="form.detalle.length>0" @click="printCuenta(form.id_orden)">
                                        <v-icon>mdi-printer-pos</v-icon> Imprimir Cuenta
                                        </v-btn>
                                        <v-btn small color="accent" v-if="form.detalle.length>0"  @click="printCocina(0)">
                                        <v-icon>mdi-chef-hat</v-icon> Enviar Cocina</v-btn>
                                        <v-btn small color="accent" v-if="form.detalle.length>0"  @click="printCocina(1)">
                                        <v-icon>mdi-glass-cocktail</v-icon> Enviar Barra</v-btn>
                                        <v-btn color="success" v-if="form.detalle.length>0" @click="preLoadData">
                                        <v-icon>mdi-printer-pos</v-icon> Procesar Orden
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                                </v-tab-item>
                            </v-tabs-items>
                        </v-flex>
                    </v-card>
                </v-col>

                <!--ITEMS-->
                <v-col  cols="12" sm="5" md="5"
                style="padding: 10px;">
                    
                    <v-card style="height:500px; overflow-y: auto;">
                        <v-sheet
                            class="mx-auto"
                            max-width="700"
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
                                class="ma-2"
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
                        <search-bar @changeInput="addItemOrder" value="changeInput"></search-bar>
                        <v-container fluid>
                            <v-row dense>
                            <v-col
                                v-for="(card,index) in productos"
                                :key="index"
                                cols="4"
                            >
                                <v-card @click="addItemOrder(card)">
                                    <v-img
                                        :src="'../storage/files/uploads/'+card.path+'/'+card.filename"
                                        class="white--text align-end"
                                        gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.8)"
                                        height="150px"
                                    >
                                        <v-card-title class="subtitle-2" v-text="card.nombre_producto"></v-card-title>
                                    </v-img>
                                </v-card>
                            </v-col>
                            </v-row>
                        </v-container>
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
                                ></v-select>
                            </v-col>
                        </v-row>

                        <v-text-field
                            label="Cliente"
                            hide-details="auto"
                            readonly
                            v-model="generate_sales.header.nombre_cliente"
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
                                <tr v-for="(item,index) in  tabs_form[tab].detalle" :key="index">
                                    <td>{{item.producto.nombre_producto}}</td>
                                    <td>{{item.cantidad}}</td>
                                    <td>{{item.precio}}</td>
                                    <td>{{item.total}}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" style="text-align:right;"><b>Subtotal: </b></td>
                                    <td colspan="1">{{tabs_form[tab].subtotal}}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="text-align:right;"><b>IGV (18%): </b></td>
                                    <td colspan="1">{{tabs_form[tab].igv}}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="text-align:right;"><b>Total: </b></td>
                                    <td colspan="1">{{tabs_form[tab].total}}</td>
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
                        ></v-select>
                        <v-select
                            :items="monedas"
                            label="Moneda"
                            placeholder="Seleccione moneda"
                            item-text="nombre"
                            item-value="id_moneda"
                            v-model="generate_sales.header.id_moneda"
                            hide-details="auto"
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
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="error" dark @click="close"
                        >Cancelar</v-btn
                        >
                        <v-btn color="primary" @click="saveComprobante"
                        >Guardar</v-btn
                        >
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="dialogDelete" max-width="300px">
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
        </div>
    </div>
  </div>
</template>
 
<script>
import SearchBar from './components/searchBar.vue';
export default {
  components: {
      SearchBar,
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
        form:{
            header:{ fecha_emision: new Date().toISOString().substr(0, 10), },
            detail:[]
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

        id_sucursal:null,
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
        nombre_cliente_cocina: '',

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
    this.id_sucursal = this.$route.params.id;
    this.getBranch();
    this.getCustomers();
    this.getCategorias();
    this.getProductos(0);
    this.getOrdenes();

    this.getMetodoPago();
    this.getMoneda();
    this.getTiposComprobante();
    this.getTiposDoc();
  },

  methods: {
        getMetodoPago(){
            let metodosPagoLocal = JSON.parse(localStorage.getItem('metodospago'));

            if(metodosPagoLocal == null){
                axios.get('/api/getMetodosPago')
                .then((response) => {
                    this.metodospago = response.data;
                    localStorage.setItem('metodospago', JSON.stringify(this.metodospago));
                })
                .catch( resonse => {
                    console.log('error: '+ response.data);
                }) 
            }else{

                this.metodospago = metodosPagoLocal;
                
            }
        },
        getMoneda(){
            let monedasLocal = JSON.parse(localStorage.getItem('monedas'));

            if(monedasLocal == null){
                axios.get('/api/getMoneda')
                .then((response) => {
                    this.monedas = response.data;
                    localStorage.setItem('monedas', JSON.stringify(this.monedas));
                })
                .catch( resonse => {
                    console.log('error: '+ response.data);
                })
            }else{
                this.monedas = monedasLocal;
            }
        },
        getTiposComprobante(){
            let tiposComprobanteLocal = JSON.parse(localStorage.getItem('tiposcomprobante'));

            if(tiposComprobanteLocal == null){
                axios.get('/api/getTiposComprobante')
                .then((response) => {
                    this.tipo_comprobante = response.data;
                    localStorage.setItem('tiposcomprobante', JSON.stringify(response.data));
                    //this.$store.commit('LOADER',false);
                })
                .catch( resonse => {
                    console.log('error: '+ response.data);
                    //this.$store.commit('LOADER',false);
                })
            }else{
                this.tipo_comprobante = tiposComprobanteLocal;
                //this.$store.commit('LOADER',false);
            }

        },

        getserie(){
            axios.get('/api/getSerieComprobante/'+this.generate_sales.header.id_tipo_comprobante)
            .then((response) => {
                this.serie = response.data;
                //this.generate_sales.header.id_medio_pago = this.metodospago[0].id_medio_pago;
                this.generate_sales.header.id_serie = this.serie[0].id_serie;
            })
            .catch( resonse => {
                console.log('error: '+ response.data);
            }) 
        },

        getBranch(){

            axios.get('/api/branch/'+this.id_sucursal)
            .then((response) => {
                this.mesa = response.data;
            })
            .catch( resonse => {
                console.log('error: '+ response.data);
            }) 
        },

        getOrdenes(){
            axios.get('/api/getOrderByBranch/'+this.id_sucursal)
            .then((response) => { 
                this.tabs_form = [];
                this.tabs_form = response.data;
                for (let index = 0; index < this.tabs_form.length; index++) {
                    for (let j = 0; j < this.tabs_form[index].detalle.length; j++) {
                        this.tabs_form[index].detalle[j].total = Number(this.tabs_form[index].detalle[j].precio * this.tabs_form[index].detalle[j].cantidad).toFixed(2);
                    }
                    var sumaTotal;
                    sumaTotal = this.tabs_form[index].detalle.reduce(function (sum, product) {
                        var total_fila = parseFloat(product.total);
                        if (!isNaN(total_fila)) {
                            return sum + total_fila;
                        }
                    }, 0);
                    
                    if(!isNaN(sumaTotal)){
                        this.tabs_form[index].subtotal = parseFloat(sumaTotal/1.10).toFixed(2);
                        this.tabs_form[index].igv = parseFloat(sumaTotal -  this.tabs_form[index].subtotal).toFixed(2);
                        this.tabs_form[index].total = parseFloat(sumaTotal).toFixed(2);
                    }else{
                        this.tabs_form[index].igv = '';
                        this.tabs_form[index].subtotal = '';
                        this.tabs_form[index].totall = '';
                    }
                }

                this.$store.commit('LOADER',false);
            })
            .catch( resonse => {
                console.log('error: '+ response.data);
                this.$store.commit('LOADER',false);
            }) 
        },

        getCustomers(){
            axios.get('/api/searchCustomer/')
            .then((response) => {
                this.customers=response.data.entries;
            })
            .catch( resonse => {
                console.log('error: '+ response.data);
            }) 
        },
        
        //TABS ORDENES
        add() {
            
            this.$store.commit('LOADER',true);
            axios.post('/api/order',{id_mesa:null,numero_comensales:0, id_sucursal: this.id_sucursal})
            .then((response) => {
                this.tabs_form.push(response.data);
                this.tab = this.tabs_form.length-1;
                this.$store.commit('LOADER',false);
            })
            .catch( resonse => {
                console.log('error: '+ response.data);
                this.$store.commit('LOADER',false);
            });

        },

        getCategorias(){
            let categorias = JSON.parse(localStorage.getItem('categorias'));

            if(categorias == null){
                axios.get('/api/getCategoriasTipoSucursal/1')
                .then((response) => {
                    var array1 = response.data;
                    this.categorias = this.categorias.concat(array1);

                    localStorage.setItem('categorias', JSON.stringify(this.categorias));
                })
                .catch( resonse => {
                    console.log('error: '+ response.data);
                }) 
            }else{
                this.categorias = categorias;
            }
            
        },

        getProductos(id_categoria){
            //console.log(localStorage.getItem('cats'));
            let productosLocal = JSON.parse(localStorage.getItem('productos'));

            if(productosLocal == null){
                axios.get('/api/getProductosSucursal/'+id_categoria)
                .then((response) => {
                    this.productos = response.data;
                    localStorage.setItem('productos', JSON.stringify(response.data));
                })
                .catch( resonse => {
                    console.log('error: '+ response.data);
                }) 
            }else{
                //console.log(productosLocal);
                this.productos = productosLocal;
                if(id_categoria == 0){
                    this.productos = productosLocal;
                }else{
                    let prd = this.productos;
                    let obj = prd.filter(item => item.id_categoria === id_categoria);
                    this.productos = obj;
                }
                
            }
            /*axios.get('/api/getProductosSucursal/'+id_categoria)
            .then((response) => {
                this.productos = response.data;
            })
            .catch( resonse => {
                console.log('error: '+ response.data);
            })*/
        },

        addItemOrder(item){
            /*item.cantidad = 1;
            item.precio = Number(item.precio).toFixed(2);
            item.total = Number(item.precio).toFixed(2);*/
            if(this.tabs_form[this.tab] == undefined){
                return;
            }
            this.$store.commit('LOADER',true);
            const ind = this.tabs_form[this.tab].detalle.findIndex((element) => element.id_producto == item.id_producto);
            if(ind==-1){
                let detalle={
                    id_producto: item.id_producto,
                    cantidad: 1,
                    precio: Number(item.precio).toFixed(2),
                    observaciones: "",
                    id_orden: this.tabs_form[this.tab].id_orden
                }
                axios.post('/api/orderdetail',detalle)
                .then((response) => {
                    this.tabs_form[this.tab].detalle.push(JSON.parse(JSON.stringify(response.data)));
                    this.calcularTotalFila(this.tabs_form[this.tab].detalle.length-1);
                    this.calcularTotal();
                    this.$store.commit('LOADER',false);
                })
                .catch( resonse => {
                    this.$store.commit('LOADER',false);
                    console.log('error: '+ response.data);
                });
            }
            else{
                this.addItem(ind);
                this.$store.commit('LOADER',false);
            }
        },
        calcularTotalFila(indx){
            this.tabs_form[this.tab].detalle[indx].total = Number(this.tabs_form[this.tab].detalle[indx].precio * this.tabs_form[this.tab].detalle[indx].cantidad).toFixed(2);
            let id_detalle=this.tabs_form[this.tab].detalle[indx].id_detalle_orden;
            let val = this.tabs_form[this.tab].detalle[indx].cantidad;
            axios.put('/api/updateFieldOrdenDetalle/'+id_detalle, { field:'cantidad', value:val })
            .then((response) => {
                
            })
            .catch( resonse => {
                console.log('error: '+ response.data);
            });
            this.calcularTotal();

        },
        calcularTotal() {
            var sumaTotal;
            //sumaTotal = this.form.detail.reduce(function (sum, product) {
            sumaTotal = this.tabs_form[this.tab].detalle.reduce(function (sum, product) {
                var total_fila = parseFloat(product.total);
                if (!isNaN(total_fila)) {
                    return sum + total_fila;
                }
            }, 0);
            
            if(!isNaN(sumaTotal)){
                this.tabs_form[this.tab].subtotal = parseFloat(sumaTotal/1.10).toFixed(2);
                this.tabs_form[this.tab].igv = parseFloat(sumaTotal -  this.tabs_form[this.tab].subtotal).toFixed(2);
                this.tabs_form[this.tab].total = parseFloat(sumaTotal).toFixed(2);
            }else{
                this.tabs_form[this.tab].igv = '';
                this.tabs_form[this.tab].subtotal = '';
                this.tabs_form[this.tab].totall = '';
            }
        },
        //PUT ITEM
        addItem(indx){
            this.tabs_form[this.tab].detalle[indx].cantidad++;
            this.calcularTotalFila(indx);
        },
        removeItem(indx){
            if(this.tabs_form[this.tab].detalle[indx].cantidad==1){ return }
            this.tabs_form[this.tab].detalle[indx].cantidad--;
            this.calcularTotalFila(indx);
        },
        deleteItem(indx){
            axios.delete('/api/orderdetail/'+this.tabs_form[this.tab].detalle[indx].id_detalle_orden)
            .then((response) => {
                this.tabs_form[this.tab].detalle.splice(indx,1);
                //DELETE ITEM
                this.calcularTotal();
            })
            .catch( resonse => {
                console.log('error: '+ response.data);
            });
        },
        updateCustomer(id){
            axios.put('/api/updateFieldOrden/'+id, { field:'id_cliente', value:this.tabs_form[this.tab].id_cliente })
            .then((response) => {
            this.observationDialog=false;
            })
            .catch( resonse => {
                console.log('error: '+ response.data);
            });
        },
        editObservation(indx){
            this.observationDialog=true;
            this.indxobservation=indx;
        },

        updateObservation(){
            let id_detalle=this.tabs_form[this.tab].detalle[this.indxobservation].id_detalle_orden;
            let val = this.tabs_form[this.tab].detalle[this.indxobservation].observaciones;
            axios.put('/api/updateFieldOrdenDetalle/'+id_detalle, { field:'observaciones', value:val })
            .then((response) => {
            this.observationDialog=false;
            })
            .catch( resonse => {
                console.log('error: '+ response.data);
            });
        },

        //GENERAR COMPROBANTE
        preLoadData(){
            this.$store.commit('LOADER',true);
            let id_orden=this.tabs_form[this.tab].id_orden;
            axios.get('/api/preLoadData/'+id_orden)
            .then((response) => {
                //var obj = Object.assign(o1, o2, o3);
                //this.generate_sales.header = response.data;
                this.generate_sales.header = Object.assign(this.tabs_form[this.tab], response.data);
                this.generate_sales.header.id_tipo_comprobante = this.tipo_comprobante[0].id_tipo_comprobante;
                this.generate_sales.header.id_medio_pago = this.metodospago[0].id_medio_pago;
                this.generate_sales.header.id_moneda = this.monedas[0].id_moneda;
                this.getserie();
                this.comprobanteDialog=true; 
                this.$store.commit('LOADER',false);
            })
            .catch( resonse => {
                console.log(resonse);
                this.$store.commit('LOADER',false);
            });
        },

        saveComprobante(){
            this.$store.commit('LOADER',true);
            let id_orden=this.tabs_form[this.tab].id_orden;
            const element = this.tabs_form[this.tab].detalle;
            for (let index = 0; index < element.length; index++) {
                const orden_detalle = element[index];
                this.generate_sales.detail.push({
                    id_producto: orden_detalle.id_producto,
                    id_unidad_medida: orden_detalle.producto.id_unidad_medida,
                    nombre_producto: orden_detalle.producto.nombre_producto,
                    nombre_producto: orden_detalle.producto.nombre_producto,
                    cantidad: orden_detalle.cantidad,
                    precio_unitario: orden_detalle.precio,
                    codigo_producto: orden_detalle.producto.codigo_producto,
                    precio_total: orden_detalle.total,
                    tipo_producto: orden_detalle.producto.tipo_producto_combo
                });
            }
            axios.post('/api/storeByOrder/'+id_orden,  this.generate_sales)
            .then((response) => {
                this.generate_sales.header = {};
                this.generate_sales.detail = [];
                this.comprobanteDialog = false;
                this.nombre_cliente_cocina = '';
                this.urlComprobante = response.data.pdf
                this.verComprobanteDialog = true;
                this.$store.commit('LOADER',false);
                this.getOrdenes();
            })
            .catch( resonse => {
                this.$store.commit('LOADER',false);
                console.log('error: '+ response.data);
            });

        },
        close(){
            this.generate_sales.header = {};
            this.generate_sales.detail = [];
            this.comprobanteDialog = false; 
        },

        deleteMesaOrden(id){
            this.deleteOrder = id;
            this.dialogDelete = true;
        },
        async deleteOrdenConfirm(id){
            try{
                await API.order.remove(this.deleteOrder);
                this.dialogDelete = false;
                this.getOrdenes();
            }catch(error){
                console.error(error.response.status);
                this.dialogDelete = false;
            }
        },
        
        printCocina(tipo=0){
            let id_orden=this.tabs_form[this.tab].id_orden;
            let id_sucursal_mesa = this.mesa.id_sucursal;
            let headers_send = {headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Access-Control-Allow-Origin': '*'
            }};

            this.$store.commit('LOADER',true);
            let urlPath = tipo==0 ? '/api/itemsPrintCocina/' : '/api/itemsPrintBarra/';

            axios.get( urlPath + id_orden)
            .then((response) => {

                let datos_print = response.data;
                datos_print.nombre_cliente = this.nombre_cliente_cocina;
                //Imprimimos en cocina, apuntamos al WS instalado localmente
                axios.post(datos_print.url_print,  datos_print, headers_send)
                .then((response2) => {
                    this.$router.push('/tablesSystem/'+id_sucursal_mesa);
                })
                .catch( resonse2 => {
                    //console.log(this.mesa.id_sucursa);
                    //this.$router.push('/tablesSystem/'+id_sucursal_mesa);
                    console.log(resonse2);
                });

                this.$store.commit('LOADER',false);
            })
            .catch( resonse => {
                this.$store.commit('LOADER',false);
                console.log(resonse);
            });

        },

        getTiposDoc(){
            axios.get('/api/getTiposDoc')
            .then((response) => {
                this.tipos_doc = response.data;
            })
            .catch( resonse => {
                console.log('error: '+ response.data);
            }) 
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
            this.$store.commit('LOADER',true);
            const response = await axios.post("/api/customer",this.nuevoCliente);
            this.getCustomers();
            this.nuevoClienteDialog = false;
            this.$store.commit('LOADER',false);
        },

        printCuenta(id_orden){
            this.$store.commit('LOADER',true);
            this.urlComprobante = '/generarTicketCuentaPDF/'+id_orden
            this.verComprobanteDialog = true;
            this.$store.commit('LOADER',false);
        },

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
</style>
