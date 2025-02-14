<template>
  <div id="app">
    <v-app id="inspire" v-if="!isLoading" style="background: #efefef;">
      <v-navigation-drawer
        v-model="drawer"
        app
        expand-on-hover
      >
        <v-list dense nav>
          <v-list-item class="mb-0 justify-space-between pl-3">
                <v-img style="margin:10px 0px; max-width:140px;" src="/assets/images/logo.png" />
            </v-list-item>
          
          <v-divider></v-divider>
          <v-list-item-group v-model="selectedItem" color="primary"  >
            <v-list-item link to="/dashboard">
              <v-list-item-action>
                <v-icon>mdi-view-dashboard-outline</v-icon>
              </v-list-item-action>
              <v-list-item-content>
                <v-list-item-title><b>Dashboard</b></v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-group prepend-icon="mdi-file-chart" >
                <template v-slot:activator>
                <v-list-item-title> <b>Reportes</b> </v-list-item-title>
                </template>
                <v-list-item
                    v-for="([title, icon, link], i) in process_reports"
                    :key="i"
                    link
                    :to="'/'+link">
                    <v-list-item-icon>
                    <v-icon dense v-text="icon"></v-icon>
                    </v-list-item-icon>
                    <v-list-item-title v-text="title"></v-list-item-title>
                </v-list-item>
            </v-list-group>
            <v-list-group prepend-icon="mdi-cash-register" >
                <template v-slot:activator>
                <v-list-item-title><b>Caja</b></v-list-item-title>
                </template>
                <v-list-item
                    v-for="([title, icon, link], i) in process_cash"
                    :key="i"
                    link
                    :to="'/'+link">
                    <v-list-item-icon>
                    <v-icon dense v-text="icon"></v-icon>
                    </v-list-item-icon>
                    <v-list-item-title v-text="title"></v-list-item-title>
                </v-list-item>
            </v-list-group>
            <!--<v-list-group prepend-icon="mdi-cart-variant" >
                <template v-slot:activator>
                <v-list-item-title><b>Compras</b></v-list-item-title>
                </template>
                <v-list-item
                    v-for="([title, icon, link], i) in purchases"
                    :key="i"
                    link
                    :to="'/'+link">
                    <v-list-item-icon>
                    <v-icon dense v-text="icon"></v-icon>
                    </v-list-item-icon>
                    <v-list-item-title v-text="title"></v-list-item-title>
                </v-list-item>
            </v-list-group>-->
            <v-list-group prepend-icon="mdi-printer-pos" >
                <template v-slot:activator>
                <v-list-item-title><b>Ventas</b></v-list-item-title>
                </template>
                <v-list-item
                    v-for="([title, icon, link], i) in sales"
                    :key="i"
                    link
                    :to="'/'+link">
                    <v-list-item-icon>
                    <v-icon dense v-text="icon"></v-icon>
                    </v-list-item-icon>
                    <v-list-item-title v-text="title"></v-list-item-title>
                </v-list-item>
            </v-list-group>
            <v-list-group prepend-icon="mdi-package-variant" >
                <template v-slot:activator>
                <v-list-item-title><b>Inventario</b></v-list-item-title>
                </template>
                <v-list-item
                    v-for="([title, icon, link], i) in process_stock"
                    :key="i"
                    link
                    :to="'/'+link">
                    <v-list-item-icon>
                    <v-icon dense v-text="icon"></v-icon>
                    </v-list-item-icon>
                    <v-list-item-title v-text="title"></v-list-item-title>
                </v-list-item>
            </v-list-group>
            
            <v-list-item link to="/branchSystem">
              <v-list-item-action>
                <v-icon>mdi-silverware-fork-knife</v-icon>
              </v-list-item-action>
              <v-list-item-content>
                <v-list-item-title><b>POS</b></v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            
            <v-list-group prepend-icon="mdi-account-group-outline" >
                <template v-slot:activator>
                <v-list-item-title><b>Administración</b></v-list-item-title>
                </template>
                <template v-for="([title, icon, link],i) in maestros_admin">
                  <v-list-item
                      link
                      v-bind:key="i"
                      :to="'/'+link"
                  >
                      <v-list-item-icon>
                      <v-icon dense v-text="icon"></v-icon>
                      </v-list-item-icon>
                      <v-list-item-title v-text="title"></v-list-item-title>
                  </v-list-item>
                </template>
            </v-list-group>

            <v-list-group prepend-icon="mdi-warehouse" >
                <template v-slot:activator>
                <v-list-item-title><b>Logística</b></v-list-item-title>
                </template>
                <template v-for="([title, icon, link],i) in maestros_logistic">
                  <v-list-item
                      link
                      v-bind:key="i"
                      :to="'/'+link"
                  >
                      <v-list-item-icon>
                      <v-icon dense v-text="icon"></v-icon>
                      </v-list-item-icon>
                      <v-list-item-title v-text="title"></v-list-item-title>
                  </v-list-item>
                </template>
            </v-list-group>
            
            <v-list-group prepend-icon="mdi-cog-outline" >
                <template v-slot:activator>
                <v-list-item-title><b>Configuración</b></v-list-item-title>
                </template>
                <v-list-item
                    v-for="([title, icon, link], i) in maestros_config"
                    :key="i"
                    link
                    :to="'/'+link"
                >
                    <v-list-item-icon>
                    <v-icon dense v-text="icon"></v-icon>
                    </v-list-item-icon>
                    <v-list-item-title v-text="title"></v-list-item-title>
                </v-list-item>
            </v-list-group>
          </v-list-item-group>
          
        </v-list>
      </v-navigation-drawer>
  
      <v-card >
        <v-app-bar
          app
          style="margin: 10px; height: 50px;  background: white;"
        >
          <v-app-bar-nav-icon
            @click.stop="drawer = !drawer"
            style="height: 50px;"
           ></v-app-bar-nav-icon>
          <v-toolbar-title></v-toolbar-title>
          <v-spacer></v-spacer>
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
        <v-container
          fluid
        >
                <router-view class="main-view" name="MainView"></router-view>
        </v-container>
      </v-main>
      <v-footer
        app dark class="grey darken-3"
      >
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
export default {
    data: () => ({ 
        isLoading: true,
        drawer: null,
        currentUser:'',
        selectedItem: 0,
        //token: localStorage.getItem('token'),
        process_reports: [
            ['Reporte de Ventas', 'mdi-file-chart','sales_report','view_report_sales'],
            //['Ingresos/Egresos', 'mdi-file-chart','income_expenses_report'],
            ['Reporte Detallado', 'mdi-file-chart','ranking_report'],
            ['Reporte Consolidado', 'mdi-file-chart','consolidate_report'],
            //['Reporte de Stock', 'mdi-file-chart','stock_report'],
        ],
        process_cash: [
            ['Caja', 'mdi-inbox-arrow-up-outline','cash','read_cash'],
            ['Egresos', 'mdi-inbox-arrow-down-outline','expenses','read_cash'],
        ],
        process_stock: [
            //['Kárdex ', 'mdi-package-variant','kardex'],
            ['Compras', 'mdi-cart-variant','purchases'],
            ['Kárdex Productos', 'mdi-package-variant','kardexProductos'],
            ['Kárdex Insumos', 'mdi-package-variant','KardexInsumos'],

            ['Inventario Productos', 'mdi-inbox-multiple-outline','inventarioProductos'],
            ['Inventario Insumos', 'mdi-inbox-multiple-outline','inventarioInsumos'],
        ],
        maestros_admin: [
            ['Clientes', 'mdi-account-group-outline','customer','read_customers'],
            ['Proveedores', 'mdi-account-details','provider','read_providers'],
            ['Usuarios', 'mdi-card-account-details-outline','user','read_users'],
            ['Sucursales', 'mdi-office-building','branch','read_branch'],
        ],
        maestros_logistic: [
            ['Categorías', 'mdi-shape-outline','category','read_category'],
            ['Insumos', 'mdi-shaker-outline','supplies','read_supplies'],
            ['Productos', 'mdi-food','products','read_products'],
            ['Recetas', 'mdi-clipboard-list-outline','recipe','read_recipe'],
            ['Zonas', 'mdi-file-table-box-outline ','zone','read_zone'],
            ['Mesas', 'mdi-table-chair','table','read_tables'],
        ],
        maestros_config: [
            //['Métodos de Pago', 'mdi-credit-card-multiple-outline','dashboard'],
            //['Documentos', 'mdi-receipt','dashboard'],
            ['Unidades de Medida', 'mdi-scale-balance','unitmeasure','read_unit_measure'],
            ['Motivos de Egreso', 'mdi-currency-usd-off','expenses_reason'],
            ['Roles y Permisos', 'mdi-account-settings','role_permission']
        ],
        purchases: [
            ['Compras', 'mdi-receipt','purchases']
        ],
        sales: [
            ['Comprobantes', 'mdi-receipt','sales','view_report_sales']
        ],
    }),
    components: { 
      LoadingPage 
    },
    methods: {
        getUser() {
            axios.get('/api/user').then(response => {
                this.currentUser = response.data;
           }).catch(error => {
                this.redirectLoginPage();
                //this.$router.push('/login');
           }).finally(() => (this.isLoading = false));
        },
        logout(){
            axios.post('/logout').then(response => {
                // localStorage.removeItem('user_data');
                // this.$router.push('/login')
                this.redirectLoginPage();
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
        
        clearStorage (){
          localStorage.clear();
        },
        redirectLoginPage(){
          this.clearStorage();
          window.location.replace('/login');
        },

    },
    created() {
      //axios.defaults.headers.common['Authorization']=`Bearer ${this.token}`;
      this.getUser();
      
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
    background: #f8e2b6 !important;
}
</style> 