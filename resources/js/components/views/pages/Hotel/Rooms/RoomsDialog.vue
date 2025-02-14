<template>
     <v-dialog v-model="abierto" max-width="500px" :persistent="cargando" @click:outside="cerrar">
          <v-card>
               <v-card-title>
                    <span class="headline">{{ dialog.titulo }}</span>
               </v-card-title>

               <v-container v-if="dialog.modo != 2 && dialog.modo != 4" class="pa-6">
                    <v-form v-model="formulario" ref="form" lazy-validation>
                         <!-- NOMBRE -->
                         <v-row dense>
                              <v-text-field
                                   v-model="parametros.nombre_habitacion"
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
                                   :value="zona.id_zona_hotel"
                                   :disabled="cargando"
                                   @change="cambioZona"
                                   v-model="zona"
                                   label="Zona"
                                   item-value="id_zona_hotel"
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
                                   label="Tipo de Habitación"
                                   item-value="id_categoria"
                                   item-text="nombre_categoria"
                                   required
                                   return-object
                              />
                         </v-row>-->
                         <!-- DETALLE -->
                         <v-row dense>
                              <v-text-field
                                   v-model="parametros.detalle"
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
                                   v-model="parametros.costo"
                                   label="Costo"
                                   :disabled="cargando"
                                   :rules="costoReglas"
                                   prepend-inner-icon="mdi-currency-usd"
                                   type="number"
                                   required
                                   @blur="precioBlur"
                              />
                         </v-row>-->
                         <!-- ESTADO -->
                         <v-row dense v-if="dialog.modo === 1">
                              <v-select
                                   :items="parametros.id_reservas ? estadosCon : estados"
                                   :value="estado.value"
                                   :disabled="cargando || parametros.id_reservas"
                                   @change="cambioEstado"
                                   v-model="estado"
                                   label="Estado"
                                   item-value="value"
                                   item-text="name"
                                   required
                                   return-object
                              />
                         </v-row>
                    </v-form>
               </v-container>

               <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="error" dark @click="cerrar" :disabled="cargando">
                         {{ dialog.cancelarLabel }}
                    </v-btn>
                    <v-btn color="primary" dark @click="salvar" :disabled="cargando">
                         {{ dialog.aceptarLabel }}
                    </v-btn>
               </v-card-actions>
          </v-card>
     </v-dialog>
</template>

<script>
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
     },
     data() {
          return {
               formulario: false,
               zona: { id_zona_hotel: 0, nombre_zona: '' },
               categoria: { id_categoria: 0, nombre_categoria: '' },
               nombreReglas: [(v) => !!v || 'Campo Nombre obligatorio'],
               zonaReglas: [({ nombre_zona }) => !!nombre_zona || 'Selecciona una zona'],
               categoriaReglas: [({ nombre_categoria }) => !!nombre_categoria || 'Selecciona una categoria'],
               detalleReglas: [(v) => !!v || 'Campo Detalle obligatorio'],
               costoReglas: [(v) => !!v || 'Campo Costo obligatorio', (v) => parseInt(v) > 0 || 'Costo muy bajo'],

               parametros: {
                    id_habitacion: 0,
                    nombre_habitacion: '',
                    id_zona_hotel: 1,
                    id_categoria: 1,
                    detalle: '',
                    costo: 0.0,
                    estado: 0,
               },
               estadosCon: [
                    { name: 'Libre', value: 0 },
                    { name: 'Ocupada', value: 1 },
                    { name: 'Limpieza', value: 2 },
               ],
               estados: [
                    { name: 'Libre', value: 0 },
                    { name: 'Limpieza', value: 2 },
               ],
               estado: { name: 'Libre', value: 0 },
          }
     },
     watch: {
          dialog: {
               handler() {
                    this.parametros = this.parametrosAPI
                    if (this.dialog.modo === 0) {
                         this.resetear()
                    }
                    if (this.dialog.modo === 1) {
                         this.zona = this.zonas.find((v) => v.id_zona_hotel === this.parametros.id_zona_hotel) || {
                              id_zona_hotel: 0,
                              nombre_zona: '',
                         }
                         this.categoria = this.categorias.find(
                              (v) => v.id_categoria === this.parametros.id_categoria,
                         ) || { id_categoria: 0, nombre_categoria: '' }

                         this.estado = this.estadosCon.find((v) => v.value === this.parametros.estado) || {
                              name: 'Libre',
                              value: 0,
                         }
                         // console.log(this.parametrosAPI)
                    }
               },
               deep: true,
          },
     },
     methods: {
          resetear() {
               this.zona = { id_zona_hotel: 0, nombre_zona: '' }
               this.categoria = { id_categoria: 0, nombre_categoria: '' }
               this.parametros = {
                    id_habitacion: 0,
                    nombre_habitacion: '',
                    id_zona_hotel: 1,
                    id_categoria: 1,
                    detalle: '',
                    costo: 0.0,
                    estado: 0,
               }
          },
          cerrar() {
               this.resetear()
               this.cancelar()
          },
          salvar() {
               if (this.dialog.modo === 0 || this.dialog.modo === 1) {
                    if (!this.$refs.form.validate()) return
               }
               this.aceptar(this.dialog.modo, this.parametros)
          },
          cambioZona({ id_zona_hotel }) {
               console.log(this.zona)
               this.parametros.id_zona_hotel = id_zona_hotel
          },
          cambioCategoria({ id_categoria }) {
               console.log(id_categoria)
               this.parametros.id_categoria = id_categoria
          },
          cambioEstado({ value }) {
               this.parametros.estado = value
          },
          precioBlur(value) {
               console.log(value.srcElement.value)
               if (value.srcElement.value) this.parametros.costo = parseFloat(value.srcElement.value).toFixed(2)
               else this.parametros.costo = 0.0
          },
     },
}
</script>