<template>
     <v-snackbar v-model="activo" :color="color" timeout="-1">
          {{ contenido }}
          <template v-slot:action="{ attrs }">
               <v-btn color="black" text v-bind="attrs" @click="salida"> Cerrar </v-btn>
          </template>
     </v-snackbar>
</template>

<script>
export default {
     props: {
          activo: {
               type: Boolean 
          },
          color: { 
               type: String, 
               default: 'success' 
          },
          contenido: { 
               default: null 
          },
          cerrar: { 
               type: Function 
          },
     },
     watch: {
          activo(value) {
               if (value) {
                    setTimeout(() => this.salida(), 2500)
               }
          },
     },
     methods: {
          salida() {
               this.cerrar(false, null, 'success')
          },
     },
}
</script>