<template>
  <div id="app">
    <v-app id="inspire" v-if="!isLoading" style="background: #efefef;">
      <v-navigation-drawer
        v-model="drawer"
        app
        dark
        class="blue-grey darken-3"
        expand-on-hover
      >
      <!-- permanent expand-on-hover-->
        <v-list dense nav >
          <v-list-item class="mb-0 justify-space-between pl-3">
                <v-img style="margin:10px 0px" :src="logo" />
            </v-list-item>
          <v-divider></v-divider>
          <nav-hotel v-if="administrador"></nav-hotel>
          <nav-restaurante v-else></nav-restaurante>
        </v-list>
      </v-navigation-drawer>
  
      <v-card :color="'#9788eb'">
        <v-app-bar
          app
          dark
          style="margin: 10px; height: 50px;"
          
        class="blue-grey darken-3"
        >
          <v-app-bar-nav-icon
            @click.stop="drawer = !drawer"
            style="height: 50px;"
           ></v-app-bar-nav-icon>
          <v-toolbar-title></v-toolbar-title>
          <v-spacer></v-spacer>
          <v-switch
            v-model="administrador"
            inset
            hide-details
            color="accent"
            :label="administrador?'Hotel':'Restaurante'"
            
          ></v-switch>
          <v-menu offset-y left transition="slide-y-transition">
            <template v-slot:activator="{ on }">
              <v-btn icon class="elevation-2" v-on="on">
                <v-badge
                  color="success"
                  dot
                  bordered
                  offset-x="10"
                  offset-y="10"
                >
                  <v-avatar size="40" >
                    <v-img src="/assets/images/avatar.png"></v-img>
                  </v-avatar>
                </v-badge>
              </v-btn>
            </template> 
            <v-list dense nav>
              <v-list-item @click="logout">
                <v-list-item-icon>
                  <v-icon small>mdi-logout-variant</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Cerrar Sesion</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-menu>
        </v-app-bar>
      </v-card>
  
      <v-main>
        <v-container fluid>
          <router-view class="main-view" name="MainView"></router-view>
        </v-container>
      </v-main>
      <v-footer app dark class="grey darken-3" >
        <span class="white--text">&copy; Copyright &copy; ByteSoluciones</span>
      </v-footer>
    </v-app>
    <div class="loading-page" v-else>
        <loading-page></loading-page>
    </div>
</div>
</template>

<script>
import LoadingPage from "./LoadingPage.vue"
import NavRestaurante from "./components/NavRestaurante.vue"
import NavHotel from "./components/NavHotel.vue"

export default {
    data: () => ({ 
        administrador: false,
        isLoading: true,
        drawer: null,
        currentUser:'',
        selectedItem: 0,
        logo:null,
    }),
    components: { 
      LoadingPage,
      NavRestaurante,
      NavHotel
    },
    methods: {
        
        async getUser() {
            axios.get('/api/user').then(response => {
                this.currentUser = response.data;
                
           }).catch(error => {
             console.log(error)
                localStorage.removeItem('user_data')
                this.$router.push('/login');
           }).finally(() => (this.isLoading = false));
        },

        logout(){
            axios.post('/logout').then(response => {
                localStorage.removeItem('user_data');
                this.$router.push('/login')
           }).catch(error => {
               console.log(error)
           }); 
        },

        async auth(){
          try{
            await API.users.auth();
          }
          catch(error){
            this.redirectLoginPage();
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

        clearStorage (){
          localStorage.clear();
        },
        redirectLoginPage(){
          this.clearStorage();
          window.location.replace('/login');
        },

        initWebSocket(){
          if(ACL.role.can('admin')){
            console.log(window.Echo)
            window.Echo.channel('order-tracker').listen('NotificactionsAdminEvent', (e) => {
              console.log(e)
              this.$toast.open({
                message: "<button onclick='console.log(window.UTILS.route.redirect("+e.mesa.id_mesa+"));'>Se agrego una nueva orden en <b>"+e.mesa.nro_mesa+"</b> </button>",
                type: "info",
                duration: 120000,
                dismissible: false,
                position: "top-right"
              });
            });
          }
        }
    },
    created() {
      this.getUser();
      this.initWebSocket();
      this.configuration();
    }
  }
</script>
<style lang="css">
.v-toolbar__content {
    height: 50px !important;
}
*{
    text-transform: none !important;
    font-family:'Quicksand', sans-serif  !important;
}

    h1, h2, h3, h4, h5, h6, .subtitle-1, .subtitle-2, .body-1, .body-2, .caption, .overline{
      text-transform: none !important;
    font-family:'Quicksand', sans-serif  !important;
    }

  .theme--light.v-data-table > .v-data-table__wrapper > table > tbody > tr:hover:not(.v-data-table__expanded__content):not(.v-data-table__empty-wrapper) {
    background: rgba(151, 136, 235, 0.18) !important;
}
.v-application .headline, .v-application .title{
text-transform: none !important;
    font-family:'Quicksand', sans-serif  !important;
}
.v-application .headline {
     font-family:'Quicksand', sans-serif  !important;
}
.v-application{
     font-family:'Quicksand', sans-serif  !important;
}
.application {
  font-family:'Quicksand', sans-serif  !important;
}
.headline,
.title,
.subheading{
     font-family:'Quicksand', sans-serif  !important;
}
.headline {
     font-family:'Quicksand', sans-serif  !important;
}
v-card__title{
  font-family:'Quicksand', sans-serif  !important;
}
.body-1{
     font-family:'Quicksand', sans-serif  !important;
}
</style> 