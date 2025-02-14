<template>
    <v-list-item-group v-model="selectedItem" color="accent">
    <v-list-item link to="/dashboard">
        <v-list-item-action>
        <v-icon>mdi-view-dashboard-outline</v-icon>
        </v-list-item-action>
        <v-list-item-content>
        <v-list-item-title><b>Dashboard</b></v-list-item-title>
        </v-list-item-content>
    </v-list-item>
    <v-divider></v-divider>

    <v-list-item link to="/branchSystem">
        <v-list-item-action>
        <v-icon>mdi-silverware-fork-knife</v-icon>
        </v-list-item-action>
        <v-list-item-content>
        <v-list-item-title><b>POS</b></v-list-item-title>
        </v-list-item-content>
    </v-list-item>
    <v-list-group prepend-icon="mdi-cash-register" v-if="hasMenuAccess(process_cash)">
        <template v-slot:activator>
        <v-list-item-title><b>Caja</b></v-list-item-title>
        </template>
        <template v-for="([title, icon, link, gate], i) in process_cash">
            <v-list-item
                :key="i"
                link
                :to="'/'+link"
                v-if="hasAccess(gate)">
                <v-list-item-icon>
                <v-icon dense v-text="icon"></v-icon>
                </v-list-item-icon>
                <v-list-item-title v-text="title"></v-list-item-title>
            </v-list-item>
        </template>
    </v-list-group>
    <v-list-group prepend-icon="mdi-package-variant" v-if="hasMenuAccess(purchases)">
        <template v-slot:activator>
        <v-list-item-title><b>Inventario</b></v-list-item-title>
        </template>
        <template v-for="([title, icon, link, gate], i) in purchases">
            <v-list-item
                :key="i"
                link
                :to="'/'+link"
                v-if="hasAccess(gate)">
                <v-list-item-icon>
                <v-icon dense v-text="icon"></v-icon>
                </v-list-item-icon>
                <v-list-item-title v-text="title"></v-list-item-title>
            </v-list-item>
        </template>
    </v-list-group>
    <v-list-group prepend-icon="mdi-printer-pos"  v-if="hasMenuAccess(sales)">
        <template v-slot:activator>
        <v-list-item-title><b>Ventas</b></v-list-item-title>
        </template>
        <template v-for="([title, icon, link, gate], i) in sales">
            <v-list-item
                :key="i"
                link
                :to="'/'+link"
                v-if="hasAccess(gate)">
                <v-list-item-icon>
                <v-icon dense v-text="icon"></v-icon>
                </v-list-item-icon>
                <v-list-item-title v-text="title"></v-list-item-title>
            </v-list-item>
        </template>
    </v-list-group>
    <v-divider></v-divider>
    <v-list-group prepend-icon="mdi-file-chart" color="accent" v-if="hasMenuAccess(process_reports)">
        <template v-slot:activator>
        <v-list-item-title> <b>Reportes</b> </v-list-item-title>
        </template>
        <template v-for="([title, icon, link, gate], i) in process_reports">
            <v-list-item
                :key="i"
                link
                :to="'/'+link"
                v-if="hasAccess(gate)">
                <v-list-item-icon>
                <v-icon dense v-text="icon"></v-icon>
                </v-list-item-icon>
                <v-list-item-title v-text="title"></v-list-item-title>
            </v-list-item>
        </template>
    </v-list-group>
    <v-divider></v-divider>
    
    <v-list-group prepend-icon="mdi-account-group-outline" v-if="hasMenuAccess(maestros_admin)">
        <template v-slot:activator>
        <v-list-item-title><b>Administración</b></v-list-item-title>
        </template>
        <template v-for="([title, icon, link, gate],i) in maestros_admin">
            <v-list-item
                link
                v-bind:key="i"
                :to="'/'+link"
                v-if="hasAccess(gate)"
            >
                <v-list-item-icon>
                <v-icon dense v-text="icon"></v-icon>
                </v-list-item-icon>
                <v-list-item-title v-text="title"></v-list-item-title>
            </v-list-item>
        </template>
    </v-list-group>

    <v-list-group prepend-icon="mdi-warehouse"  v-if="hasMenuAccess(maestros_logistic)">
        <template v-slot:activator>
        <v-list-item-title><b>Logística</b></v-list-item-title>
        </template>
        <template v-for="([title, icon, link, gate],i) in maestros_logistic">
            <v-list-item
                link
                v-bind:key="i"
                :to="'/'+link"
                v-if="hasAccess(gate)"
            >
                <v-list-item-icon>
                <v-icon dense v-text="icon"></v-icon>
                </v-list-item-icon>
                <v-list-item-title v-text="title"></v-list-item-title>
            </v-list-item>
        </template>
    </v-list-group>
    
    <v-list-group prepend-icon="mdi-cog-outline" v-if="hasMenuAccess(maestros_config)">
        <template v-slot:activator>
        <v-list-item-title><b>Configuración</b></v-list-item-title>
        </template>
        <template v-for="([title, icon, link, gate],i) in maestros_config">
            <v-list-item
                :key="i"
                link
                :to="'/'+link"
                v-if="hasAccess(gate)"
            >
                <v-list-item-icon>
                <v-icon dense v-text="icon"></v-icon>
                </v-list-item-icon>
                <v-list-item-title v-text="title"></v-list-item-title>
            </v-list-item>
        </template>
    </v-list-group>
    </v-list-item-group>
</template>

<script>
export default {
    data: () => ({ 
        administrador: true,
        isLoading: true,
        drawer: null,
        currentUser:'',
        selectedItem: 0,
        process_reports: [
            ['Reporte de Ventas', 'mdi-file-chart','sales_report','view_report_sales'],
            ['Reporte Detallado', 'mdi-file-chart','ranking_report','view_report_detail'],
            ['Reporte Consolidado', 'mdi-file-chart','consolidate_report','view_report_consolidate'],
        ],
        process_cash: [
            ['Caja', 'mdi-inbox-arrow-up-outline','cash/restaurant','read_cash'],
            ['Egresos', 'mdi-inbox-arrow-down-outline','expenses/restaurant','read_expenses'],
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
            ['Motivos de Egreso', 'mdi-currency-usd-off','expenses_reason','read_reason_expense'],
            ['Roles y Permisos', 'mdi-account-settings','role_permission','role_permission'],
            ['Unidades de Medida', 'mdi-scale-balance','unitmeasure','read_unit_measure'],
            ['Configuración', 'mdi-cogs','configuration','read_configuration'],
        ],
        purchases: [
            ['Kárdex', 'mdi-package-variant','kardex','view_report_kardex'],
            ['Compras', 'mdi-cart-variant','purchases','register_purchases'],
            ['Stock Insumos', 'mdi-clipboard-list-outline','stock_supplies','view_report_supplies'],
            ['Stock Productos', 'mdi-clipboard-list-outline','stock_items','view_report_items'],
        ],
        sales: [
            ['Comprobantes', 'mdi-receipt','sales','read_sale']
        ],


    }),

    methods: {
        hasAccess(permission){
          return ACL.permissions.can(permission);
        },
        hasMenuAccess(permission){
          return ACL.permissions.cangroup(permission);
        },
    }
}
</script>