<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
        <h2 style="color:  #AD91FD">Productos</h2>
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
        <v-row dense class="pa-2 align-center">
            <v-col cols="4">
                <v-text-field
                v-model="searchQuery"
                append-icon="mdi-magnify"
                class="flex-grow-1 mr-1"
                single-line
                hide-details
                placeholder="Buscar por nombre …"
                @keyup.enter="searchUser(searchQuery)"
                @keyup.delete="researchUser(searchQuery)"
                ></v-text-field>
            </v-col>
            <v-col cols="5">
              <v-menu offset-y left>
                <template v-slot:activator="{ on }">
                  <transition name="slide-fade" mode="out-in">
                    <v-btn
                      v-show="selectedItems.length > 0"
                      v-on="on"
                      color="secondary"
                    >
                      Acciones
                      <v-icon right>mdi-menu-down</v-icon>
                    </v-btn>
                  </transition>
                </template>
                <v-list dense>
                  <v-list-item @click="activegroup(0)">
                    <v-list-item-title>Deshabilitar</v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="activegroup(1)">
                    <v-list-item-title>Habilitar</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </v-col>
            <v-col cols="3" style="text-align: right;">
             <v-btn small color="primary" class="mr-2" @click="openDialog()">
              Añadir Productos
              <v-icon>mdi-plus</v-icon>
            </v-btn>
          </v-col>
        </v-row>
        <br>
        <v-data-table
          v-model="selectedItems"
          item-key="id_producto"
          show-select
          :headers="headers"
          :items="data.data"
          class="elevation-1"
          :server-items-length="data.total"
          :page="page"
          :options.sync="options"
          :items-per-page="itemsperpage"
          :footer-props="{
              itemsPerPageOptions: [25,50,100,1000],
              itemsPerPageText:'items por página'
          }"
        >
            <template v-slot:[`item.actions`]="{ item }">
                <v-btn small icon v-if="item.tipo_producto_combo==2"  @click="getDetail(item.id_producto)">
                    <v-icon small> mdi-food</v-icon>
                </v-btn>
                <v-btn small icon @click="verProducto(item)">
                    <v-icon small> mdi-information</v-icon>
                </v-btn>

                <!--<v-btn small icon @click="editItem(item)">
                    <v-icon small> mdi-office-building-marker</v-icon>
                </v-btn>-->
                <v-btn small icon @click="editItem(item)">
                    <v-icon small> mdi-pencil</v-icon>
                </v-btn>
                <v-btn small icon @click="deleteItem(item)">
                    <v-icon small> mdi-delete</v-icon>
                </v-btn>
            </template>
            <template v-slot:[`item.tipo_producto_combo`]="{ item }">
                <div v-if="item.tipo_producto_combo==1">
                   Plato
                </div>
                <div v-if="item.tipo_producto_combo==2">
                   Combo
                </div>
                <div v-if="item.tipo_producto_combo==3">
                   Producto
                </div>
            </template>
            <template v-slot:[`item.id_categoria`]="{ item }">
                <div>
                    {{item.categoria.nombre}}
                </div>
            </template>
            <template v-slot:[`item.precio`]="{ item }">
                <div>
                    {{Number(item.precio).toFixed(2)}}
                </div>
            </template>
            <template v-slot:[`item.imprimir_cocina`]="{ item }">
                <div class="text-center">
                  <v-icon color="success" v-if="item.imprimir_cocina == 1" medium>
                    mdi-checkbox-marked
                  </v-icon>
                  <v-icon v-else medium> mdi-checkbox-blank-outline </v-icon>
                </div>
            </template>
            <template v-slot:[`item.imprimir_barra`]="{ item }">
                <div class="text-center">
                  <v-icon color="success" v-if="item.imprimir_barra == 1" medium>
                    mdi-checkbox-marked
                  </v-icon>
                  <v-icon v-else medium> mdi-checkbox-blank-outline </v-icon>
                </div>
            </template>
            <template v-slot:[`item.id_unidad_medida`]="{ item }">
                <div>
                    {{item.unidad_medida.unidad_medida}}
                </div>
            </template>
            <template v-slot:[`item.activo`]="{ item }">
                <v-btn  icon @click="activeItem(item,0)" v-if="item.activo==1">
                    <v-icon color="primary"> mdi-checkbox-marked</v-icon>
                </v-btn>
                <v-btn  icon @click="activeItem(item,1)" v-else>
                    <v-icon > mdi-checkbox-blank-outline</v-icon>
                </v-btn>
            </template>
        </v-data-table>

        <v-dialog
          v-model="dialog"
          max-width="1000px"
          @click:outside="close"
        >
          <v-card>
                <v-form
                  ref="form"
                  v-model="valid"
                  lazy-validation
                >
                  <v-card-title>
                    <span class="headline">{{ formTitle }}</span>
                  </v-card-title>
                  <v-card-text>
                    <v-row>
                      <v-col cols="12" sm="8" md="8">
                        <v-container>
                            <v-row>
                            <v-col cols="12" sm="6"  md="6" >
                                <v-select
                                  :items="tipo_producto"
                                  label="Tipo Producto"
                                  placeholder="Selecciona un tipo"
                                  v-model="editedItem.tipo_producto_combo"
                                  :rules="requiredRules">
                                </v-select>
                                <v-text-field
                                  v-model="editedItem.nombre_producto"
                                  label="Nombre Producto"
                                  placeholder="Nombre Producto"
                                  :rules="requiredRules"
                                ></v-text-field>
                                <v-select
                                  :items="categorias"
                                  label="Categoría"
                                  placeholder="Selecciona una Categoria"
                                  v-model="editedItem.id_categoria"
                                  :rules="requiredRules"
                                  item-text="nombre"
                                  item-value="id_categoria"></v-select>
                                <v-text-field
                                  v-model="editedItem.precio"
                                  label="Precio Venta"
                                  placeholder="Precio Venta"
                                  :rules="requiredRules"
                                ></v-text-field>
                                <v-text-field
                                  v-model="editedItem.precio_compra"
                                  label="Precio Compra"
                                  placeholder="Precio Compra"
                                  :disabled="!(editedItem.tipo_producto_combo==3)"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" sm="6"  md="6" >
                                <v-text-field
                                  v-model="editedItem.codigo_producto"
                                  label="Código" placeholder="Código"
                                  :rules="requiredRules"
                                ></v-text-field>
                                <v-text-field
                                  :disabled="!(editedItem.tipo_producto_combo==3)"
                                  v-model="editedItem.stock_minimo"
                                  label="Stock Mínimo"
                                  placeholder="Stock Mínimo"
                                ></v-text-field>
                                <v-text-field
                                  :disabled="!(editedItem.tipo_producto_combo==3) || isEdited"
                                  v-model="editedItem.stock"
                                  label="Stock"
                                  placeholder="Stock"
                                ></v-text-field>
                                <v-select
                                  :items="unidades"
                                  label="Unidad de Medida"
                                  placeholder="Selecciona una Unidad de Medida"
                                  v-model="editedItem.id_unidad_medida"
                                  item-text="unidad_medida"
                                  item-value="id_unidad_medida"
                                  :rules="requiredRules"
                                ></v-select>

                                <v-switch
                                    v-model="editedItem.imprimir_cocina"
                                    inset
                                    label="Enviar a Cocina"
                                ></v-switch>
                                <v-switch
                                    v-model="editedItem.imprimir_barra"
                                    inset
                                    label="Enviar a Barra"
                                ></v-switch>
                            </v-col>
                            <v-col cols="12" sm="12"  md="12" >
                                <v-select
                                    v-model="editedItem.recetas_productos"
                                    :items="recetas"
                                    chips
                                    label="Recetas"
                                    multiple
                                    item-text="nombre_receta"
                                    item-value="id_receta"
                                ></v-select>
                            </v-col>
                            <v-col cols="6" v-if="this.editedIndex > -1">
                                <v-img
                                v-if="editedItem.imagen_producto!=null"
                                :src="'../storage/files/uploads/'+editedItem.imagen_producto.path+'/'+editedItem.imagen_producto.filename"
                                aspect-ratio="1.7"
                                contain
                                ></v-img>

                                <v-img
                                v-else
                                :src="img_src"
                                aspect-ratio="1.7"
                                contain
                                ></v-img>
                            </v-col>
                            <v-col cols="6" v-if="this.editedIndex > -1">
                                <v-text-field
                                  readonly
                                  v-if="editedItem.imagen_producto!=null"
                                  v-model="editedItem.imagen_producto.filename"
                                  label="Imágen"
                                  placeholder="Imágen"
                                ></v-text-field>
                                <v-btn small color="primary" class="mr-2" @click="editImageProduct(editedItem.imagen_producto)">
                                    Editar
                                    <v-icon>mdi-image-edit</v-icon>
                                </v-btn>
                            </v-col>
                            <v-col cols="6" v-if="!(this.editedIndex > -1)">
                                <v-img
                                :src="img_src"
                                aspect-ratio="1.7"
                                contain
                                ></v-img>
                            </v-col>
                            <v-col cols="6" v-if="!(this.editedIndex > -1)">
                                <template>
                                    <v-file-input
                                        v-model="image"
                                        ref="files"
                                        v-on:change="handleFiles()"
                                        accept="image/*"
                                        label="Imágen del producto"
                                        prepend-icon="mdi-file-image-outline"
                                    ></v-file-input>
                                </template>
                            </v-col>
                            </v-row>
                        </v-container>
                      </v-col>
                      <v-col cols="12" sm="4" md="4">
                        <p>Sucursal</p>
                        <v-checkbox class="my-0 py-0"
                          v-model="sucursales_selected_all"
                          label="Todas las sucursales"
                        ></v-checkbox>
                        <v-list  class="my-0 py-0">
                          <v-list-item class="my-0 py-0"
                            v-for="item in sucursales"
                            :key="item.id_sucursal"
                          >
                            <v-checkbox class="my-0 py-0"
                              v-model="sucursales_selected"
                              :label="item.nombre_sucursal"
                              :value="item.id_sucursal"
                            ></v-checkbox>
                          </v-list-item>
                        </v-list>
                      </v-col>
                    </v-row>
                  </v-card-text>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="error" dark @click="close" >Cancelar</v-btn>
                    <v-btn color="primary" :disabled="!valid" @click="save"> Guardar </v-btn>
                  </v-card-actions>
                </v-form>
          </v-card>
        </v-dialog>

        <v-dialog v-model="dialogDelete" max-width="300px">
          <v-card>
            <v-card-title class="subtitle-1">¿Desea eliminar este Producto?</v-card-title>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" dark @click="closeDelete">Cancelar</v-btn>
              <v-btn color="primary" @click="deleteItemConfirm">Aceptar</v-btn>
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>



        <!--Combinaciones-->
        <v-dialog
          v-model="dialogDetail"
          max-width="500px"
        >
          <v-card>
            <v-card-title>
              <span class="headline">Combinaciones</span>
            </v-card-title>
            <v-card-text>
              <v-container>
                  <v-row dense class="pa-2 align-center">
                      <v-col cols="8">
                      </v-col>
                      <v-col cols="4" style="text-align: right;">
                      <v-btn small color="primary" class="mr-2" @click="dialogAddDetail=true;">
                      Añadir Producto
                      <v-icon>mdi-plus</v-icon>
                      </v-btn>
                  </v-col>
                  </v-row>
                  <v-simple-table>
                      <template v-slot:default>
                      <thead>
                          <tr>
                          <th class="text-left">Producto</th>
                          <th class="text-left">Cantidad</th>
                          <th class="text-left">Acciones</th>
                          </tr>
                      </thead>
                      <tbody>
                          <tr
                          v-for="(item,index) in recipe_detail"
                          :key="index"
                          >
                              <td>{{ item.producto.nombre_producto }}</td>
                              <td>{{ item.cantidad }}</td>
                              <td>
                                  <v-btn small icon @click="editInsumo(item)">
                                      <v-icon small> mdi-pencil</v-icon>
                                  </v-btn>
                                  <v-btn small icon @click="deleteInsumo(item)">
                                      <v-icon small> mdi-delete</v-icon>
                                  </v-btn>
                              </td>
                          </tr>
                      </tbody>
                      </template>
                  </v-simple-table>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" dark @click="dialogDetail=false;" >Cerrar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <!--Add Combinaciones-->
        <v-dialog
          v-model="dialogAddDetail"
          max-width="500px"
        >
          <v-card>
            <v-form
              ref="formcombo"
              v-model="valid_combo"
              lazy-validation
            >
              <v-card-title>
                <span class="headline">{{ formTitleInsumo }}</span>
              </v-card-title>
              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="6" sm="12"  md="6" >
                      <v-autocomplete
                      v-if="editedIndexInsumo==-1"
                      v-model="model"
                      :items="items"
                      :loading="isLoading"
                      :search-input.sync="search"
                      hide-no-data
                      hide-selected
                      item-text="Description"
                      item-value="API"
                      label="Producto"
                      placeholder="Buscar Producto"
                      return-object
                      :rules="requiredRules"
                      ></v-autocomplete>
                      <v-text-field
                      v-else
                      v-model="model.nombre_producto"
                      label="Producto"
                      placeholder="Producto"
                      disabled
                      ></v-text-field>
                    </v-col>
                    <v-col cols="6" sm="12"  md="6" >
                      <v-text-field
                      v-model="model.cantidad"
                      label="Cantidad"
                      placeholder="Cantidad"
                      :rules="requiredRules"
                      ></v-text-field>
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="error" dark @click="dialogAddDetail=false" >Cancelar</v-btn>
                <v-btn color="primary" :disabled="!valid_combo" @click="saveInsumo"> Guardar </v-btn>
              </v-card-actions>
            </v-form>
          </v-card>
        </v-dialog>

        <v-dialog v-model="dialogDeleteCombinacion" max-width="300px">
          <v-card>
            <v-card-title class="subtitle-1">¿Desea eliminar este Producto de la Combinación?</v-card-title>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" dark @click="dialogDeleteCombinacion=false">Cancelar</v-btn>
              <v-btn color="primary" @click="deleteInsumoConfirm">Aceptar</v-btn>
              <v-spacer></v-spacer>
            </v-card-actions>
          </v-card>
        </v-dialog>


        <!--Ver Producto-->
        <v-dialog
          v-model="dialogItem"
          max-width="800px"
        >
          <v-card>
            <v-card-title>
              <span class="headline"> Ver Producto </span>
            </v-card-title>
            <v-card-text v-if="dialogItem">
                <v-tabs v-model="tab" :show-arrows="false" background-color="transparent">
                    <v-tab to="#tabs-account">Información</v-tab>
                    <v-tab to="#tabs-information" v-if="item_view.tipo_producto_combo==2">Combinación</v-tab>
                </v-tabs>

                <v-tabs-items v-model="tab">
                    <v-tab-item value="tabs-account">
                        <div class="d-flex flex-column flex-md-row">
                            <div class="flex-grow-1 pt-1 pa-sm-1" style="text-align: center">
                                <v-img
                                v-if="item_view.imagen_producto!=null"
                                :src="'../storage/files/uploads/'+item_view.imagen_producto.path+'/'+item_view.imagen_producto.filename"
                                aspect-ratio="1.7"
                                style="min-width: 200px"
                                contain
                                class="img-radius"
                                ></v-img>
                            </div>
                            <div
                                class="flex-grow-1 pt-2 pa-sm-2"
                                style="padding: 16px !important"
                            >
                                <v-row>
                                    <v-col cols="12" md="6" sm="6" class="col-form">
                                        <v-text-field
                                        v-model="item_view.nombre_producto"
                                        label="Nombre Producto"
                                        placeholder="Nombre Producto"
                                        readonly
                                        >
                                        </v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6" sm="6" class="col-form">
                                        <v-text-field
                                        v-model="item_view.categoria.nombre"
                                        label="Categoria"
                                        placeholder="Categoria"
                                        readonly
                                        >
                                        </v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6" sm="6" class="col-form">
                                        <v-text-field
                                        v-model="item_view.unidad_medida.simbolo"
                                        label="Unidad Medida"
                                        placeholder="Unidad Medida"
                                        readonly
                                        >
                                        </v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6" sm="6" class="col-form">
                                        <v-text-field
                                        v-model="item_view.precio"
                                        label="Precio"
                                        placeholder="Precio"
                                        readonly
                                        >
                                        </v-text-field>
                                    </v-col>
                                </v-row>
                            </div>
                            </div>
                    </v-tab-item>

                    <v-tab-item value="tabs-information">
                        <v-simple-table>
                        <template v-slot:default>
                        <thead>
                            <tr>
                            <th class="text-left">Producto</th>
                            <th class="text-left">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                            v-for="(item,index) in recipe_detail"
                            :key="index"
                            >
                                <td>{{ item.producto.nombre_producto }}</td>
                                <td>{{ item.cantidad }}</td>
                            </tr>
                        </tbody>
                        </template>
                    </v-simple-table>
                    </v-tab-item>
                </v-tabs-items>

            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="error" dark @click="dialogItem=false;" >Cerrar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <!--Editar Imagen-->
        <v-dialog v-model="edit_image_dialog" max-width="500px">
          <v-card>
            <v-card-title> Editar Imágen </v-card-title>
            <v-card-text>
              <v-img
                v-if="edited_imagen!=null"
                :src="edited_imagen"
                aspect-ratio="1.7"
                contain
                ></v-img>
                <v-img
                v-else
                :src="img_src"
                aspect-ratio="1.7"
                contain
              ></v-img>
              <template>
                  <v-file-input
                      v-model="image_edit"
                      ref="files"
                      v-on:change="getEditImagePreviews()"
                      accept="image/*"
                      label="Imágen del producto"
                      prepend-icon="mdi-file-image-outline"
                  ></v-file-input>
              </template>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="gray" text>Cancelar</v-btn>
              <v-btn color="primary" text @click="submitEditFiles">Cambiar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

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
    searchQuery: '',
    dialog: false,
    dialogDelete: false,
    dialogDetail: false,
    dialogAddDetail: false,
    dialogDeleteCombinacion:false,
    dialogItem: false,
    headers: [
      { text: 'ID', sortable: false, value: 'id_producto' },
      { text: 'Código', sortable: false, value: 'codigo_producto' },
      { text: 'Nombre', sortable: false, value: 'nombre_producto' },
      { text: 'Categoría', sortable: false, value: 'id_categoria' },
      { text: 'Unidad Medida', sortable: false, value: 'id_unidad_medida' },
      { text: 'Precio', sortable: false, value: 'precio' },
      { text: 'Tipo Producto', sortable: false, value: 'tipo_producto_combo' },
      { text: 'Cocina', sortable: false, value: 'imprimir_cocina' },
      { text: 'Barra', sortable: false, value: 'imprimir_barra' },
      { text: 'Activo', value: 'activo', sortable: false, align:'right' },
      { text: 'Acciones', value: 'actions', align: 'center', sortable: false, align:'right' }
    ],
    tipo_producto: [
      { text: 'Plato', value: 1 },
      { text: 'Combo', value: 2 },
      { text: 'Producto', value: 3 }
    ],
    tab:0,
    files: [],
    img_src:"data:image/jpeg;base64,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",
    desserts: [],
    //editedIndex: -1,
    valid: true,
    valid_combo: true,
    data: {
        data: [],
        total: 0,
    },
    options: {},
    searchData: {},
    itemsperpage: 25,
    page:1,
    recetas_productos: [],
    productos: [],
    categorias: [],
    unidades: [],
    recetas: [],
    loading:false,
    autocomplteLoading:false,
    showName:true,
    showAdd:true,
    editedIndex: -1,
      editedItem: {
        recetas_productos:[],
        cocina:1,
      },
      defaultItem: {
        image:null
      },
    editedInsumo: {
      },

    editedIndexInsumo: -1,
    image:null,
    requiredRules: [
        v => !!v || 'Campo obligatorio',
      ],
    emailRules: [
      v => !!v || 'Campo obligatorio',
      v => /.+@.+\..+/.test(v) || 'Correo Electrónico debe ser válido',
    ],

    descriptionLimit: 60,
    entries: [],
    recipe_detail: [],
    isLoading: false,
    model: {
        unidad_medida: {}
    },
    search: null,
    id_detail:null,
    item_view:{},

    currentUser: {},
    sucursales: [],
    sucursales_selected: [],
    selectedItems:[],
    selectAll: false,
    groupid: [],

    edit_image_dialog: false,
    edited_imagen:'',
    image_edit:null,
    isEdited:false,
  }),

  computed: {
    formTitle () {
        return this.editedIndex === -1 ? 'Nuevo Producto' : 'Editar Producto'
    },

    formTitleInsumo () {
        return this.editedIndexInsumo === -1 ? 'Agregar Producto - Combinación' : 'Editar Producto - Combinación'
    },
    fields() {
      if (!this.model) return [];
      return Object.keys(this.model).map((key) => {
        return {
          key,
          value: this.model[key],
        };
      });
    },
    items() {
      return this.entries.map((entry) => {
        const Description = entry.nombre_producto;
      return Object.assign({}, entry, { Description });
      });
    },

    sucursales_selected_all: {
        set(val){
            this.sucursales_selected = [];
            if (val == true) {
                this.sucursales_selected = this.sucursales.map((s) => s.id_sucursal);
            }
        },
        get(){
            return this.sucursales_selected.length === this.sucursales.length;
        }
    },
  },


  watch: {
    selectedItems(val) {
      this.groupid = this.selectedItems.map(({ id_producto }) => id_producto)
    },

    options(event) {
      this.itemsperpage=event.itemsPerPage;
      this.getlist(event.page, event.itemsPerPage);
    },

    search(val) {
      if (this.items.length > 0) return;
      if (this.isLoading) return;
      this.isLoading = true;
      fetch("/api/getProductosFetch")
        .then((res) => res.json())
        .then((res) => {
          const { count, entries } = res;
          this.count = count;
          this.entries = entries;
        })
        .catch((err) => {
          console.log(err);
        });
    },
  },

  created () {
    this.getUser();
    this.getCategorias();
    this.getUnidadesMedida();
    this.getRecetas();
    this.getSucursales();
  },

  methods: {
      openDialog(){
          this.sucursales_selected = this.sucursales.map( e => e.id_sucursal);
          this.dialog=true;
      },
    editImageProduct(image){
      if(image){
        this.edited_imagen = '../storage/files/uploads/'+image.path+'/'+image.filename;
      }
      else{
        this.edited_imagen = this.img_src;
      }
      this.edit_image_dialog = true;
      //'../storage/files/uploads/'+editedItem.imagen_producto.path+'/'+editedItem.imagen_producto.filename
    },

    selectAllToggle(props){
      this.selectAll = props.value;
    },
    editItemImage(){

    },
    getUser() {
      axios
        .get("/api/user")
        .then((response) => {
          this.currentUser = response.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getSucursales() {
      axios
        .get("/api/branch")
        .then((response) => {
          this.sucursales = response.data;
          let selected = [];
          for (let sucursal in this.sucursales) {
            selected.push(this.sucursales[sucursal].id_sucursal);
          }
          this.sucursales_selected = selected;
        })
        .catch((resonse) => {
          console.log("error: " + response.data);
        });
    },

    handleFiles() {
        this.getImagePreviews();
    },
    getImagePreviews(){
        if(this.image){
            if ( /\.(jpe?g|png|gif)$/i.test( this.image.name ) ) {
                let reader = new FileReader();
                reader.addEventListener("load", function(){
                    this.img_src= reader.result;
                }.bind(this), false);
                reader.readAsDataURL( this.image );
            }else{
                this.$nextTick(function(){
                    this.img_src= reader.result;
                });
            }
        }
        else{
             this.img_src = null;
        }
    },


    getEditImagePreviews(){
        if(this.image_edit){
            if ( /\.(jpe?g|png|gif)$/i.test( this.image_edit.name ) ) {
                let reader = new FileReader();
                reader.addEventListener("load", function(){
                    this.edited_imagen = reader.result;
                }.bind(this), false);
                reader.readAsDataURL( this.image_edit );
            }else{
                this.$nextTick(function(){
                    this.edited_imagen= reader.result;
                });
            }
        }
        else{
             this.edited_imagen = null;
        }
    },
    submitEditFiles() {
        let formData = new FormData();
        formData.append('file', this.image_edit);
        formData.append('id_producto', this.editedItem.id_producto);
        if(this.editedItem.imagen_producto){
          formData.append('path', this.editedItem.imagen_producto.path);
        }
        axios.post('/files/edit-file',formData, { headers: { 'Content-Type': 'multipart/form-data' } }
        ).then(function(data) {
            this.image_edit.id = data['data']['id'];
            this.image_edit = null;
            this.edit_image_dialog = false;
            this.dialog = false;
            this.getlist();
        }.bind(this)).catch(function(data) {
            console.log('error');
        });
    },



    getCategorias(){
        axios.get('/api/getCategoriasPorTipo/1')
        .then((response) => {
            this.categorias = response.data;
        })
        .catch( resonse => {
            console.log('error: '+ response.data);
        })
    },

    getUnidadesMedida(){
        axios.get('/api/unitmeasure')
        .then((response) => {
            this.unidades = response.data;
        })
        .catch( resonse => {
            console.log('error: '+ response.data);
        })
    },

    getRecetas(){
        axios.get('/api/recipe')
        .then((response) => {
            this.recetas = response.data;
        })
        .catch( resonse => {
            console.log('error: '+ response.data);
        })
    },

    searchUser() {
      this.searchData = {
        data: this.searchQuery,
      };
      this.data.data= [];
      this.data.total= 0;
      this.data.from=0;
      this.data.to=0;
      this.getlist(1,this.itemsperpage);
    },

    getlist(page=1,perpage=25) {

        this.$store.commit('LOADER',true);
        this.data.data= [];
        this.data.total= 0;
        this.loading = true;
        this.searchData = {
            perpage: perpage,
            data: this.searchQuery,
        };
        this.cargando = true;
        axios.post('/api/getProductos?page=' + page, this.searchData)
        .then(response => {
            this.data = response.data;
            this.page = response.data.current_page;
            this.loading = false;
            this.$store.commit('LOADER',false);
        });
    },

    researchUser(){
      if(this.searchQuery.length==0){
        this.getlist(1,this.itemsperpage);
      }
    },

    editItem (item) {
            this.isEdited = false;
            this.editedIndex = this.data.data.indexOf(item)
            this.editedItem = Object.assign({}, item);
            this.editedItem.id_categoria =  this.editedItem.categoria.id_categoria;
            this.sucursales_selected = item.sucursales.map(({id_sucursal}) => id_sucursal);
            this.editedItem.recetas_productos = this.editedItem.recetas.map(({ id_receta }) => id_receta);
            this.editedItem.sucursales = this.sucursales_selected;
            this.dialog = true;
    },

    deleteItem (item) {
        this.editedIndex = this.data.data.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialogDelete = true
    },

    deleteItemConfirm () {
        //this.desserts.splice(this.editedIndex, 1)
        this.loading = true;
        axios.delete('/api/product/'+this.editedItem.id_producto, this.editedItem)
            .then(response => {
                this.loading = false;
                this.getlist();
                this.close();
            });
        this.closeDelete();
    },

    close () {
        this.dialog = false;
        this.$refs.form.reset();
        this.$nextTick(() => {
            this.editedItem = Object.assign({}, this.defaultItem)
            this.editedIndex = -1
        })
    },

    closeDelete () {
        this.dialogDelete = false
        this.$nextTick(() => {
            this.editedItem = Object.assign({}, this.defaultItem)
            this.editedIndex = -1
        })
    },
    /*
    save () {
      if(this.$refs.form.validate()){
        this.loading = true;
        if (this.editedIndex > -1) {
            //Object.assign(this.desserts[this.editedIndex], this.editedItem),
            axios.put('/api/product/'+this.editedItem.id_producto, this.editedItem)
            .then(response => {
                this.loading = false;
                this.getlist();
                this.close();
                this.$refs.form.reset();
            });
        } else {

            this.editedItem.sucursales = this.sucursales_selected;
            axios.post('/api/product', this.editedItem)
            .then(response => {
                this.loading = false;
                this.submitFiles(response.data.id_producto)
                this.getlist();
                this.close();
                this.$refs.form.reset();

            });
        }
      }
      else{
        return;
      }
    },*/
      save () {
          if(this.$refs.form.validate()){
              this.loading = true;
              if (this.editedIndex > -1) {
                  //Object.assign(this.desserts[this.editedIndex], this.editedItem),
                  delete this.editedItem.stock;
                  delete this.editedItem.sucursal_producto;
                  this.editedItem.sucursales = this.sucursales_selected;
                  this.editedItem.recetas = this.editedItem.recetas_productos;
                  console.log(this.editedItem);

                  axios.put('/api/product/'+this.editedItem.id_producto, this.editedItem)
                      .then(response => {

                          this.loading = false;

                          this.getlist();
                          this.close();
                          this.$refs.form.reset();
                          console.log(response);
                      });
              } else if( this.sucursales_selected.length > 0) {

                  this.editedItem.sucursales = this.sucursales_selected;
                  axios.post('/api/product', this.editedItem)
                      .then(response => {
                          this.loading = false;
                          this.submitFiles(response.data.id_producto)
                          this.getlist();
                          this.close();
                          this.$refs.form.reset();

                      });
              } else{
                  this.$swal({
                      toast: true,
                      position: 'top-end',
                      icon:'warning',
                      title:'Faltan campos sin llenar',
                      showConfirmButton:false,
                      timerProgressBar:true,
                      timer:2500
                  });
              }
          }
          else{
              this.$swal({
                  toast: true,
                  position: 'top-end',
                  icon:'warning',
                  title:'Faltan campos sin llenar',
                  showConfirmButton:false,
                  timerProgressBar:true,
                  timer:2500
              });
              return;
          }
      },


    submitFiles(id_producto) {
        let formData = new FormData();
        formData.append('file', this.image);
        formData.append('id_producto', id_producto);
        axios.post('/files/upload-file',formData, { headers: { 'Content-Type': 'multipart/form-data' } }
        ).then(function(data) {
            this.image.id = data['data']['id'];
            this.image = null;
        }.bind(this)).catch(function(data) {
            console.log('error');
        });
    },


    //COMBINACIONES

    getDetail(id){
        this.dialogDetail = true;
        this.id_detail = id;
        axios.get('/api/combo/'+id)
            .then(response => {
                this.loading = false;
                this.recipe_detail = response.data;
            });
    },
    closeDetail() {
        this.dialogDelete = false
        this.$nextTick(() => {
            this.editedItem = Object.assign({}, this.defaultItem)
            this.editedIndex = -1
        })
    },



    editInsumo (item) {
        this.editedIndexInsumo = this.recipe_detail.indexOf(item)
        this.editedInsumo = Object.assign({}, item)
        let insumo = item.producto;
        let data = {item, insumo };
        this.model = Object.assign({},item, insumo);
        this.dialogAddDetail = true;
    },

    deleteInsumo (item) {
        this.editedIndexInsumo = this.recipe_detail.indexOf(item)
        this.editedInsumo = Object.assign({}, item);
        this.dialogDeleteCombinacion = true;
    },

    deleteInsumoConfirm () {
        this.loading = true;
        axios.delete('/api/combo/'+this.editedInsumo.id_combo_detalle)
            .then(response => {
                this.loading = false;
                this.dialogDeleteCombinacion = false;
                this.recipe_detail.splice(this.editedIndexInsumo, 1);
            });
        this.closeDelete();
    },
    saveInsumo () {
      if(this.$refs.formcombo.validate()){
        this.loading = true;
        if (this.editedIndexInsumo > -1) {
            //Object.assign(this.desserts[this.editedIndex], this.editedItem),
            //console.log("asdasda");
            axios.put('/api/combo/'+this.model.id_combo_detalle, {id_combo: this.id_combo, id_producto: this.model.id_producto, cantidad: this.model.cantidad})
            .then(response => {
                this.dialogAddDetail = false;
                this.getDetail(this.editedInsumo.id_combo)
            });
        } else {
            console.log(this.id_detail);
            let id_combo = this.id_detail;
            axios.post('/api/combo', {id_combo: id_combo, id_producto: this.model.id_producto, cantidad: this.model.cantidad})
            .then(response => {
                this.loading = false;
                this.model = {unidad_medida: {}};
                this.dialogAddDetail = false;
                this.getDetail(this.id_detail)
            });
        }
      }
      else{
        return;
      }
    },
    verProducto(item){
        this.dialogItem = true;
        this.item_view = item;
        if(item.tipo_producto_combo==2){
            axios.get('/api/combo/'+this.item_view.id_producto)
            .then(response => {
                this.recipe_detail = response.data;
            });
        }
    },

    async activeItem (item, active) {
        try{
            await API.items.active( item.id_producto, { activo: active} );
            this.loading = false;
            this.getlist();
            this.$swal({
                toast: true,
                position: 'top-end',
                icon:'success',
                title:'Producto actualizazdo correctamente',
                showConfirmButton:false,
                timerProgressBar:true,
                timer:2500
            });
        }
        catch(error){
            this.$swal({
                icon: 'error',
                title: 'Oops...',
                text: 'Error al actualizar.'
            });
            console.error(error);
        }
    },

    async activegroup(status){
      this.loading = true;
      try {
        await API.items.activeGroup({groupid:this.groupid,status:status, selectAll: this.selectAll});
        this.getlist();
        this.selectedItems = [];
      } catch (error) {
        this.loading = false;
        console.error(error);
      }
    },
  }
}
</script>
<style lang="scss" scoped>
  .btn-actions{
    background-color: #fff !important;
    color: #121212;
  }

.col-form {
  padding-top: 0px;
  padding-bottom: 0px;
}
</style>
