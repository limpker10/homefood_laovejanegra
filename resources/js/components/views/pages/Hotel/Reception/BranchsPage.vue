<template>
  <div class="flex-grow-1">
    <v-toolbar
          class="elevation-0"
          color="transparent"
        >
          <v-spacer></v-spacer>
          <h2 style="color:  #AD91FD">Elige una Sucursal</h2> 
          <v-spacer></v-spacer>
    </v-toolbar>

    <div class="my-2">
      <v-card
        class="mx-auto"
        max-width="400"
        v-for="(branch, index) in branches" :key="index"
      >
        <v-list-item three-line @click="openPOS(index, branch.id_sucursal, branch.id_caja)">
          
          <v-list-item-avatar
            tile
            size="80"
          >
            <v-img :src="'assets/images/reception.png'"></v-img>
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title class="headline mb-1">
              {{branch.nombre_sucursal}}
            </v-list-item-title>
            <v-list-item-subtitle>
              {{branch.telefono}} / {{branch.direccion_comercial}}</v-list-item-subtitle>
          </v-list-item-content>

          <v-list-item-action style="margin: auto; text-align: center">
                <v-list-item-action-text v-if="branch.id_caja"  style="color: #4CAF50" >Abierto
                  <v-icon
                   color="success"
                  >  mdi-lock-open-outline
                  </v-icon>
                </v-list-item-action-text>
                <v-list-item-action-text v-else style="color: #FF5252">Cerrado
                  <v-icon
                     color="error"
                  >  mdi-lock-outline
                  </v-icon>
                </v-list-item-action-text>
            </v-list-item-action>

        </v-list-item>
      </v-card>
    </div>

    <v-dialog
          v-model="dialog"
          max-width="400px"
        >
          <v-card>
            <v-form
              ref="form"
              v-model="valid"
              lazy-validation
            >
              <v-card-title>
                <span class="headline">Abrir Caja</span>
              </v-card-title>
              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12" sm="12"  md="12" >
                      <v-text-field
                         v-model="editedItem.monto_apertura" 
                         label="Monto de Apertura" 
                         placeholder="Monto de Apertura" 
                         :rules="requiredRules"
                         type="number"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" dark @click="dialog = false" >Cancelar</v-btn>
                <v-btn color="primary" @click="openCash" :disabled="!valid"> Guardar </v-btn>
              </v-card-actions>
            </v-form>
          </v-card>
        </v-dialog>
        <v-dialog v-model="pendingDialog" max-width="500">
      <v-card>
        <v-card-title class="text-h5">
          <strong> CAJAS SIN CERRAR </strong>
        </v-card-title>

        <v-card-text>
          <v-list>
            <v-list-item v-for="item in pendingSucursal" :key="item.title">
              <v-list-item-content>
                <v-list-item-title
                  v-text="item.fecha_apertura"
                ></v-list-item-title>
              </v-list-item-content>

              <v-list-item-action>
                <v-btn class="mx-2" dark small color="primary" @click="getCash(item.id_caja)">
                  Cerrar
                  <v-icon dark> mdi-close </v-icon>
                </v-btn>
              </v-list-item-action>
            </v-list-item>
          </v-list>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="green darken-1" @click="pendingDialog = false">
            Cancelar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="cerrarCajaDialog" max-width="500px" persistent>
      <v-card>
        <v-card-title class="subtitle-1">Cierre de Caja</v-card-title>
        <v-card-text>
          <v-row dense>
            <v-col cols="4" class="text-center">
              -- Abierto por
              <h4>{{ caja.usuario.name }}</h4>
            </v-col>
            <v-col cols="4" class="text-center">
              -- Caja Chica
              <h4>{{ caja.monto_apertura }}</h4>
            </v-col>
            <v-col cols="4" class="text-center">
              -- Egresos de Caja
              <h4>{{ caja.egresos }}</h4>
            </v-col>
          </v-row>
          <br />
          <v-divider></v-divider>
          <br />
          <h3>Resumen de Pago</h3>
          <v-simple-table>
            <template v-slot:default>
              <thead>
                <tr>
                  <th class="text-left">Método de pago</th>
                  <th class="text-left">Ingresos</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in caja.detalle" :key="index">
                  <td>{{ item.medio_pago }}</td>
                  <td>{{ item.monto }}</td>
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
          <v-btn color="primary" @click="closeCash">Cerrar Caja</v-btn>
          <v-spacer></v-spacer>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
 
