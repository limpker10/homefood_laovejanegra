<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color:  #AD91FD">Ver Compra </h2> 
        <v-breadcrumbs :items="breadcrumbs" class="pa-0 py-2"></v-breadcrumbs>
      </div>
    </div>

    <div class="my-2">
    <div>
        <v-card class="mb-4" light style="padding: 15px">
                <v-card-title primary-title>
                    Nro. Compra: #{{String(id_compra).padStart(8, "0")}}
                </v-card-title>
                <v-row dense class="pa-2">
                    <v-col cols="4">
                        <v-text-field
                            v-model="form.header.nombre_proveedor"
                            label="Nombre Proveedor"
                            readonly
                        ></v-text-field>
                    </v-col>
                    <v-col cols="2">
                        <v-text-field
                            v-model="form.header.tipo_documento"
                            label="Tipo Documento"
                            readonly
                        ></v-text-field>
                    </v-col>
                    <v-col cols="2">
                        <v-text-field
                            v-model="form.header.nro_doc"
                            label="Nro. Documento"
                            readonly
                        ></v-text-field>
                    </v-col>
                    <v-col cols="4">
                        <v-text-field
                            v-model="form.header.direccion_proveedor"
                            label="Dirección Proveedor"
                            readonly
                        ></v-text-field>
                    </v-col>
                    <v-col cols="4">
                        <v-text-field
                            v-model="form.header.nro_factura"
                            label="Nro.Factura"
                            readonly
                        ></v-text-field>
                    </v-col>
                    <v-col cols="4">
                        <v-text-field
                            v-model="form.header.nro_guia_remision"
                            label="Guía de Remisión"
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
                                <th class="text-left">Producto</th>
                                <th style="width:10%;" class="text-left">CNT</th>
                                <th style="width:15%;" class="text-left">P.U (S/.)</th>
                                <th style="width:15%;" class="text-left">P.T (S/.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                            v-for="(item,index) in form.detail"
                            :key="index"
                            >
                                <td>{{ index+1 }}</td>
                                <td>{{ item.nombre_producto_insumo }}</td>
                                <td>{{ item.cantidad }}</td>
                                <td>S/.{{ item.precio_compra_unitario }}</td>
                                <td>S/.{{ item.total }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align:right;"><b>Subtotal: </b></td>
                                <td colspan="2">
                                    <v-text-field
                                        v-model="form.header.subtotal"
                                        readonly
                                        hide-details="auto"
                                        prefix="S/."
                                    ></v-text-field>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" style="text-align:right;"><b>IGV (18%): </b></td>
                                <td colspan="2">
                                    <v-text-field
                                        v-model="form.header.igv"
                                        readonly
                                        hide-details="auto"
                                        prefix="S/."
                                    ></v-text-field>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" style="text-align:right;"><b>Total: </b></td>
                                <td colspan="2">
                                    <v-text-field
                                        v-model="form.header.total"
                                        readonly
                                        hide-details="auto"
                                        prefix="S/."
                                    ></v-text-field>
                                </td>
                            </tr>
                        </tfoot>
                    </template>
                </v-simple-table>
        </v-card>
    
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
                text: 'Compras',
                disabled: false,
                to: '/purchases'
            },
            {
                text: 'Ver Compra',
                disabled: true,
            }
        ],
        form:{
            header:{ fecha_emision: new Date().toISOString().substr(0, 10), },
            detail:[]
        },
        id_compra:null,
        isLoading: false,
        
  }),


  computed: {
    
  },

  watch: {
    
  },

  created () {
      this.id_compra = this.$route.params.id;
      this.getPurchase();
      this.getPurchaseDetail();
  },

  methods: {
      getPurchase(){
          this.$store.commit('LOADER',true);
           axios.get('/api/purchases/'+this.id_compra)
            .then(response => {
                response.data.created_at = response.data.created_at.split('T')[0];
                response.data.tipo_documento = response.data.proveedor.tipo_doc.tipo_documento;
                this.form.header = response.data;
                this.$store.commit('LOADER',false);
            }).catch((error)=>{
                console.log(error)
            });
      },
      getPurchaseDetail(){
           axios.get('/api/purchases/detail/'+this.id_compra)
            .then(response => {
                this.form.detail = response.data;
            }).catch((error)=>{
                console.log(error)
            });
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
