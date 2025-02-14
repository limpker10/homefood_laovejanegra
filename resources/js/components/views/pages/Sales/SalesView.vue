<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color:  #AD91FD">Ver Comprobante</h2> 
        <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
      </div>
    </div>

    <div class="my-2">
    <div>
        <v-card class="mb-4" light style="padding: 15px">
                <v-card-title primary-title>
                    {{form.header.serie.serie}} {{String(form.header.correlativo).padStart(8, "0")}}
                    <v-spacer></v-spacer>
                    <v-btn 
                        @click="verpdf()"
                        depressed
                        color="error"
                        v-if="form.header.id_estado_comprobante==1"
                        >
                        <v-icon small> mdi-file-pdf-box-outline</v-icon> Ver PDF
                    </v-btn>
                    &nbsp;&nbsp;
                    <v-btn 
                        @click="dialogMotivoAnulacion=true;"
                        depressed
                        color="warning"
                        v-if="form.header.id_estado_comprobante==4 || form.header.id_estado_comprobante==1"
                        >
                        <v-icon small> mdi-close-circle-outline</v-icon> Anular Comprobante
                    </v-btn>

                    <v-alert dense outlined type="error" v-if="form.header.id_estado_comprobante==2">
                        Comprobante <strong>Anulado</strong>
                    </v-alert>
                </v-card-title>
                <v-row dense class="pa-2">
                    <v-col cols="4">
                        <v-text-field
                            v-model="form.header.nombre_cliente"
                            label="Nombre Cliente"
                            readonly
                        ></v-text-field>
                    </v-col>
                    <v-col cols="2">
                        <v-text-field
                            v-model="form.header.cliente.tipo_doc.tipo_documento"
                            label="Tipo Documento"
                            readonly
                        ></v-text-field>
                    </v-col>
                    <v-col cols="2">
                        <v-text-field
                            v-model="form.header.nro_documento"
                            label="Nro. Documento"
                            readonly
                        ></v-text-field>
                    </v-col>
                    <v-col cols="4">
                        <v-text-field
                            v-model="form.header.created_at"
                            label="Fecha de Emision"
                            readonly
                        ></v-text-field>
                    </v-col>
                </v-row>
                <br>
                <v-divider></v-divider>
                <br>
                <v-simple-table>
                    <template v-slot:default>
                        <thead>
                            <tr>
                                <th style="width:7%;" class="text-left"></th>
                                <th class="text-left">Código</th>
                                <th class="text-left">Producto</th>
                                <th style="width:10%;" class="text-left">CNT</th>
                                <th style="width:10%;" class="text-left">P.U (S/.)</th>
                                <th style="width:10%;" class="text-left">P.T (S/.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                            v-for="(item,index) in form.detail"
                            :key="index"
                            >
                                <td>{{ index+1 }}</td>
                                <td>{{ item.codigo_producto }}</td>
                                <td>{{ item.nombre_producto }}</td>
                                <td>{{ item.cantidad }}</td>
                                <td>{{ item.precio_unitario }}</td>
                                <td>{{ item.precio_total }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" style="text-align:right;"><b>Subtotal: </b></td>
                                <td>
                                    <v-text-field
                                        v-model="form.header.subtotal"
                                        readonly
                                        hide-details="auto"
                                    ></v-text-field>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" style="text-align:right;"><b>IGV (18%): </b></td>
                                <td>
                                    <v-text-field
                                        v-model="form.header.igv"
                                        readonly
                                        hide-details="auto"
                                    ></v-text-field>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" style="text-align:right;"><b>Total: </b></td>
                                <td>
                                    <v-text-field
                                        v-model="form.header.total"
                                        readonly
                                        hide-details="auto"
                                    ></v-text-field>
                                </td>
                            </tr>
                        </tfoot>
                    </template>
                </v-simple-table>
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
                            :rules="requiredRules"
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

    </div>
    </div>
  </div>
</template>
 
<script>
export default {
  components: {
  },
  data: () => ({
        breadcrumbs: [{
                text: 'Comprobantes',
                disabled: false,
                to: '/sales'
            },
            {
                text: 'Ver Cormprobante',
                disabled: true,
            }
        ],
        form:{
            header:{ fecha_emision: new Date().toISOString().substr(0, 10), serie:{serie:null}, cliente:{tipo_doc:{}},},
            detail:[]
        },
        id_comprobante:null,
        isLoading: false,
        verComprobanteDialog:false,
        urlComprobante: '',
        dialogDelete:false,
        dialogMotivoAnulacion:false,
        motivo_anulacion:'',
        requiredRules: [(v) => !!v || "Campo obligatorio"],
  }),


  computed: {
    
  },

  watch: {
    
  },

  created () {
      this.id_comprobante = this.$route.params.id;
      this.getPurchase();
      this.getPurchaseDetail();
  },

  methods: {
      getPurchase(){
        this.$store.commit('LOADER',true);
           axios.get('/api/sales/'+this.id_comprobante)
            .then(response => {
                response.data.created_at = response.data.created_at.split('T')[0];
                //response.data.tipo_documento = response.data.proveedor.tipo_doc.tipo_documento;
                this.form.header = response.data;
                this.$store.commit('LOADER',false);
            }).catch((error)=>{
                console.log(error)
            });
      },
      getPurchaseDetail(){
           axios.get('/api/sales/detail/'+this.id_comprobante)
            .then(response => {
                this.form.detail = response.data;
            }).catch((error)=>{
                console.log(error)
            });
      },
      verpdf(){
        //window.open('/generarTicketPDF/'+this.id_comprobante, '_blank')
        this.$store.commit('LOADER',true);             
        this.comprobanteDialog = false;
        if(this.form.header.id_tipo_comprobante!=3){
            this.urlComprobante = this.form.header.sucursal.facturador+'print/document/'+this.form.header.external_id+'/ticket'
            this.verComprobanteDialog = true;
            this.$store.commit('LOADER',false);
        }else{
            this.urlComprobante =  '/generarTicketPDF/'+this.form.header.id_comprobante
            this.verComprobanteDialog = true;
            this.$store.commit('LOADER',false);
        }
      },

        deleteInvoice() {
            this.dialogDelete = true;
        },
        deleteInvoiceConfirm () {
            this.$store.commit('LOADER',true);
            axios.put('/api/sales/anular/'+this.form.header.id_comprobante,{motivo_anulacion:this.motivo_anulacion}).then(response => {
                this.loading = false;
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
                this.$route.push({name:'SalesPage'})
            });
        },
      
  }
}
</script>
<style lang="scss" scoped>
  .btn-actions{
    background-color: #fff !important;
    color: #121212;
  }
</style>
