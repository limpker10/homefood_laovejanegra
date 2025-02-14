import Vue from 'vue'
import VueRouter from 'vue-router'
/*ROUTES*/
import HotelRoutes from './hotel.routes'
import RestaurantRoutes from './restaurant.routes'

/**LAYOUTS */
import IndexMain from './../components/layouts/IndexMain'
import NotFound from './../components/layouts/NotFound'
/**GENERAL */
import Dashboard from './../components/views/pages/Dashboard/Dashboard'
import Login from './../components/views/auth/Login'
import Configuration from './../components/views/pages/Configuration/Configuration'

/**MAESTROS RESTAURANT*/
import CategoryPage from './../components/views/pages/restaurant/Category/CategoryPage'
import BranchPage from './../components/views/pages/Branch/BranchPage'
import CustomerPage from './../components/views/pages/Customer/CustomerPage'
import ProviderPage from './../components/views/pages/Provider/ProviderPage'
import UnitMeasurePage from './../components/views/pages/UnitMeasure/UnitMeasurePage'
import SuppliesPage from './../components/views/pages/restaurant/Items/SuppliesPage'
import ZonePage from './../components/views/pages/restaurant/Zone/ZonePage'
import RecipePage from './../components/views/pages/restaurant/Recipe/RecipePage'
import ItemPage from './../components/views/pages/restaurant/Items/ItemPage'
import ItemsBranch from './../components/views/pages/restaurant/ItemsBranch/ItemsBranchPage'
import TablePage from './../components/views/pages/restaurant/Table/TablePage'
import UserPage from './../components/views/pages/User/UserPage'
import RolesPermissionsPage from './../components/views/pages/RolePermissions/RolesPermissionsPage'

/**PROCESOS */
import PurchasesPage from './../components/views/pages/Purchases/PurchasesPage'
import PurchaseCreate from './../components/views/pages/Purchases/PurchaseCreate'
import PurchaseView from './../components/views/pages/Purchases/PurchaseView'

import SalesPage from './../components/views/pages/Sales/SalesPage'
import SalesCreate from './../components/views/pages/Sales/SalesCreate'
import SalesView from './../components/views/pages/Sales/SalesView'

import CashPage from './../components/views/pages/restaurant/Cash/CashPage'
import ExpensesPage from './../components/views/pages/restaurant/Cash/ExpensesPage'
import ExpenseReasons from './../components/views/pages/Configuration/ExpenseReasons'

import KardexPage from './../components/views/pages/Warehouse/KardexPage'
import SuppliesStockPage from './../components/views/pages/Warehouse/SuppliesPage'
import ItemsStockPage from './../components/views/pages/Warehouse/ItemsPage'

/*POS*/
import TablesPage from './../components/views/pages/restaurant/pos/TablesPage'
import PosPage from './../components/views/pages/restaurant/pos/PosPage'
import BranchsPage from './../components/views/pages/restaurant/pos/BranchsPage'
import PickUpPage from './../components/views/pages/restaurant/pos/PickUpPage'

/*REPORT*/
import SalesReport from './../components/views/pages/Reports/InvoiceReport'
import RankingReport from './../components/views/pages/Reports/RankingReport'
import IncomeExpensesReport from './../components/views/pages/Reports/SalesReport'
import ConsolidateReport from './../components/views/pages/Reports/ConsolidateReport'
import SuppliesReport from './../components/views/pages/Reports/SuppliesReport'


import RoomsPage from './../components/views/pages/Hotel/Rooms/RoomsPage'
import CategoriesPage from './../components/views/pages/Hotel/Categories/CategoriesPage'
import ZonasPage from './../components/views/pages/Hotel/Zonas/ZonasPage'
import BookingsPage from './../components/views/pages/Hotel/Bookings/BookingsPage'
import ReceptionPage from './../components/views/pages/Hotel/Reception/ReceptionPage'
import BranchsHotelPage from './../components/views/pages/Hotel/Reception/BranchsPage'

Vue.use(VueRouter)