<script>
export default {
  components: {
  },
  data: () => ({
    cerrarCajaDialog: false,
    caja: { usuario:{} },
    pendingDialog: false,
    pendingSucursal: [],
    valid: true,
    searchQuery: '',
    dialog: false,
    dialogDelete: false,
    desserts: [],
    sucursales: [],
    editedIndex: -1,
    data: {
        data: [],
        total: 0,
    },
    options: {},
    searchData: {},
    itemsperpage: 25,
    page:1,
    mesas: [],
    zona_mesas: [],
    currentUser: {},
    loading:false,
    editedIndex: -1,
      editedItem: {
        id_sucursal:'',
        monto_apertura:0,
        fecha_apertura:"",
        rubro:1
      },
      defaultItem: {
        codigo: '',
        zona:''
      },

    requiredRules: [
        v => !!v || 'Campo obligatorio',
      ],
		mask: '!#XXXXXXXX',
		menu: false,
    branches:[],
    id_sucursal:0,
    pendientes: [],
    selectedSucursal:'',
    selectedCaja:'',
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
    this.$store.commit('LOADER',true);
    this.getBranchUser();
  },

  methods: {
    async getCash(id) {
      try {
        const detail = await API.cash.detail(id, "hotel", 2);
        this.caja = detail.data;
        this.selectedCaja = id;
        this.cerrarCajaDialog = true;
        //console.log(detail);
      } catch (error) {
        console.error(error.response.status);
        //this.dialogDelete = false;
      }
    },
    
    async getBranchUser( ){
      try {
        const branches = await API.pos.branch("hotel");
        this.branches = branches.data.data;
        this.pendientes = branches.data.pendientes;
        console.log(this.pendientes);
        this.$store.commit("LOADER", false);
      } catch (error) {
        console.error(error.response.status);
        this.$store.commit("LOADER", false);
      }
    },
    
    openPOS(index, id_sucursal, id_caja){
      localStorage.setItem("SYSTEM_ID_BRANCH", id_sucursal);
      if (id_caja == null) {
        let sinCerrar = this.pendientes.filter(
          (e) => e.id_sucursal == id_sucursal
        );
        if (sinCerrar.length == 0) {
          this.editedItem.id_sucursal = id_sucursal;
          this.dialog = true;
          this.id_sucursal = id_sucursal;
        } else {
          this.pendingSucursal = sinCerrar;
          this.pendingDialog = true;
          console.log(sinCerrar);
        }
      } else {
        this.$router.push("/reception/" + id_sucursal);
      }
    },
    async closeCash() {
      try {
        const detail = await API.cash.update(this.caja.id_caja, {
          detalle: this.caja.detalle,
        });
        this.caja = detail.data;
        this.cerrarCajaDialog = false;
        this.pendingDialog = false;
        //this.$router.push("/branchHotelSystem");
        //this.pendingSucursal = this.pendingSucursal.filter(e => e.id_caja !== this.selectedCaja);
        this.getBranchUser();
        console.log(this.pendingSucursal,this.selectedCaja);
      } catch (error) {
        console.error(error.response.status);
        this.dialogDelete = false;
      }
    },
    async openCash(id_sucursal){
      try{
            await API.cash_hotel.create(this.editedItem);
            this.$router.push('/reception/'+this.id_sucursal) /*+this.editedItem.id_sucursal*/
        }catch(error){
            console.error(error.response.status);
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
    //font-size: 12px !important;
    //font-family:'Quicksand', sans-serif  !important;
    }
</style>
