<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
//AUTH
Route::post('login','App\Http\Controllers\Api\Auth\AuthController@login');
Route::post('logout','App\Http\Controllers\Api\Auth\AuthController@logout');
Route::apiResource('configuration', ConfiguracionController::class);

Route::middleware("auth:sanctum")->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->getData();
    });

    //MASTER SERVICE
    Route::apiResource('category', CategoryController::class);
    Route::apiResource('branch', SucursalesController::class);
    Route::apiResource('provider', ProviderController::class);
    Route::apiResource('customer', CustomerController::class);
    Route::apiResource('unitmeasure', UnitMeasureController::class);
    Route::apiResource('supplies', SuppliesController::class);
    Route::apiResource('zone', ZonasController::class);
    Route::apiResource('recipe', RecetasControler::class);
    Route::apiResource('recipe_detail', RecipeDetailController::class);
    Route::apiResource('table', MesasController::class);
    Route::apiResource('product', ProductController::class);
    Route::apiResource('combo', ComboController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('order', OrdenController::class);
    Route::apiResource('orderdetail', OrdenDetalleController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permissions', PermissionsController::class);
    Route::apiResource('reason_expense', ReasonExpenseController::class);
    Route::apiResource('expenses', ExpenseController::class);
    Route::apiResource('kitchen', KitchenController::class);


    //POS
    Route::apiResource('mesaorden', MesasOrdenController::class);

    //PROCESS
    Route::apiResource('purchases', PurchasesController::class);
    Route::apiResource('sales', SalesController::class);
    Route::apiResource('cash', CashController::class);

    //MASTER SERVICES
    Route::post('/getAlmacen', [AlmacenController::class,'getAlmacen']);
    Route::post('/getAlmacenProductos', [AlmacenController::class,'getAlmacenProductos']);
    Route::post('/getAlmacenInsumos', [AlmacenController::class,'getAlmacenInsumos']);
    Route::post('/getInventarioInsumos', [AlmacenController::class,'getInventarioInsumos']);
    Route::post('/getInventarioProductos', [AlmacenController::class,'getInventarioProductos']);
    Route::post('/updateStockProducto', [ProductController::class,'updateStock']);
    Route::post('/updateStockInsumo', [SuppliesController::class,'updateStock']);

    Route::post('/getCategorias', [CategoryController::class,'getCategorias']);
    Route::post('/getSucursales', [SucursalesController::class,'getSucursales']);
    Route::post('/getProveedores', [ProviderController::class,'getProveedores']);
    Route::post('/getClientes', [CustomerController::class,'getClientes']);
    Route::post('/getUnidadesMedida', [UnitMeasureController::class,'getUnidadesMedida']);
    Route::post('/getInsumos', [SuppliesController::class,'getInsumos']);
    Route::post('/getZonas', [ZonasController::class,'getZonas']);
    Route::post('/getRecetas', [RecetasControler::class,'getRecetas']);
    Route::post('/getMesas', [MesasController::class,'getMesas']);
    Route::post('/getProductos', [ProductController::class,'getProductos']);
    Route::post('/getUsuarios', [UserController::class,'getUsuarios']);
    Route::post('/getEgresosMotivos', [ReasonExpenseController::class,'getEgresosMotivos']);
    Route::put('/updateFieldOrden/{id}', [OrdenController::class,'updateField']);
    Route::get('/getOrderByBranch/{id}', [OrdenController::class,'getOrderByBranch']);
    Route::put('/updateFieldOrdenDetalle/{id}', [OrdenDetalleController::class,'updateField']);
    Route::post('roles/assignPermissions/{id}', [RoleController::class,'assignPermissions']);
    Route::get('/getTableByBranch/{id}', [MesasController::class,'getTableByBranch']);

    Route::put('/activeProductos/{id}', [ProductController::class,'activeProductos']);
    Route::post('/activeProductos/group', [ProductController::class,'activeProductosGroup']);

    //PROCESS SERVICES
    Route::get('/sales/detail/{id}',  [SalesController::class,'showDetail']);
    Route::get('/purchases/detail/{id}',  [PurchasesController::class,'showDetail']);
    Route::post('/getCompras',  [PurchasesController::class,'getCompras']);
    Route::get('/preLoadData/{id}',  [SalesController::class,'preLoadData']);
    Route::post('/storeByOrder/{id}',  [SalesController::class,'storeByOrder']);
    Route::post('/getComprobantes',  [SalesController::class,'getComprobantes']);
    Route::put('/sales/anular/{id}',  [SalesController::class,'anularComprobante']);


    //GENERAL SERVICES
    Route::get('/getTiposDoc', [ServicesController::class,'getTiposDocumentos']);
    Route::get('/getCategoriasPorTipo/{id}', [ServicesController::class,'getCategoriasPorTipo']);
    Route::get('/getInsumosFecth', [ServicesController::class,'getInsumos']);
    Route::get('/getProductosFetch', [ServicesController::class,'getProductos']);
    Route::get('/getProductosInsumos', [ServicesController::class,'getProductosInsumos']);
    Route::get('/searchProvider', [ServicesController::class,'searchProvider']);
    Route::get('/searchCustomer', [ServicesController::class,'searchCustomer']);
    Route::get('/getSucursalesPorCategoria/{id}', [CategoryController::class,'getSucursalesPorCategoria']);
    Route::get('/getCategoriasTipoSucursal/{id}', [ServicesController::class,'getCategoriasTipoSucursal']);
    Route::get('/getRoles', [ServicesController::class,'getRoles']);
    Route::get('/getProductosSucursal/{id}', [ServicesController::class,'getProductosSucursal']);
    Route::get('/getCategoriasTipoSucursal/{id}', [ServicesController::class,'getCategoriasTipoSucursal']);
    Route::get('/getTiposComprobante', [ServicesController::class,'getTiposComprobante']);
    Route::get('/getMetodosPago', [ServicesController::class,'getMetodosPago']);
    Route::get('/getMoneda', [ServicesController::class,'getMoneda']);
    Route::get('/getSerieComprobante/{id}', [ServicesController::class,'getSerieComprobante']);
    Route::get('/getUserBranchs/{id}', [ServicesController::class,'getUserBranchs']);
    Route::get('/getMozos', [UserController::class,'getMozos']);

    Route::post('searchRuc', [ServicesController::class,'searchRuc']);
    Route::post('searchDni', [ServicesController::class,'searchDni']);
    Route::post('buscarDniRuc', [ServicesController::class,'buscarDniRuc']);



    //CAJA
    Route::post('/getCajasAll', [CashController::class,'getCajasAll']);
    Route::get('/getDetalleCaja/{id}', [CashController::class,'getDetalleCaja']);
    Route::get('/getMontosDelDia/{id}/{rubro}/{flag}', [CashController::class,'getMontosDelDia']);
    Route::post('/getEgresosAll', [ExpenseController::class,'getEgresosAll']);
    Route::get('/getListMotivosEgreso', [ServicesController::class,'getListMotivosEgreso']);


    //REPORTE
    Route::post('/getComprobantesReport', [SalesController::class,'getComprobantesreport']);
    Route::get('/getTiposComp', [ServicesController::class,'getTiposComp']);
    Route::get('/getEstadoComp', [ServicesController::class,'getEstadoComp']);

    Route::post('/getReportIncomes', [ReportController::class,'getReportIncomes']);
    Route::post('/getReportExpenses', [ReportController::class,'getReportExpenses']);
    Route::post('/getReportPurchases', [ReportController::class,'getReportPurchases']);
    Route::post('/getRanking', [ReportController::class,'getRanking']);
    Route::post('/getReportConsolidated', [ReportController::class,'getReportConsolidated']);

    //COCINA
    Route::get('/itemsPrintCocina/{id}', [KitchenController::class,'itemsPrintCocina']);
    Route::get('/itemsPrintBarra/{id}', [KitchenController::class,'itemsPrintBarra']);
    Route::get('/itemsPrintLlevar/{id}', [KitchenController::class,'itemsPrintLlevar']);
    

    //HOTEL
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('bookings', BookingController::class);
    Route::apiResource('booking_detail', BookingsDetailController::class);
    Route::get('/generate-pdf-reservation/{id}',  [BookingController::class,'generatePDF']);
    Route::get('/preLoadDataHotel/{id}',  [BookingController::class,'preLoadData']);
    Route::apiResource('typeroom',TypeRoomController::class);
    Route::apiResource('zonehotel',ZonasHotelController::class);
    Route::apiResource('invoice',SalesHotelController::class);
    Route::post('/getTypeRoom',[TypeRoomController::class,'getCategorias']);
    Route::post('/getZonasHotel',[ZonasHotelController::class,'getZonas']);
    Route::post('/deleteRooms', [RoomController::class, 'destroyMany']);
    Route::post('/deleteBookings', [BookingController::class, 'destroyMany']);
    Route::get('/availableRooms', [RoomController::class, 'availableRooms']);
    Route::get('/room/active', [RoomController::class, 'active']);
    Route::post('/searchProviderByDocument', [ServicesController::class,'searchProviderByDocument']);

    Route::get('/bookingsToday', [BookingController::class, 'bookingsToday']);
    Route::put('/checkin_booking/{id}', [CheckInController::class, 'booking']);
    Route::put('/checkout/{id}', [CheckInController::class, 'checkout']);

    Route::post('/storeByRoom/{id}',  [SalesHotelController::class,'storeByRoom']);

    Route::apiResource('checkin', CheckInController::class);
    Route::put('/updateFieldRoom/{id}', [RoomController::class,'updateField']);

    Route::get('/dashboard/card', [DashboardController::class,'dashboardCards']);
    Route::get('/dashboard/graph', [DashboardController::class,'dashboardGraph']);
    Route::get('/dashboard/sales', [DashboardController::class,'dashboardSales']);


    Route::post('/products/stock', [ProductController::class,'stock']);
    Route::post('/supplies/stock', [SuppliesController::class,'stock']);

    Route::post('/activate/customer/{id}',[CustomerController::class, 'activateCustomer']);
    Route::post('/activate/provider/{id}',[ProviderController::class, 'activateProvider']);

    
    Route::get('/updateStatus/{id}', [MesasOrdenController::class,'updateStatus']);
    Route::post('/validatePasswordAccess', [UserController::class,'validatePasswordAccess']);
    Route::post('/validateCodeAccess', [UserController::class,'validateCodeAccess']);
    Route::get('/getProductosSucursalFetch',[ServicesController::class,'getProductosSucursalFetch']);
    
    Route::apiResource('aditional', AdicionalController::class);

    Route::get('/supplies/branch/{id}', [SuppliesController::class,'branch']);

});
