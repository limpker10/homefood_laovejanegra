<template>
     <v-toolbar flat :extended="buscando">
          <template v-if="buscando" v-slot:extension>
               <v-select :items="headers" item-text="name" v-model="where" label="Buscar por" hide-details required />
          </template>
          <v-text-field
               v-model="word"
               v-if="buscando"
               label="Buscador"
               hide-details
               clearable
               filled
               dense
               full-width
               prepend-inner-icon="mdi-arrow-left"
               append-outer-icon="mdi-magnify"
               @keydown.enter="buscar"
               @click:prepend-inner="ocultarBuscador"
               @click:append-outer="buscar"
          />
          <v-container v-else fluid class="d-flex flex-row">
               <v-spacer />
               <v-tooltip bottom> 
                    <template v-slot:activator="{ on, attrs }">
                         <v-icon
                              color="primary"
                              class="mr-2"
                              v-bind="attrs"
                              v-on="on"
                              @click="buscando = !buscando"
                              :disabled="cargando"
                         >
                              mdi-magnify
                         </v-icon>
                    </template>
                    Buscar
               </v-tooltip>

               <v-tooltip bottom v-if="eliminar">
                    <template v-slot:activator="{ on, attrs }">
                         <v-icon
                              color="primary"
                              class="mr-2"
                              v-bind="attrs"
                              v-on="on"
                              @click="modal(4, {})"
                              :disabled="cargando"
                         >
                              mdi-delete
                         </v-icon>
                    </template>
                    Eliminar
               </v-tooltip>

               <v-menu v-model="menu" :close-on-content-click="false" :nudge-width="200" offset-y>
                    <template v-slot:activator="{ on, attrs }">
                         <v-tooltip bottom v-bind="attrs" v-on="on">
                              <template v-slot:activator="{ on, attrs }">
                                   <v-icon
                                        color="primary"
                                        class="mr-2"
                                        v-bind="attrs"
                                        v-on="on"
                                        @click="menu = !menu"
                                        :disabled="cargando"
                                   >
                                        mdi-view-column
                                   </v-icon>
                              </template>
                              Ver Columnas
                         </v-tooltip>
                    </template>
                    <v-list>
                         <v-list-item v-for="(header, i) in headers" :key="i" dense>
                              <v-list-item-action>
                                   <v-checkbox
                                        v-model="header.checked"
                                        :label="header.name"
                                        color="primary"
                                        @click="gestionarVistaColumna(header, i)"
                                   />
                              </v-list-item-action>
                         </v-list-item>
                    </v-list>
               </v-menu>

               <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                         <v-icon
                              color="primary"
                              class="mr-2"
                              v-bind="attrs"
                              v-on="on"
                              @click="recargar"
                              :disabled="cargando"
                         >
                              mdi-refresh
                         </v-icon>
                    </template>
                    Recargar
               </v-tooltip>
               <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                         <v-icon
                              color="primary"
                              class="mr-2"
                              v-bind="attrs"
                              v-on="on"
                              @click="modal(0, {})"
                              :disabled="cargando"
                         >
                              mdi-plus
                         </v-icon>
                    </template>
                    Crear Reserva
               </v-tooltip>
          </v-container>
     </v-toolbar>
</template>

<script>
export default {
     props: {
          cargando: { type: Boolean },
          eliminar: { type: Boolean },
          headers: { type: Array },
          buscador: { type: Function },
          gestionarBusqueda: { type: Function },
          gestionarVistaColumna: { type: Function },
          modal: { type: Function },
          recargar: { type: Function },
     },
     data() {
          return {
               buscando: false,
               menu: false,
               where: 'Nombre',
               word: '',
          }
     },
     methods: {
          buscar() {
               this.buscador(this.word, this.where)
          },
          ocultarBuscador() {
               this.buscando = false
               this.word = ''
               this.gestionarBusqueda(true)
          },
     },
}
</script>