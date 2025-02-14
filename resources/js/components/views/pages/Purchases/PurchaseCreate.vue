<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color: #ad91fd">Ingresar Nueva Compra</h2>
        <v-breadcrumbs class="pa-0 py-2"></v-breadcrumbs>
      </div>
      <v-spacer></v-spacer>
      <v-btn icon>
        <v-icon>mdi-refresh</v-icon>
      </v-btn>
    </div>

    <div class="my-2">
      <div>
        <v-card class="mb-4" light style="padding: 15px">
          <v-row dense class="pa-2">
            <v-col cols="12" sm="4" class="d-flex justify-center align-center">
              <v-autocomplete
                v-model="get_provider"
                :items="items"
                :loading="isLoading"
                :search-input.sync="search_provider"
                hide-no-data
                hide-selected
                item-text="Description"
                item-value="API"
                label="Buscar Proveedor"
                placeholder="Buscar Proveedor"
                prepend-icon="mdi-account-group"
                return-object
              ></v-autocomplete>
            </v-col>
            <v-col cols="12" sm="4" class="d-flex justify-center align-center">
              <v-text-field
                style="width: 90%"
                v-model="searchDocument"
                label="Buscar Proveedor (DNI | RUC)"
                append-icon="mdi-magnify"
                @click:append="searchProviderByDocument()"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="4" class="d-flex justify-center align-center">
              <v-btn
                style="width: 80%"
                depressed
                color="success"
                @click="nuevoProveedor()"
              >
                <v-icon right dark class="ma-2 white--text">mdi-plus</v-icon>
                Nuevo Proveedor
              </v-btn>
            </v-col>
          </v-row>
          <v-divider></v-divider>


          <v-form ref="form" v-model="valid">
            <v-row dense class="pa-2">
              <v-col cols="4">
                <v-text-field
                  v-model="form.header.nombre_proveedor"
                  :rules="[rules.required]"
                  label="Nombre Proveedor"
                  readonly
                ></v-text-field>
              </v-col>
              <v-col cols="2">
                <v-text-field
                  v-model="form.header.tipo_documento"
                  :rules="[rules.required]"
                  label="Tipo Documento"
                  readonly
                ></v-text-field>
              </v-col>
              <v-col cols="2">
                <v-text-field
                  v-model="form.header.nro_doc"
                  :rules="[rules.required]"
                  label="Nro. Documento"
                  readonly
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <v-text-field
                  v-model="form.header.direccion_proveedor"
                  :rules="[rules.required]"
                  label="Dirección Proveedor"
                  readonly
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <v-text-field
                  v-model="form.header.nro_factura"
                  :rules="[rules.required]"
                  label="Nro.Factura"
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <v-text-field
                  v-model="form.header.nro_guia_remision"
                  label="Guía de Remisión"
                ></v-text-field>
              </v-col>
              <v-col cols="4">
                <v-text-field
                  v-model="form.header.fecha_emision"
                  :rules="[rules.required]"
                  label="Fecha de Emision"
                ></v-text-field>
              </v-col>
            </v-row>
            <br />
            <v-row dense class="pa-2">
              <v-col cols="4">
                <v-checkbox
                  v-model="form.header.caja"
                  label="¿Compra salio de caja chica?"
                ></v-checkbox>
              </v-col>
              <v-col cols="4">
                <v-select
                  v-if="form.header.caja"
                  v-model="form.header.rubro"
                  :items="items_caja"
                  label="Caja"
                  item-text="text"
                  item-value="value"
                ></v-select>
              </v-col>
            </v-row>
            <br />
            <v-divider></v-divider>
            <br />
            <v-row dense class="pa-2">
              <v-col cols="9">
                <v-autocomplete
                  v-model="get_producto"
                  :items="items_producto"
                  :search-input.sync="search_productos"
                  hide-no-data
                  hide-selected
                  item-text="Description"
                  item-value="API"
                  label="Buscar Producto / Insumo"
                  placeholder="Buscar Producto / Insumo"
                  prepend-icon="mdi-food-variant"
                  return-object
                ></v-autocomplete>
              </v-col>
            </v-row>
            <v-simple-table>
              <template v-slot:default>
                <thead>
                  <tr>
                    <th style="width: 7%" class="text-left"></th>
                    <th class="text-left">Producto</th>
                    <th class="text-left">Unidad</th>
                    <th style="width: 15%" class="text-left">CNT</th>
                    <th style="width: 15%" class="text-left">P.U (S/.)</th>
                    <th style="width: 15%" class="text-left">P.T (S/.)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in form.detail" :key="index">
                    <td>
                      <v-btn @click="deleteItem(index)" small color="error" icon
                        ><v-icon small> mdi-delete</v-icon></v-btn
                      >
                    </td>
                    <td>{{ item.nombre_producto_insumo }}</td>
                    <td>{{ item.unidad_medida }}</td>
                    <td>
                      <v-text-field
                        v-on:keyup="calcularTotalFila(index)"
                        v-model="item.cantidad"
                        hide-details="auto"
                      ></v-text-field>
                    </td>
                    <td>
                      <v-text-field
                        v-on:keyup="calcularTotalFila(index)"
                        v-model="item.precio_compra_unitario"
                        prefix="S/."
                        hide-details="auto"
                      ></v-text-field>
                    </td>
                    <td>
                      <v-text-field
                        v-model="item.total"
                        readonly
                        hide-details="auto"
                        prefix="S/."
                      ></v-text-field>
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="5" style="text-align: right">
                      <b>Subtotal: </b>
                    </td>
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
                    <td colspan="5" style="text-align: right">
                      <b>IGV (18%): </b>
                    </td>
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
                    <td colspan="5" style="text-align: right">
                      <b>Total: </b>
                    </td>
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

            <v-card-actions class="justify-end">
              <v-btn :disabled="!valid" color="primary" @click="save"
                >Guardar</v-btn
              >
            </v-card-actions>
          </v-form>




        </v-card>

        <v-dialog v-model="dialog" max-width="600px">
          <v-card>
            <v-card-title>
              <span class="headline">{{ formTitle }}</span>
            </v-card-title>
            <v-card-text>
              <v-form ref="form" v-model="valid" lazy-validation>
                <v-container>
                  <v-row>
                    <v-col cols="12" sm="6" md="6">
                      <v-select
                        :items="tipos_doc"
                        label="Tipo Documento"
                        placeholder="Selecciona un tipo de Documento"
                        v-model="editedItem.id_tipo_doc"
                        required
                        item-text="tipo_documento"
                        item-value="id_tipo_doc"
                      ></v-select>
                      <v-text-field
                        v-model="editedItem.nombre"
                        label="Nombre / Razón Social Proveedor"
                        placeholder="Nombre / Razón Social Proveedor"
                        :rules="requiredRules"
                      ></v-text-field>
                      <v-text-field
                        v-model="editedItem.email"
                        label="Correo Electrónico"
                        placeholder="Correo Electrónico"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12" sm="6" md="6"
                      ><!--v-on:keyup.enter="buscarDoc()"-->
                      <v-text-field
                        v-model="editedItem.nro_doc"
                        label="Nro. Documento"
                        placeholder="Nro. Documento"
                        :rules="requiredRules"
                      ></v-text-field>
                      <v-text-field
                        v-if="showAdd"
                        v-model="editedItem.direccion"
                        label="Dirección"
                        placeholder="Dirección"
                        :rules="requiredRules"
                      ></v-text-field>
                      <v-text-field
                        v-model="editedItem.telefono"
                        label="Teléfono"
                        placeholder="Teléfono"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-form>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" dark @click="close">Cancelar</v-btn>
              <v-btn color="primary" :disabled="!valid" @click="saveProveedor">
                Guardar
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  components: {},
  data: () => ({
    breadcrumbs: [
      {
        text: "Compras",
        disabled: false,
        to: "/purchases",
      },
      {
        text: "Ingresar Compra",
        disabled: true,
      },
    ],
    valid: false,
    form: {
      header: {
        fecha_emision: new Date().toISOString().substr(0, 10),
        caja: false,
        rubro: "hotel",
        id_proveedor: "",
        nombre_proveedor: "",
        tipo_documento: "",
        nro_doc: "",
        direccion_proveedor: "",
        nro_factura: "",
      },
      detail: [],
    },
    rules: {
      required: (value) => !!value || "Campo Requerido.",
    },
    descriptionLimit: 60,
    entries: [],
    searchDocument: "",
    isLoading: false,
    search_provider: null,
    get_provider: {},

    search_productos: null,
    get_producto: {
      unidad_medida: {},
    },
    entries_productos: [],
    tipos_doc: [],

    editedIndex: -1,
    editedItem: {
      codigo: "",
      categoria: "",
      nro_doc: "",
    },
    defaultItem: {
      codigo: "",
      categoria: "",
      nro_doc: "",
    },
    dialog: false,
    dialogDelete: false,
    showName: true,
    showAdd: true,
    requiredRules: [(v) => !!v || "Campo obligatorio"],
    emailRules: [
      (v) => !!v || "Campo obligatorio",
      (v) => /.+@.+\..+/.test(v) || "Correo Electrónico debe ser válido",
    ],
    items_caja: [
      { text: "Hotel", value: "hotel" },
      { text: "Restaurante", value: "restaurant" },
    ],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nuevo Proveedor" : "Editar Proveedor";
    },
    items() {
      return this.entries.map((entry) => {
        const Description = entry.nombre;
        return Object.assign({}, entry, { Description });
      });
    },

    items_producto() {
      return this.entries_productos.map((entry) => {
        let Description = "";
        if ("nombre_insumo" in entry) {
          Description = "[Insumo] " + entry.nombre_insumo;
        }
        if ("nombre_producto" in entry) {
          Description = "[Producto] " + entry.nombre_producto;
        }
        return Object.assign({}, entry, { Description });
      });
    },
  },

  watch: {
    search_provider(val) {
      if (this.items.length > 0) return;
      if (this.isLoading) return;
      this.isLoading = true;
      fetch("/api/searchProvider")
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
    get_provider(val) {
      this.form.header.id_proveedor = val.id_proveedor;
      this.form.header.nombre_proveedor = val.nombre;
      this.form.header.tipo_documento = val.tipo_doc.tipo_documento;
      this.form.header.nro_doc = val.nro_doc;
      this.form.header.direccion_proveedor = val.direccion;
    },

    search_productos(val) {
      if (this.items_producto.length > 0) return;
      //if (this.isLoading) return;
      //this.isLoading = true;
      fetch("/api/getProductosInsumos")
        .then((res) => res.json())
        .then((res) => {
          const { count, entries } = res;
          this.count = count;
          this.entries_productos = entries;
        })
        .catch((err) => {
          console.log(err);
        })
        .finally(() => (this.isLoading = false));
    },
    get_producto(val) {
      let item = {};
      if ("nombre_insumo" in val) {
        item.id_insumo = val.id_insumo;
        item.tipo_compra = 1;
        item.nombre_producto_insumo = val.nombre_insumo;
        item.precio_compra_unitario = Number(val.precio).toFixed(2);
        item.total = Number(val.precio).toFixed(2);
      }
      if ("nombre_producto" in val) {
        item.id_producto = val.id_producto;
        item.tipo_compra = 0;
        item.nombre_producto_insumo = val.nombre_producto;
        item.precio_compra_unitario = Number(val.precio_compra).toFixed(2);
        item.total = Number(val.precio_compra).toFixed(2);
      }
      item.cantidad = 1;
      item.stock = val.stock;
      item.unidad_medida = val.unidad_medida.unidad_medida;
      //this.get_producto = {unidad_medida: {}};
      this.form.detail.push(item);
      this.calcularTotal();
    },
  },

  created() {
    this.getTiposDoc();
  },

  methods: {
    close() {
      this.dialog = false;
      this.$refs.form.reset();
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    getTiposDoc() {
      axios
        .get("/api/getTiposDoc")
        .then((response) => {
          this.tipos_doc = response.data;
        })
        .catch((resonse) => {
          console.log("error: " + response.data);
        });
    },
    nuevoProveedor(item) {
      this.dialog = true;
    },
    calcularTotalFila(indx) {
      this.form.detail[indx].total = Number(
        this.form.detail[indx].precio_compra_unitario *
          this.form.detail[indx].cantidad
      ).toFixed(2);
      this.calcularTotal();
    },
    calcularTotal() {
      var sumaTotal;
      sumaTotal = this.form.detail.reduce(function (sum, product) {
        var total_fila = parseFloat(product.total);
        if (!isNaN(total_fila)) {
          return sum + total_fila;
        }
      }, 0);

      if (!isNaN(sumaTotal)) {
        this.form.header.subtotal = parseFloat(sumaTotal / 1.18).toFixed(2);
        this.form.header.igv = parseFloat(
          sumaTotal - this.form.header.subtotal
        ).toFixed(2);
        this.form.header.total = parseFloat(sumaTotal).toFixed(2);
      } else {
        this.form.header.igv = "";
        this.form.header.subtotal = "";
        this.form.header.totall = "";
      }
    },
    deleteItem(indx) {
      this.form.detail.splice(indx, 1);
      this.calcularTotal();
    },
    save() {
      
      if (this.valid && this.form.detail.length > 0) {
          console.log(this.valid)
        this.$store.commit("LOADER", true);
        axios
          .post("/api/purchases", this.form)
          .then((response) => {
            this.$router.push("/purchase_view/" + response.data.id_compra);
            this.$store.commit("LOADER", false);
          })
          .catch((error) => {
            console.log(error);
          });
      } else {
        return;
      }
    },

    validateFields(){
        if(form.header.nombre_proveedor == "") return false;
        if(form.header.tipo_documento == "") return false;
        if(form.header.nro_doc == "") return false;
        if(form.header.direccion_proveedor == "") return false;
        if(form.header.nro_factura == "") return false;
        if(form.header.nombre_proveedor == "") return false;
        return true;
    },
    searchProviderByDocument() {
      this.loading = true;
      let formData = new FormData();
      formData.append("nro_doc", this.searchDocument);
      try {
        //const response = await axios.post('/api/searchProviderByDocument',formData);
        axios
          .post("/api/searchProviderByDocument", formData)
          .then((response) => {
            if (Object.keys(response.data).length === 0) {
              this.$swal({
                toast: true,
                position: "top-end",
                icon: "error",
                title: "Proveedor no encontrado",
                showConfirmButton: false,
                timerProgressBar: true,
                timer: 2500,
              });
            } else {
              this.loading = false;
              this.form.header.id_proveedor = response.data.id_proveedor;
              this.form.header.nombre_proveedor = response.data.nombre;
              this.form.header.tipo_documento =
                response.data.tipo_doc.tipo_documento;
              this.form.header.nro_doc = response.data.nro_doc;
              this.form.header.direccion_proveedor = response.data.direccion;
              this.$swal({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "Proveedor encontrado",
                showConfirmButton: false,
                timerProgressBar: true,
                timer: 2500,
              });
              //this.close();
            }
          });
      } catch (e) {
        console.error(e);
      }
    },
    async saveProveedor() {
      if (this.$refs.form.validate()) {
        this.loading = true;
        if (this.editedIndex > -1) {
          //Object.assign(this.desserts[this.editedIndex], this.editedItem),
          axios
            .put(
              "/api/provider/" + this.editedItem.id_proveedor,
              this.editedItem
            )
            .then((response) => {
              this.loading = false;
              //this.getlist();
              this.close();
              this.$swal({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "Proveedor Actualizado correctamente",
                showConfirmButton: false,
                timerProgressBar: true,
                timer: 2500,
              });
            })
            .finally(() => (this.cargando = false));
        } else {
          const response = await axios.post("/api/provider", this.editedItem);
          if (response.data.allowed.status) {
            let provider = response.data.cliente;
            console.log(provider);
            this.loading = false;
            //this.getlist();

            this.form.header.id_proveedor = provider.id_proveedor;
            this.form.header.nombre_proveedor = provider.nombre;
            this.form.header.tipo_documento = provider.tipo_doc.tipo_documento;
            this.form.header.nro_doc = provider.nro_doc;
            this.form.header.direccion_proveedor = provider.direccion;
            this.close();
            this.$swal({
              toast: true,
              position: "top-end",
              icon: "success",
              title: "Proveedor registrado correctamente",
              showConfirmButton: false,
              timerProgressBar: true,
              timer: 2500,
            });
          } else {
            this.close();
            this.$swal({
              toast: true,
              position: "top-end",
              icon: "error",
              title: "No se pudo registrar al proveedor",
              showConfirmButton: false,
              timerProgressBar: true,
              timer: 2500,
            });
          }


          this.cargando = false;
        }
      } else {
        return;
      }
    },
  },
};
</script>
<style lang="scss" scoped>
.btn-actions {
  background-color: #fff !important;
  color: #121212;
}
</style>
