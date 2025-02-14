<template>
  <div class="flex-grow-1">
    <v-toolbar class="elevation-0" color="transparent">
      <h2 style="color: #ad91fd">Mesas</h2>
      <v-spacer></v-spacer>
      <!--<v-tooltip bottom class="mr-4">
            <template v-slot:activator="{ on, attrs }">
              <v-btn icon v-bind="attrs" v-on="on">
                <v-badge
                  dot
                >
                  <v-icon>mdi-bell-outline</v-icon>
                </v-badge>
              </v-btn>
            </template>
            Notificaciones
          </v-tooltip>-->

      <v-tooltip bottom class="mr-4">
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon v-bind="attrs" v-on="on" @click="limpiarDatosLocal()">
            <v-icon>mdi-reload</v-icon>
          </v-btn>
        </template>
        Actualizar Carta
      </v-tooltip>

      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            icon
            v-bind="attrs"
            v-on="on"
            :to="'/pickupSystem/' + id_sucursal"
          >
            <v-icon>mdi-shopping-outline</v-icon>
          </v-btn>
        </template>
        Registrar Venta
      </v-tooltip>
      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon v-bind="attrs" v-on="on" to="/branchSystem">
            <v-icon>mdi-store-outline</v-icon>
          </v-btn>
        </template>
        Cambiar Sucursal
      </v-tooltip>
      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon v-bind="attrs" v-on="on" @click="getCash">
            <v-icon>mdi-lock-outline</v-icon>
          </v-btn>
        </template>
        Cerrar Caja
      </v-tooltip>
    </v-toolbar>

    <div class="my-2">
      <v-row no-gutters class="d-flex d-sm-none">
          <v-col
            style="padding: 10px"
            cols="12"
            sm="12"
            md="12"
            v-for="(n, i) in zona_mesas"
            :key="i"
          >
            <h4>{{ n.field }}</h4>
            
          <v-select
              v-model="mesa_sele"
              :items="n.groupList"
              label="Seleccione Mesa"
              item-text="nro_mesa"
              item-key="id_mesa"
              item-value="id_mesa"
              return-object
              @change="seleccionMesaDrop"
          ></v-select>
          </v-col>
        </v-row>
      <div v-for="(n, i) in zona_mesas" :key="i">
        <!--<div class="amber lighten-2" style="text-align: center;">
        <h2 class="white--text">{{n.field}}</h2>
      </div>-->
        <v-app-bar
          flat
          dense
          color="rgba(151, 136, 235, 0.18)"
          style="border-radius: 10px"
        >
          <v-toolbar-title
            ><b style="color: #ad91fd">{{ n.field }}</b>
          </v-toolbar-title>
        </v-app-bar>
        <v-row no-gutters>
          <v-col
            style="padding: 10px"
            v-for="(t, index) in n.groupList"
            :key="index"
            cols="12"
            sm="2"
          >
            <v-card
              class="mb-4"
              light
              style="padding: 15px"
              :to="'/posSystem/' + t.id_mesa"
              :color="t.orders[0].status == 0 ? '#d61c1c' : '#F57C00'"
              v-if="t.ordenes_count > 0 && t.ordenes_detalle_count > 0"
            >
              <v-badge
                :content="t.ordenes_count"
                :value="t.ordenes_count"
                overlap
                style="float: right"
              >
              </v-badge>
              <v-card-title
                class="subtitle-2"
                style="padding: 0px; color: #ffffff"
                v-text="t.nro_mesa"
              ></v-card-title>
              <v-img
                src="/assets/images/chair-outline.png"
                style="max-width: 150px"
                class="d-flex align-center ma-auto"
              >
              </v-img>
              <p class="mb-1" style="font-size: 15px; color: white">
                Atención: <b>{{ t.orders[0].usuario.name }}</b>
              </p>
            </v-card>

            <v-card
              class="mb-4"
              light
              style="padding: 15px"
              :to="'/posSystem/' + t.id_mesa"
              v-if="t.ordenes_count == 0 || t.ordenes_detalle_count == 0"
            >
              <v-card-title
                class="subtitle-2"
                style="padding: 0px"
                v-text="t.nro_mesa"
              ></v-card-title>
              <v-img
                src="/assets/images/chair-outline.png"
                style="max-width: 150px"
                class="d-flex align-center ma-auto"
              >
              </v-img>
              <p class="mb-1" style="font-size: 15px; color: transparent">
                Libre
              </p>
            </v-card>
          </v-col>
        </v-row>
      </div>

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
  </div>
