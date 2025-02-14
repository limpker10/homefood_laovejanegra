<template>
  <div class="flex-grow-1">
    <div class="d-flex align-center py-3">
      <div>
            <h2 style="color:  #AD91FD">Configuracion de Empresa</h2> 
      </div>
      <v-spacer></v-spacer>
    </div>


    <div class="my-2">
    <div>
        <v-card>
            <v-card-title>Información</v-card-title>
            <v-card-text>
            <div class="d-flex flex-column flex-sm-row">
                <div class="px-3" style="text-align: center">
                    <v-img
                        :src="configuration.logo"
                        :aspect-ratio="1.7"
                        style="min-width: 300px"
                        class="grey lighten-2 rounded elevation-3"
                        contain
                    ></v-img>
                    <v-btn class="mt-1" color="primary" small  @click="editImageProduct(configuration.logo)">Editar Logo</v-btn>
                </div>
                <div class="px-3 flex-grow-1 pt-2 pa-sm-2">
                    <v-text-field v-model="configuration.nombre" label="Nombre" placeholder="Nombre"></v-text-field>
                    <v-text-field v-model="configuration.direccion" label="Dirección" placeholder="Dirección"></v-text-field>
                    <v-text-field v-model="configuration.telefono" label="Teléfono" placeholder="Teléfono"></v-text-field>
                    <v-text-field v-model="configuration.email" label="E-mail" placeholder="E-mail"></v-text-field>
                </div>
            </div>
            </v-card-text>
            <v-card-actions>
                <v-spacer>
                </v-spacer>
                <v-btn color="primary" >Guardar Cambios</v-btn>
            </v-card-actions>
        </v-card>

        <!--Editar Imagen-->
        <v-dialog v-model="edit_image_dialog" max-width="500px">
          <v-card>
            <v-card-title> Editar Imágen </v-card-title>
            <v-card-text>
              <v-img
                class="grey lighten-2 rounded elevation-3"
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
              <v-btn color="error">Cancelar</v-btn>
              <v-btn color="primary" @click="submitEditFiles">Cambiar</v-btn>
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
  data() {
    return {
        configuration: {
            'nombre':'',
            'direccion':'',
            'telefono':'',
            'email':'',
            'logo':''
        },
        tab: null,
        edit_image_dialog: false,
        edited_imagen:null,
        image_edit:null,
        img_src: '../assets/images/logo_white.png'
    }
  },
  created(){
      if(!this.edited_imagen){
          this.edited_imagen = "data:image/jpeg;base64,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";
      }
      this.getConfiguration();
  },
  methods:{
    async update(){
        try{
            const data = await API.config.update(this.configuration);
            this.$swal({
                toast: true,
                position: 'top-end',
                icon:'success',
                title:'Configuración actualizada correctamente',
                showConfirmButton:false,
                timerProgressBar:true,
                timer:2500
            });
        }catch(error){
            this.$swal({
                icon: 'error',
                title: 'Oops...',
                text: 'Error al actualizar.'
            });
            console.error(error.response.status);
        }
    },
    async getConfiguration(){
        try{
            const data = await API.config.read();
            this.configuration = data.data;
        }
        catch(error){
            console.log(read)
        }
    },
    editImageProduct(image){
      if(image){
        this.edited_imagen = image;
      }
      else{
        this.edited_imagen = this.img_src;
      }
      this.edit_image_dialog = true;
      //'../storage/files/uploads/'+editedItem.imagen_producto.path+'/'+editedItem.imagen_producto.filename
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
        if(this.configuration.logo){
          formData.append('path', this.configuration.logo);
        }
        axios.post('/files/configuration',formData, { headers: { 'Content-Type': 'multipart/form-data' } }
        ).then(function(data) {
            this.image_edit.id = data['data']['id'];
            this.image_edit = null;
            this.edit_image_dialog = false;
            this.configuration.logo = data.data.file.logo;
            this.dialog = false;
        }.bind(this)).catch(function(data) {
            console.log(data);
        });
    },
  }
}
</script>
