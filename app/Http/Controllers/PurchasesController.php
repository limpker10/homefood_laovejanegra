<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Compras;
use App\Models\ComprasDetalle;
use App\Models\AlmacenMovimientos;
use App\Models\Productos;
use App\Models\Insumos;
use App\Models\Egresos;
use App\Models\Caja;

use App\Models\ProductosSucursal;
use App\Models\InsumosSucursal;

class PurchasesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $usuario_logeado = auth('sanctum')->user();
            $_merge = $request->header;
            $_merge['id_usuario'] = $usuario_logeado->id;
            $_merge['id_sucursal'] = $usuario_logeado->id_sucursal;
            $compra = Compras::create(collect($_merge)->all());

            foreach ($request->detail as $value) {
                $value['id_compra']=$compra->id_compra;
                ComprasDetalle::create(collect($value)->all());

                $act = 0;

                if($value['tipo_compra']==0){
                    $productoGeneral = Productos::findOrFail($value['id_producto']);
                    $upd_data = array( 'stock' => $productoGeneral->stock + $value['cantidad']);
                    $productoGeneral->update($upd_data);

                    $producto = ProductosSucursal::where('id_producto',$value['id_producto'])->first();
                    $act = $producto->stock;
                    $upd_data = array( 'stock' => $producto->stock + $value['cantidad']);
                    $producto->update($upd_data);

                }
                if($value['tipo_compra']==1){
                    $productoGeneral = Insumos::findOrFail($value['id_insumo']);
                    $upd_data = array( 'stock' => $productoGeneral->stock + $value['cantidad']);
                    $productoGeneral->update($upd_data);

                    $producto = InsumosSucursal::where('id_insumo',$value['id_insumo'])->first();
                    $act = $producto->stock;
                    $upd_data2 = array( 'stock' => $producto->stock + $value['cantidad']);
                    $producto->update($upd_data);
                }
                
                $movimiento = new AlmacenMovimientos();
                $movimiento->id_sucursal = $usuario_logeado->id_sucursal;
                $movimiento->id_usuario  = $usuario_logeado->id;
                $movimiento->tipo_producto_insumo = $value['tipo_compra'];
                $movimiento->nombre_producto_insumo = $value['nombre_producto_insumo'];
                $movimiento->cantidad = $value['cantidad'];
                $movimiento->precio_unitario = $value['precio_compra_unitario'];
                $movimiento->precio_total = $value['total'];
                $movimiento->stock_actual =  $act;// - $value['cantidad'];
                $movimiento->fecha_movimiento = date('Y-m-d');
                
                
                if($value['tipo_compra']==0){
                    $movimiento->id_producto = $value['id_producto'];
                    $movimiento->id_tipo_movimiento = 1;
                }
                if($value['tipo_compra']==1){
                    $movimiento->id_insumo = $value['id_insumo'];
                    $movimiento->id_tipo_movimiento = 2;
                }
                $movimiento->save();


            }

            if($request->header['caja']){
                $usuario_logeado = auth('sanctum')->user();
                $caja = Caja::where("id_sucursal",$usuario_logeado->id_sucursal)
                    ->whereNull("fecha_cierre")
                    ->where('rubro',$request->header['rubro'])
                    ->first();
                $egreso = Egresos::create([
                    'fecha_egreso'  => date('Y-m-d H:i:s'),
                    'monto'         => $request->header['total'],
                    'detalle'       => 'Compras',
                    'id_usuario'    => $usuario_logeado->id,
                    'id_sucursal'   => $usuario_logeado->id_sucursal,
                    'id_caja'    => $caja->id_caja,
                    'id_motivo_egreso'  => 0,
                    'id_compra'     => $compra->id_compra,
                    'caja_chica'  => $request->header['caja'] ? 1:0,
                    'rubro'=> $request->header['rubro']
                ]);
            }
            return response()->json($compra, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $compra = Compras::findOrFail($id);
            return response()->json($compra, 200);
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function showDetail($id)
    {
        try{
            $compra = ComprasDetalle::where('id_compra',$id)->get();
            return response()->json($compra, 200);
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //ANULAR COMPRA
        try{
            
            $usuario_logeado = auth('sanctum')->user();
            $detalle = ComprasDetalle::where('id_compra', $id)->get();
            foreach ($detalle as $value) {
                $movimiento = new AlmacenMovimientos();
                $movimiento->id_sucursal = $usuario_logeado->id_sucursal;
                $movimiento->id_usuario  = $usuario_logeado->id;
                $movimiento->nombre_producto_insumo = $value['nombre_producto_insumo'];
                $movimiento->cantidad = $value['cantidad'];
                $movimiento->precio_unitario = $value['precio_unitario'];
                $movimiento->precio_total = $value['precio_total'];
                $movimiento->fecha_movimiento = date('Y-m-d');
                if(isset($value->id_producto)){
                    $prd = ProductosSucursal::where('id_producto',$value['id_producto'])
                                ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                ->first();
                    $movimiento->id_producto = $value['id_producto'];
                    $movimiento->tipo_producto_insumo = 0;
                    $movimiento->stock_actual = $prd->stock -  $value['cantidad'];
                    $movimiento->id_tipo_movimiento = 3;
                }
                if(isset($value->id_insumo)){
                    $prd = InsumosSucursal::where('id_insumo',$value['id_insumo'])
                                ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                ->first();
                    $movimiento->id_insumo = $value['id_insumo'];
                    $movimiento->tipo_producto_insumo = 1;
                    $movimiento->stock_actual = $prd->stock - $value['cantidad'];
                    $movimiento->id_tipo_movimiento = 4;
                }
                $movimiento->save();

                if(isset($value->id_producto)){               
                    //$producto = Productos::findOrFail($value['id_producto']);
                    $producto = ProductosSucursal::where('id_producto',$value['id_producto'])
                                ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                ->first();
                    $upd_data = array( 'stock' => $producto['stock'] - $value['cantidad']);
                    $producto->update($upd_data);
                }
                if(isset($value->id_insumo)){
                    //$producto = Insumos::findOrFail($value['id_insumo']);
                    $insumo = InsumosSucursal::where('id_insumo',$value['id_insumo'])
                                ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                ->first();
                    $upd_data = array( 'stock' => $insumo['stock'] - $value['cantidad']);
                    $insumo->update($upd_data);
                }

                //ComprasDetalle::destroy($detalle->id_compra_detalle );
                $cd = ComprasDetalle::findOrFail($value->id_compra_detalle);
                $cd->delete();
            }
            
            $egreso = Egresos::where('id_compra', $id)
                            ->first();
            if($egreso) $egreso->delete();
            
            $compra = Compras::findOrFail($id);
            $compra->delete();
        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getCompras(Request $request){
        try{
            $usuario_logeado = auth('sanctum')->user();
            $datos = Compras::where([
                ["id_sucursal", $usuario_logeado->id_sucursal]
            ])->latest();
            return $datos->paginate($request->perpage);
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}
