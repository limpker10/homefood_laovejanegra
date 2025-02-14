<template>
  <div id="app">
  <v-app id="inspire">
    <v-app id="inspire">
      <v-main style="background-image: url(/assets/images/hotel-interior.jpg);background-position: center; background-size: cover;">
        <v-container
          class="fill-height"
          fluid
        >
          <v-row
            align="center"
            justify="center"
          >
            <v-col
              cols="12"
              sm="8"
              md="4"
            >
              <v-card class="elevation-12 grey lighten-3" style="border-radius: 15px;" 
                      width="350px">
                <v-card-text>
                  <div class="d-flex flex-column justify-space-between align-center">
                    <v-img
                      :aspect-ratio="16/9"
                      width="300px"
                      contain
                      :src="logo" 
                    ></v-img> 
                  </div>
                  <v-form ref="form" v-model="isFormValid" lazy-validation @submit="login">
                    <v-text-field
                      label="Correo Electrónico"
                      name="login"
                      prepend-icon="mdi-account"
                      type="text"
                      v-model="form.email"
                      :rules="emailRules"
                      @keydown.enter="login"
                    ></v-text-field>
  
                    <v-text-field
                      id="password"
                      label="Contraseña"
                      name="password"
                      prepend-icon="mdi-lock"
                      type="password"
                      v-model="form.password"
                      :rules="[rules.required]"
                      @keydown.enter="login"
                    ></v-text-field>
                  </v-form>
                </v-card-text>
                <v-card-actions>
                  <!--<v-spacer></v-spacer>-->
                  <v-btn @click="login()" 
                    block
                    x-large 
                    color="primary"
                    :loading="isLoading"
                    type="submit"
                  >
                    Iniciar Sesión
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </v-main>
    </v-app>
  </v-app>
</div>
</template>

<script>
  export default {
    data: () => ({
        form: {
            email: '',
            password: '',
            device_name:'browser',
        },
        drawer: null,
        isLoading: false,
        rules: {
          required: (value) => (value && Boolean(value)) || 'Campo Obligatorio'
        },
        emailRules: [
          (v) => !!v || 'Correo Electrónico es obligatorio',
          (v) => /.+@.+\..+/.test(v) || 'Correo Electrónico debe ser válido'
        ],
        isFormValid: true,
        logo:null
    }),
    props: {
      source: String
    },
    created(){
      this.configuration();
    },
    methods: {
        login() {
          
          if(this.$refs.form.validate()){
            this.isLoading = true;
            axios.get('/sanctum/csrf-cookie').then(response => {
                axios.post('/login', this.form)
                .then((response) => {
                    let userData = {
                      id: response.data.id, 
                      name: response.data.name, 
                      id_sucursal: response.data.id_sucursal, 
                      email: response.data.email, 
                    }
                    localStorage.setItem('user_data', JSON.stringify(userData))
                    localStorage.setItem('user_permissions',JSON.stringify(response.data.permissions));
                    localStorage.setItem('user_roles',JSON.stringify(response.data.roles));
                    window.location.replace('/');
                    //this.$router.push({name:'BranchsPage'});
                    this.isLoading = false;
                })
                .catch((error) => {
                    this.$swal({
                      icon:'error',
                      title:'Opps!',
                      text:'Usuario y/o Contraseña incorrecta!',
                      timerProgressBar:true,
                      timer:2500
                    })
                    this.isLoading = false;
                })
            });
          }
        },
        async configuration(){
           try{
              const data = await API.config.read();
              this.logo = data.data.logo;
              localStorage.setItem('SYSTEM_HOTEL_IMAGE_LOGO', data.data.logo);
            }
            catch(error){
              console.log(read)
            }
        },
    }
  }
</script>