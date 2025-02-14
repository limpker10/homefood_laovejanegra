// import MyError from './utils/MyError'
import callAPI from './connector'
import PATHS from './paths'

const API = {

  users:{
    auth(){
      return callAPI('get', PATHS.users.AUTH);
    }
  },
  roles:{
    list(){
      return callAPI('get', PATHS.roles.ROOT);
    },
    create( data ){
      return callAPI('post', PATHS.roles.ROOT,{ data: data });
    },
    read(id){
      return callAPI('get', PATHS.roles.SET+id);
    },
    update(id,data){
      return callAPI('put', PATHS.roles.SET+id,{ data: data });
    },
    remove(id){
      return callAPI('delete', PATHS.roles.SET+id);
    },
    assignpermissions(idrole,data){
      return callAPI('post', PATHS.roles.ASSING_PERMISSION+idrole,{ data: data });
    },
  },

  permissions:{
    list(){
      return callAPI('get',PATHS.permissions.ROOT);
    },
    create(data){
      return callAPI('post', PATHS.permissions.ROOT,{ data: data });
    },
    read(id){
      return callAPI('get',PATHS.permissions.SET+id);
    },
    update(id,data){
      return callAPI('put', PATHS.permissions.SET+id,{ data: data });
    },
    remove(id){
      return callAPI('delete', PATHS.permissions.SET+id);
    },
  },

  items:{
    active(id,data){
      return callAPI('put',PATHS.items.ACTIVE+id,{ data: data })
    },
    activeGroup(data){
      return callAPI('post', PATHS.items.ACTIVE+'group',{ data: data }) 
    },
    stock(page, data){
      return callAPI('post', PATHS.items.SET+'stock?page='+page,{ data: data });
    }
  }, 
  pos:{
    branch(id){
      return callAPI('get',PATHS.pos.BRANCH+id);
    }
  },
  
  cash:{
    create(data){
      return callAPI('post',PATHS.cash.ROOT,{ data: data });
    },
    update(id,data){
      return callAPI('put', PATHS.cash.SET+id,{ data: data });
    },
    detail(id, data,flag){
      return callAPI('get',PATHS.cash.DETAIL+id+'/'+data+'/'+flag);
    }
  },
  order:{
    remove(id){
      return callAPI('delete', PATHS.order.SET+id);
    }
  },
  table_order:{
    remove(id){
      return callAPI('delete', PATHS.table_order.SET+id);
    }
  },
  //HOTEL

  cash_hotel:{
    create(data){
      return callAPI('post',PATHS.cash.ROOT,{ data: data });
    },
    update(id,data){
      return callAPI('put', PATHS.cash.SET+id,{ data: data });
    },
    detail(id,data){
      return callAPI('get',PATHS.cash.DETAIL+id+'/'+data);
    }
  },

  config:{
    read(){
      return callAPI('get', PATHS.CONFIG.ROOT);
    },
    update(id,data){
      return callAPI('put', PATHS.CONFIG.SET+id,{ data: data });
    }
  },

  bookings:{
    pending_bookings(){
      return callAPI('get',PATHS.booking.PENDING);
    },
    read(id){
      return callAPI('get',PATHS.booking.SET+id);
    },
    detail(data){
      return callAPI('post',PATHS.booking.DETAIL,{ data: data });
    }
  },

  rooms:{
    read(id){
      return callAPI('get',PATHS.room.SET+id);
    },
    updateField(id,data){
      return callAPI('put',PATHS.room.UPDATE+id,{ data: data });
    },
    active(){
      return callAPI('get',PATHS.room.ACTIVE);
    }
  },
  checkin:{
    create(data){
      return callAPI('post', PATHS.checkin.ROOT,{ data: data });
    },
    booking(id,data){
      return callAPI('put', PATHS.checkin.BOOKING+id,{ data: data });
    },
  },
  checkout:{
    create(id,data){
      return callAPI('put', PATHS.checkout.SET+id,{ data: data });
    },
    preload(id){
      return callAPI('get', PATHS.checkout.PRELOAD+id);
    },

  },
  customer:{
    list(page,data){
      return callAPI('post',PATHS.customers.LIST+page,{ data: data });
    },
    read(id){
      return callAPI('get',PATHS.customers.SET+id);
    },
    create(data){
      return callAPI('post', PATHS.customers.ROOT,{ data: data });
    },
  },

  invoice:{
    create(data){
      return callAPI('post', PATHS.invoice.SET,{ data: data });
    }
  },

  services:{
    tipoDocumentos(){
      return callAPI('get','/api/getTiposDoc');
    },
    tipoComprobante(){
      return callAPI('get','/api/getTiposComprobante');
    },
    metodoPago(){
      return callAPI('get','/api/getMetodosPago');
    },
    moneda(){
      return callAPI('get','/api/getMoneda');
    },
    serie(id){
      return callAPI('get','/api/getSerieComprobante/'+id);
    }
  },
  dashboard:{
    card(){
      return callAPI('get',PATHS.DASHBOARD.CARD);
    },
    graph(){
      return callAPI('get',PATHS.DASHBOARD.GRAPH);
    },
    sales(){
      return callAPI('get',PATHS.DASHBOARD.SALES);
    }
  },

  typeroom:{
    list(){
      return callAPI('get',PATHS.typeroom.ROOT);
    },
    create(data){
      return callAPI('post', PATHS.typeroom.ROOT,{ data: data });
    },
    read(id){
      return callAPI('get',PATHS.typeroom.SET+id);
    },
    update(id,data){
      return callAPI('put', PATHS.typeroom.SET+id,{ data: data });
    },
    remove(id){
      return callAPI('delete', PATHS.typeroom.SET+id);
    },
  },
  supplies:{
    list(){
      return callAPI('get', PATHS.supplies.ROOT);
    },
    create( data ){
      return callAPI('post', PATHS.supplies.ROOT,{ data: data });
    },
    read(id){
      return callAPI('get', PATHS.supplies.SET+id);
    },
    update(id,data){
      return callAPI('put', PATHS.supplies.SET+id,{ data: data });
    },
    remove(id){
      return callAPI('delete', PATHS.supplies.SET+id);
    },
    branch(id){
      return callAPI('get', PATHS.supplies.SET+'branch/'+id);
    },
    stock(page,data){
      return callAPI('post', PATHS.supplies.SET+'stock?page='+page,{ data: data });
    }
  },
};

export default API;
