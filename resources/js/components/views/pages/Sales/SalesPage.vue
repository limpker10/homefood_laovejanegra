<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color:  #AD91FD">Comprobante</h2> 
        <!--<v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>-->
      </div>
      <v-spacer></v-spacer>
      <v-btn icon >
        <v-icon>mdi-refresh</v-icon>
      </v-btn>
    </div>
    <div class="my-2">
      <div>
        <v-card class="mb-4" light style="padding: 15px">
          <v-row>
            <v-col
              cols="12"
              sm="6"
              md="4"
            >
              <v-menu
                v-model="menu1"
                :close-on-content-click="false"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="filter.fecha_inicio"
                    label="Fecha Inicio"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  no-title
                  v-model="filter.fecha_inicio"
                  @input="menu1 = false"
                ></v-date-picker>
              </v-menu>
            </v-col>
            <v-col
              cols="12"
              sm="6"
              md="4"
            >
              <v-menu
                v-model="menu2"
                :close-on-content-click="false"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="filter.fecha_fin"
                    label="Fecha Fin"
                    prepend-icon="mdi-calendar"
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  no-title
                  v-model="filter.fecha_fin"
                  @input="menu2 = false"
                ></v-date-picker>
              </v-menu>
            </v-col>

            <v-col
              cols="12"
              sm="6"
              md="2"
            >
              <v-select 
                :items="mozos" 
                label="Atendido por"  
                placeholder="" 
                v-model="filter.id_mozo"
                item-text="name"
                item-value="id"></v-select>
            </v-col>

            <v-col
              cols="12"
              sm="6"
              md="2"
            >
            <v-btn color="primary" class="mr-2" @click="getlist">
              <v-icon>mdi-magnify</v-icon>
              Buscar
            </v-btn>            
            </v-col>
          </v-row>
        </v-card>
      </div>
    </div>
    <div class="my-2">
    <div>
      <v-card class="mb-4" light style="padding: 15px">
        <v-data-table 
        :headers="headers"
        :items="data.data"
        class="elevation-1"
        :server-items-length="data.total"
        :page="page"
        :options.sync="options"
        :items-per-page="itemsperpage"
        :footer-props="{
            itemsPerPageOptions: [25,50,100,1000],
            itemsPerPageText:'Items por página'
        }"
        >
            <template v-slot:[`item.date_emit`]="{ item }">
                <div>{{item.created_at | formatDate2}}</div>
            </template>

            <template v-slot:[`item.tipo_comprobante`]="{ item }">
                <div>{{item.tipo_comprobante.tipo_comprobante}}</div>
            </template>

            <template v-slot:[`item.nro_comprobante`]="{ item }">
                <div>{{item.serie.serie}} - {{String(item.correlativo).padStart(8, "0")}}</div>
            </template>

            <template v-slot:[`item.origin`]="{ item }">
                <div v-if="item.id_reserva==null">Restaurante</div>
                <div v-if="item.id_reserva!=null">Hotel</div>
            </template>

            <template v-slot:[`item.estado`]="{ item }">
                <div>
                    <v-chip v-if="item.id_estado_comprobante == 1" class="ma-2" color="success">
                        Aprobado
                    </v-chip>
                    <v-chip v-if="item.id_estado_comprobante == 2" class="ma-2" color="error">
                        Anulado
                    </v-chip>
                    <v-chip v-if="item.id_estado_comprobante == 3" class="ma-2" color="warning">
                        Rechazado
                    </v-chip>
                    <v-chip v-if="item.id_estado_comprobante == 4" class="ma-2" color="info">
                        Pendiente
                    </v-chip>
                    <v-chip v-if="item.id_estado_comprobante == 5" class="ma-2" color="grey">
                        Sin Estado
                    </v-chip>
                </div>
            </template>

            <template v-slot:[`item.actions`]="{ item }">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on" :to="'/sales_view/'+item.id_comprobante">
                      <v-icon small> mdi-file-eye-outline</v-icon>
                    </v-btn>
                  </template>
                  Ver Comprobante
                </v-tooltip>

                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on" @click="verpdf(item)"
                      v-if="item.id_estado_comprobante==1 && item.id_tipo_comprobante!=3">
                      <v-icon small> mdi-file-pdf-box-outline</v-icon>
                    </v-btn>
                  </template>
                  Ver PDF
                </v-tooltip>

                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on" @click="verpdf(item)"
                      v-if="item.id_tipo_comprobante==3">
                      <v-icon small> mdi-file-pdf-box-outline</v-icon>
                    </v-btn>
                  </template>
                  Ver PDF
                </v-tooltip>

                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on" @click="deleteInvoice(item.id_comprobante)"
                      v-if="item.id_estado_comprobante==1">
                      <v-icon small> mdi-close-circle-outline</v-icon>
                    </v-btn>
                  </template>
                  Anular Comprobante
                </v-tooltip>

                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on" @click="openDialogComprobante(item)" 
                      v-if="item.id_tipo_comprobante==3">
                      <v-icon small> mdi-receipt</v-icon>
                    </v-btn>
                  </template>
                  Generar Comprobante
                </v-tooltip>

                <!--<v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on"
                      v-if="item.id_estado_comprobante==3 || (item.id_estado_comprobante==4 && item.id_tipo_comprobante!=3)">
                      <v-icon small> mdi-cloud-sync-outline</v-icon>
                    </v-btn>
                  </template>
                  Enviar SUNAT
                </v-tooltip>-->
            </template>
        </v-data-table>
      </v-card>

      <v-dialog v-model="verComprobanteDialog" max-width="500px">
          <v-card>
              <iframe :src="urlComprobante" frameborder="0" height="700px" width="100%">
              </iframe>
          </v-card>
      </v-dialog>


        <v-dialog v-model="dialogMotivoAnulacion" max-width="320px">
            <v-card>
                <v-card-title class="subtitle-1">Ingrese el motivo de anulación</v-card-title>
                <v-card-text>
                    <v-container>
                        <v-text-field
                            v-model="motivo_anulacion"
                            label="Motivo"
                        ></v-text-field>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" dark @click="dialogMotivoAnulacion=false;">Cancelar</v-btn>
                <v-btn color="primary" @click="dialogMotivoAnulacion=false;dialogDelete=true;">Aceptar</v-btn>
                <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="dialogDelete" max-width="320px">
            <v-card>
                <v-card-title class="subtitle-1">¿Está seguro que desea anular este comprobante?</v-card-title>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" dark @click="dialogDelete=false;">Cancelar</v-btn>
                <v-btn color="primary" @click="deleteInvoiceConfirm">Aceptar</v-btn>
                <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!--
        <v-dialog v-model="dialogGenerarComprobante" max-width="500px">
            <v-card>
                <v-card-title class="subtitle-1">Generar Comprobante</v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row dense>
                            <v-col cols="8">
                                <v-autocomplete
                                v-model="generate_sales.id_cliente"
                                :items="customers"
                                dense
                                item-text="nombre"
                                item-value="id_cliente"
                                label="Elegir Cliente"
                                placeholder="Elegir Cliente"
                                @change="updateCustomer(form.id_orden)"
                                ></v-autocomplete>
                            </v-col>
                            <v-col cols="4" align="center">
                                <v-btn @click="addNuevoCliente()" medium color="success">
                                    <v-icon small> mdi-plus</v-icon> Nuevo Cliente
                                </v-btn>
                            </v-col>
                        </v-row>
                        <v-row dense>
                            <v-col cols="6" >
                                <v-select
                                    :items="tipo_comprobante"
                                    label="Comprobante"
                                    placeholder="Selecciona un tipo de comprobante"
                                    item-text="tipo_comprobante"
                                    item-value="id_tipo_comprobante"
                                    v-model="generate_sales.id_tipo_comprobante"
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
                                    v-model="generate_sales.id_serie"
                                    hide-details="auto"
                                ></v-select>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" dark @click="dialogGenerarComprobante=false;">Cancelar</v-btn>
                <v-btn color="primary" @click="updateComprobante">Aceptar</v-btn>
                <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>-->
        <v-dialog v-model="dialogGenerarComprobante" max-width="500px">
            <v-card>
                <v-card-title class="subtitle-1">Generar Comprobante</v-card-title>
                <v-card-text>
                    <v-container>
                      <v-form
                        ref="formComprobante"
                        v-model="valid"
                        lazy-validation
                      >
                        <v-row dense>
                            <v-col cols="4" >
                                <v-text-field
                                    
                                    label="Nro. Documento"
                                    hide-details="auto"
                                    v-model="generate_sales.nro_doc"
                                    append-icon="mdi-magnify"
                                    @click:append="toggleSearchCsutomer"
                                    :rules="requiredRules"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="8" >
                                <v-text-field
                                    
                                    label="Cliente"
                                    hide-details="auto"
                                    v-model="generate_sales.nombre"
                                    :rules="requiredRules"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" >
                              <v-text-field
                                
                                label="Dirección"
                                hide-details="auto"
                                v-model="generate_sales.direccion"
                                :rules="requiredRules"
                              ></v-text-field>
                            </v-col>
                        </v-row>
                        <br>
                        <v-row dense>
                            <v-col cols="6" >
                                <v-select
                                    :items="tipo_comprobante"
                                    label="Comprobante"
                                    placeholder="Selecciona un tipo de comprobante"
                                    item-text="tipo_comprobante"
                                    item-value="id_tipo_comprobante"
                                    v-model="generate_sales.id_tipo_comprobante"
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
                                    v-model="generate_sales.id_serie"
                                    hide-details="auto"
                                     :rules="requiredRules"
                                ></v-select>
                            </v-col>
                        </v-row>
                      </v-form>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" dark @click="dialogGenerarComprobante=false;">Cancelar</v-btn>
                <v-btn color="primary"  @click="updateComprobante">Aceptar</v-btn>
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
                            <v-select :items="tipos_doc" label="Tipo Documento" placeholder="Selecciona un tipo de Documento" v-model="nuevoCliente.id_tipo_doc" :rules="requiredRules" item-text="tipo_documento" item-value="id_tipo_doc"></v-select>
                            <v-text-field  v-model="nuevoCliente.nombre" label="Nombre / Razón Social Cliente" placeholder="Nombre / Razón Social Cliente" :rules="requiredRules"
                            ></v-text-field>
                        <v-text-field v-model="nuevoCliente.email" label="Correo Electrónico" placeholder="Correo Electrónico"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="6"  md="6" ><!--v-on:keyup.enter="buscarDoc()"-->
                            <v-text-field  v-model="nuevoCliente.nro_doc" label="Nro. Documento" placeholder="Nro. Documento" 
                                        :rules="[rules.required,rules.only_numbers]" :append-outer-icon="iconSearch" @click:append-outer="SearchApi()"></v-text-field>
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
export default {
  components: {
  },
  data: () => ({
    search: '',
    dialog: false,
    headers: [
      { text: 'ID', sortable: false, value: 'id_comprobante' },
      { text: 'Cliente', sortable: false, value: 'nombre_cliente' },
      { text: 'Tipo Comprobante', sortable: false, value: 'tipo_comprobante' },
      { text: 'Nro. Comprobante', sortable: false, value: 'nro_comprobante' },
      { text: 'Estado', sortable: false, value: 'id_estado_comprobante' },
      { text: 'Total', sortable: false, value: 'total', align:'right' },
      { text: 'Fecha', sortable: false, value: 'date_emit' },
      { text: 'Estado', sortable: false, value: 'estado' },
      { text: 'Origen', sortable: false, value: 'origin' },
      { text: 'Acciones', value: 'actions', align: 'center', sortable: false, align:'right' }
    ],
    menu1: false,
    menu2: false,
    desserts: [],
    customers:[],
    editedIndex: -1,
    data: {
        data: [],
        total: 0,
    },
    options: {},
    searchData: {},
    itemsperpage: 25,
    page:1,
    productos: [],
    verComprobanteDialog:false,
    urlComprobante: '',

    id_comprobante: 0,

    motivo_anulacion:'',
    dialogMotivoAnulacion: false,
    dialogDelete:false,

    filter: {
      fecha_inicio:'',
      fecha_fin:'',
      id_mozo:null,
    },


    tipo_comprobante:[
      { id_tipo_comprobante:1, tipo_comprobante:'Boleta', codigo_sunat: 'BV' },
      { id_tipo_comprobante:2, tipo_comprobante:'Factura', codigo_sunat: 'FC' }
    ],
    serie:[],
    dialogGenerarComprobante: false,
    generate_sales:{
      id_cliente:0,
      nro_doc: "-",
      nombre: "-",
      direccion: "-"
    },
    valid: true,
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

    iconSearch:"mdi-magnify",
    requiredRules: [
        v => !!v || 'Campo obligatorio',
    ],
    emailRules: [
        v => !!v || 'Campo obligatorio',
        v => /.+@.+\..+/.test(v) || 'Correo Electrónico debe ser válido',
    ],
    
    datosBus: {
        tipoDoc: '',
        numDoc: ''
    },
    mozos:[],
    
    rules: {
        required: (value) => !!value || 'Campo obligatorio',
        only_numbers: UTILS.rules.only_numbers,
    },
  }),

  computed: {
    
  },

  watch: {
    options(event) {
      this.itemsperpage=event.itemsPerPage;
      this.getlist(event.page, event.itemsPerPage);
    },
  },

  created () {
    this.generate_sales.id_tipo_comprobante = this.tipo_comprobante[0].id_tipo_comprobante;
    this.getserie();
    this.getlist();
    this.getCustomers();

    this.getTiposDoc();

    this.getMozos();

  },

  methods: {
    async toggleSearchCsutomer(){
        this.$store.commit('LOADER',true);
        let tipodoc = 1;
        if(this.generate_sales.nro_doc.length==8){
            tipodoc = 1;
        }
        else{
            tipodoc = 2;
        }
        const rpta = await axios.post("/api/buscarDniRuc",{tipoDoc:tipodoc, numDoc:this.generate_sales.nro_doc});
        /*
        if(rpta.data.success === true){
            if(tipodoc === 2){
                this.generate_sales.nombre_cliente = rpta.data.nombre_o_razon_social;
                this.generate_sales.direccion_cliente = rpta.data.direccion_completa;
                this.$store.commit('LOADER',false);
            }else if(tipodoc === 1){
                this.generate_sales.nombre_cliente = rpta.data.result.Nombres + " " + rpta.data.result.Apellidos;
                this.$store.commit('LOADER',false); 
            }
            console.log(this.generate_sales.nombre_cliente)
        }else{
            this.$store.commit('LOADER',false);
            //this.setTextSnack("No encontrado","red");
        }*/
        console.log(rpta);
        this.$store.commit('LOADER',false);
    },
    getMozos(){
       axios.get('/api/getMozos')
        .then((response) => {
          console.log(response.data);
            this.mozos = response.data;
        })
        .catch( resonse => {
            console.log(resonse);
        });
    },
    getlist(page=1,perpage=25) {
        this.$store.commit('LOADER',true);
        this.data.data= [];
        this.data.total= 0;
        this.loading = true;
        this.searchData = {
            perpage: perpage,
            data: this.filter
        };
        this.page=page;
        this.cargando = true;
        axios.post('api/getComprobantes?page=' + this.page, this.searchData)
        .then(response => {
            this.data = response.data;
            this.page = response.data.current_page;
            this.$store.commit('LOADER',false);
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
              if(this.datosBus.tipoDoc === 1){
                  this.nuevoCliente.nombre = rpta.data.nombre_o_razon_social;
                  this.nuevoCliente.direccion = rpta.data.direccion_completa;
                  this.$store.commit('LOADER',false);
              }else if(this.datosBus.tipoDoc === 2){
                  console.log(rpta.data.Nombres);
                  this.nuevoCliente.nombre = rpta.data.result.Nombres + " " + rpta.data.result.Apellidos;
                  this.$store.commit('LOADER',false);
              }
          }
          else{
              this.$store.commit('LOADER',false);
              this.$swal({ 
                  icon:'error',
                  title:'Opps!',
                  text:rpta.data.msg,
                  timerProgressBar:true,
                  timer:2500
              });
          }
          this.$store.commit('LOADER',false);

        } catch (error) {
            this.$store.commit('LOADER',false);
        }
    },

    verpdf(item){
      this.$store.commit('LOADER',true);
      //window.open('/generarTicketPDF/'+id, '_blank')               
      this.comprobanteDialog = false;
      // if(!item.id_reserva){
        if(item.id_tipo_comprobante!=3){
          this.urlComprobante = item.sucursal.facturador+'/print/document/'+item.external_id
          this.verComprobanteDialog = true;
          this.$store.commit('LOADER',false);
        }
        else{
          this.urlComprobante =  '/generarTicketPDF/'+item.id_comprobante
          this.verComprobanteDialog = true;
          this.$store.commit('LOADER',false);
        }
      // }
      // else{
      //     this.urlComprobante =  '/inovicePDF/'+item.id_comprobante
      //     this.verComprobanteDialog = true;
      //     this.$store.commit('LOADER',false);
      // }
    },

    getserie(){
        axios.get('/api/getSerieComprobante/'+this.generate_sales.id_tipo_comprobante)
        .then((response) => {
            this.serie = response.data;
            this.generate_sales.id_serie = this.serie[0].id_serie
        })
        .catch( resonse => {
            console.log('error: '+ response.data);
        }) 
    },
    openDialogComprobante(item){
        this.id_comprobante = item.id_comprobante;
        this.generate_sales.direccion = item.cliente.direccion;
        this.generate_sales.id_cliente = item.cliente.id_cliente;
        this.generate_sales.nombre = item.cliente.nombre;
        this.generate_sales.nro_doc = item.cliente.nro_doc;
        this.dialogGenerarComprobante = true;


    },
    updateComprobante() {
        this.$store.commit('LOADER',true);
        axios.put('/api/sales/'+this.id_comprobante,this.generate_sales).then(response => {
            this.dialogGenerarComprobante=false;
            this.getlist();
            this.urlComprobante = response.data.pdf;
            this.verComprobanteDialog = true;
            this.$store.commit('LOADER',false);
        });
    },

    deleteInvoice(id) {
        this.id_comprobante = id;
        this.dialogMotivoAnulacion = true;
    },
    deleteInvoiceConfirm () {
        this.$store.commit('LOADER',true);
        axios.put('/api/sales/anular/'+this.id_comprobante,{motivo_anulacion:this.motivo_anulacion}).then(response => {
            this.$swal({
                toast: true,
                position: 'top-end',
                icon:'success',
                title:'Eliminado!',
                text:'El comprobante ha sido anulado.',
                timerProgressBar:true,
                timer:2500
            });
            this.dialogDelete=false;
            this.$store.commit('LOADER',false);
            this.getlist();
        });
    },

    async getCustomers(){
        this.$store.commit('LOADER',true);
        await axios.get('/api/searchCustomer')
        .then((response) => {
            this.customers=response.data.entries;
            this.generate_sales.id_cliente = this.customers[0].id_cliente;
            this.$store.commit('LOADER',false);
        })
        .catch( resonse => {
          this.$store.commit('LOADER',false);
            console.log('error: '+ resonse.data);
        }) 
    },
    
    updateCustomer(id){
        axios.put('/api/updateFieldOrden/'+id, { field:'id_cliente', value:this.tabs_form[this.tab].orden.id_cliente })
        .then((response) => {
        this.observationDialog=false;
        })
        .catch( resonse => {
            console.log('error: '+ response.data);
        });
    },

    addNuevoCliente(){    
        this.nuevoClienteDialog = true;
    },
    async guardarCliente(){
      if(this.$refs.formCliente.validate()){
        this.$store.commit('LOADER',true);
        const response = await axios.post("/api/customer",this.nuevoCliente);
        this.getCustomers();
        this.generate_sales.id_cliente = response.data.id_cliente;
        this.nuevoClienteDialog = false;
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
</style>