</template>
 
<script>
export default {
  components: {},
  data: () => ({
    valid: true,
    searchQuery: "",
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
    page: 1,
    mesas: [],
    zona_mesas: [],
    currentUser: {},
    loading: false,
    editedIndex: -1,
    editedItem: {
      codigo: "",
      zona: "",
    },
    defaultItem: {
      codigo: "",
      zona: "",
    },

    requiredRules: [(v) => !!v || "Campo obligatorio"],
    mask: "!#XXXXXXXX",
    menu: false,
    cerrarCajaDialog: false,
    caja: { usuario: {} },
    id_sucursal: 0,
    mesa_sele: "",
  }),

  mounted() {},

  computed: {},

  watch: {
    options(event) {
      this.itemsperpage = event.itemsPerPage;
      this.getlist(event.page, event.itemsPerPage);
    },
  },

  created() {
    Array.prototype.groupBy = function (field) {
      let groupedArr = [];
      this.forEach(function (e) {
        //look for an existent group
        let group = groupedArr.find((g) => g["field"] === e[field]);
        if (group == undefined) {
          //add new group if it doesn't exist
          group = { field: e[field], groupList: [] };
          groupedArr.push(group);
        }

        //add the element to the group
        group.groupList.push(e);
      });

      return groupedArr;
    };
    this.id_sucursal = this.$route.params.id;
    this.$store.commit("LOADER", true);
    this.getMesas();
  },

  methods: {
    limpiarDatosLocal() {
      //resetear todos los valores
      localStorage.setItem("categorias", null);
      localStorage.setItem("listamozos", null);
      localStorage.setItem("metodospago", null);
      localStorage.setItem("monedas", null);
      localStorage.setItem("tiposcomprobante", null);
      localStorage.setItem("productos", null);
      localStorage.setItem("tiposdoc", null);

      this.$swal({
        icon: "success",
        title: "Correcto",
        text: "Los datos han sido actualizados!",
      });
    },
    getMesas() {
      axios
        .get("/api/getTableByBranch/" + this.$route.params.id)
        .then((response) => {
          this.mesas = response.data;
          for (let index = 0; index < this.mesas.length; index++) {
            this.mesas[index].name_zona = this.mesas[index].zona.nombre;
          }
          this.zona_mesas = this.mesas.groupBy("name_zona");
          console.log(this.zona_mesas);
          this.$store.commit("LOADER", false);
        })
        .catch((resonse) => {
          console.log("error: " + response.data);
          this.$store.commit("LOADER", false);
        });
    },

    async getCash() {
      try {
        const detail = await API.cash.detail(
          this.$route.params.id,
          "restaurant",
          1
        );
        this.caja = detail.data;
        this.cerrarCajaDialog = true;
        console.log(detail);
      } catch (error) {
        console.error(error.response.status);
        this.dialogDelete = false;
      }
    },

    async closeCash() {
      try {
        const detail = await API.cash.update(this.caja.id_caja, {
          detalle: this.caja.detalle,
        });
        this.caja = detail.data;
        this.cerrarCajaDialog = false;
        this.$router.push("/branchSystem");
      } catch (error) {
        console.error(error.response.status);
        this.dialogDelete = false;
      }
    },
    goToMesa() {
      console.log("qewqe");
      //this.$router.push('/posSystem/'+id_mesa)
    },
    seleccionMesaDrop() {
      let mesa = this.mesa_sele;
      //console.log(this.mesa_sele);
      this.$router.push("/posSystem/" + mesa.id_mesa);
    },
  },
};
</script>
<style lang="scss" scoped>
.btn-actions {
  background-color: #fff !important;
  color: #121212;
}
* {
  text-transform: none !important;
  //font-size: 12px !important;
  //font-family:'Quicksand', sans-serif  !important;
}
</style>