const routes = [
    {
        path: "/",
        name: "Main",
        component: IndexMain,
        meta: { requiresAuth: true },
        children: [
            {
                path: '/',
                redirect: '/dashboard'
            },
            /** ROUTES*/
            ...HotelRoutes,
            ...RestaurantRoutes,
            /**MAESTROS */
            {
                path: "/dashboard",
                name: "Dashboard",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: Dashboard
                }
            },
            {
                path: "/configuration",
                name: "Configuration",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: Configuration
                }
            },
            {
                path: "/category",
                name: "Category",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: CategoryPage
                }
            },
            {
                path: "/branch",
                name: "Branch",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: BranchPage
                }
            },
            {
                path: "/customer",
                name: "Customer",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: CustomerPage
                }
            },
            {
                path: "/provider",
                name: "Provider",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: ProviderPage
                }
            },
            {
                path: "/unitmeasure",
                name: "UnitMeasure",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: UnitMeasurePage
                }
            },
            {
                path: "/supplies",
                name: "Supplies",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: SuppliesPage
                }
            },
            {
                path: "/zone",
                name: "Zone",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: ZonePage
                }
            },
            {
                path: "/recipe",
                name: "Recipe",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: RecipePage
                }
            },
            {
                path: "/products",
                name: "Products",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: ItemPage
                }
            },

            {
                path: "/products_branch",
                name: "ProductsBranch",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: ItemsBranch
                }
            },

            {
                path: "/user",
                name: "Users",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: UserPage
                }
            },

            {
                path: "/table",
                name: "Table",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: TablePage
                }
            },

            /**PROCESOS */
            {
                path: "/purchases",
                name: "PurchasesPage",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: PurchasesPage
                }
            }, 
            {
                path: "/purchase_view/:id",
                name: "PurchaseView",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: PurchaseView
                }
            },
            {
                path: "/purchase_create",
                name: "PurchaseCreate",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: PurchaseCreate
                }
            },


            {
                path: "/sales",
                name: "SalesPage",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: SalesPage
                }
            }, 
            {
                path: "/sales_view/:id",
                name: "SalesView",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: SalesView
                }
            },
            {
                path: "/sales_create",
                name: "SalesCreate",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: SalesCreate
                }
            },


            {
                path: "/branchSystem/",
                name: "BranchsPage",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: BranchsPage
                }
            },
            {
                path: "/posSystem/:id",
                name: "PosPage",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: PosPage
                }
            },
            {
                path: "/tablesSystem/:id",
                name: "TablesPage",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: TablesPage
                }
            },

            {
                path: "/pickupSystem/:id",
                name: "PickUpPage",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: PickUpPage
                }
            },

            
            {
                path: "/role_permission",
                name: "RolePermissionPage",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: RolesPermissionsPage
                }
            },

            {
                path: "/cash/:id",
                name: "CashPage",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: CashPage//
                }
            },
            
            {
                path: "/expenses/:id",
                name: "ExpensePage",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: ExpensesPage
                }
            },

            {
                path: "/expenses_reason",
                name: "ExpenseReasons",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: ExpenseReasons
                }
            },
            

            //REPORT

            {
                path: "/sales_report",
                name: "SalesReport",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: SalesReport
                }
            },
            {
                path: "/income_expenses_report",
                name: "IncomeExpensesReport",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: IncomeExpensesReport
                }
            },

            {
                path: "/ranking_report",
                name: "RankingReport",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: RankingReport
                }
                
            },
            
            {
                path: "/consolidate_report",
                name: "ConsolidateReport",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: ConsolidateReport
                }
                
            },
            
            
            {
                path: "/stock_report",
                name: "SuppliesReport",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: SuppliesReport
                }
                
            },

            {
                path: "/kardex",
                name: "KardexPage",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: KardexPage
                }
                
            },
            

            {
                path: "/stock_supplies",
                name: "SuppliesStockPage",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: SuppliesStockPage
                }
                
            },

            {
                path: "/stock_items",
                name: "ItemsStockPage",
                meta: { requiresAuth: true },
                components: {
                    default: IndexMain,
                    MainView: ItemsStockPage
                }
                
            },
        ]
    },
    /*
    {
        path: "/tablesSystem",
        name: "TablesPage",
        component: TablesPage,
        meta: { requiresAuth: true },
    },
    {
        path: "/posSystem",
        name: "PosPage",
        component: PosPage,
        meta: { requiresAuth: true },
    },*/
    
    {
        path: "/login",
        name: "Login",
        component: Login,
        meta: { guest: true },
    },
    {
         path: '*',
         component: NotFound,
    },
]

const router = new VueRouter({
    mode: "history",
    routes
});

function loggedIn(){
    //return true;
    return localStorage.getItem("user_data");
}

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
      // this route requires auth, check if logged in
      // if not, redirect to login page.
      if (!loggedIn()) {
        next({
          path: '/login',
          query: { redirect: to.fullPath }
        })
      } else {
        next()
      }
    } else if (to.matched.some(record => record.meta.guest)) {
        if (loggedIn()) {
            next({
                path: '/branchSystem',
                query: { redirect: to.fullPath }
            })
        } else {
            next()
        }
    }
    else{
        next()  
    }
  })

export default router;