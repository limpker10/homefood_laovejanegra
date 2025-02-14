const PATHS = {
    LOGIN: "/signin",
    LOGOUT: "/logout",
    CONFIG:{
        ROOT:'api/configuration',
        SET:'api/configuration/'
    },
    DASHBOARD:{
        CARD: "api/dashboard/card",
        GRAPH: "api/dashboard/graph",
        SALES: "api/dashboard/sales"
    },
    users:{
        ROOT:'api/users',
        SET:'api/users/',
        AUTH : 'api/user',
        LIST :'api/users/list?page=',
    },
    roles:{
        ROOT:'api/roles',
        SET:'api/roles/',
        ASSING_PERMISSION :'api/roles/assignPermissions/',
    },
    permissions:{
        ROOT:'api/permissions',
        SET:'api/permissions/',
        ASSING_PERMISSION :'api/roles/assignPermissions/',
    },
    items:{
        SET:'api/products/',
        ROOT:'api/products',
        ACTIVE:'api/activeProductos/'
    },
    order:{
        ROOT:'api/order',
        SET:'api/order/'
    },
    table_order:{
        ROOT:'api/mesaorden',
        SET:'api/mesaorden/'
    },
    pos:{
        BRANCH:'api/getUserBranchs/',
    },
    cash:{
        ROOT:'api/cash',
        SET:'api/cash/',
        DETAIL:'api/getMontosDelDia/'
    },

    booking:{
        PENDING: 'api/bookingsToday',
        ROOT: 'api/bookings',
        SET: 'api/bookings/',
        DETAIL: 'api/booking_detail'
    },
    checkin:{
        ROOT: 'api/checkin',
        SET: 'api/checkin/',
        BOOKING: 'api/checkin_booking/',
    },
    checkout:{
        SET: 'api/checkout/',
        PRELOAD: 'api/preLoadDataHotel/'
    },
    room:{
        ROOT: 'api/rooms',
        SET: 'api/rooms/',
        UPDATE: 'api/updateFieldRoom/',
        ACTIVE: 'api/room/active'
    },
    customers:{
        LIST: '/api/getClientes?page=',
        ROOT: '/api/customer',
        SET: '/api/customer'
    },
    invoice:{
        SET: 'api/invoice'
    },
    typeroom:{
        ROOT: '/api/typeroom',
        SET: '/api/typeroom/'
    },
    supplies:{
        ROOT:'api/supplies',
        SET:'api/supplies/'
    }
};

export default PATHS;
