<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdenDetalle;
use App\Models\MesaOrden;
use App\Models\Mesas;
use App\Models\Productos;
use App\Models\Orden;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
class KitchenController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    public function itemsPrintCocina($id){
        try{
            $orden = Orden::where("id_orden", $id)->first();
            //dd($orden);

            $usuario = User::where("id", $orden->id_usuario)->first();

            $mesaorden = MesaOrden::where("id_orden", $id)->first();
            
            $detalle = OrdenDetalle::whereHas('producto',function (Builder $query) {
                            return $query->where('imprimir_cocina',1);
                        })
                        ->where('id_orden', $id)->get();
            //dd($mesaorden->id_mesa);
            if(isset($mesaorden->id_mesa)){
                $mesa = Mesas::find($mesaorden->id_mesa);
                $mesa_zona = $mesa->zona->nombre ." | ".$mesa->nro_mesa;
            }else{
                $mesa_zona = "Para Llevar";
            }
            

            $datos_print = array(
                "restaurante" => $orden->sucursal->nombre_sucursal,
                "mesa" => $mesa_zona,
                "mozo" => $usuario->name,
                "url_print" => $orden->sucursal->url_print_cocina,
            );

            foreach ($detalle as $key) {
                $cantidad_print = $key->cantidad - $key->contador_comanda;
                if($key->cantidad > $key->contador_comanda){
                    $producto = Productos::find($key->id_producto);
                    if(empty($key->detalle_combo)){
                        if($producto->tipo_producto_combo != 2){
                            $datos_print["items"][] = array(
                                "producto" => $producto->nombre_producto,
                                "cantidad" => $cantidad_print,
                                "observaciones" => $key->observaciones
                            );
                        }
                    }
                    else{
                        $key->detalle_combo = json_decode($key->detalle_combo, true);
                        foreach ($key->detalle_combo as $value) {
                            $producto_combo = Productos::find($value["id_producto"]);
                            if($producto_combo->imprimir_cocina==1){
                                $datos_print["items"][] = array(
                                    "producto" => '['.$producto->nombre_producto.'] '.$value["nombre_producto"],
                                    "cantidad" => $value["cantidad"],
                                    "observaciones" => ""
                                );
                            }
                        }
                    }
                    $od = OrdenDetalle::find($key->id_detalle_orden);
                    $od->contador_comanda += $cantidad_print;
                    $od->save();
                }
            }
            
            return response()->json($datos_print);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function itemsPrintBarra($id){
        try{
            $orden = Orden::where("id_orden", $id)->first();
            //dd($orden);

            $usuario = User::where("id", $orden->id_usuario)->first();

            $mesaorden = MesaOrden::where("id_orden", $id)->first();
            
            $detalle = OrdenDetalle::whereHas('producto',function (Builder $query) {
                            return $query->where('imprimir_barra',1);
                        })
                        ->where('id_orden', $id)->get();
            //dd($mesaorden->id_mesa);
            if(isset($mesaorden->id_mesa)){
                $mesa = Mesas::find($mesaorden->id_mesa);
                $mesa_zona = $mesa->zona->nombre ." | ".$mesa->nro_mesa;
            }else{
                $mesa_zona = "Para Llevar";
            }
            

            $datos_print = array(
                "restaurante" => $orden->sucursal->nombre_sucursal,
                "mesa" => $mesa_zona,
                "mozo" => $usuario->name,
                "url_print" => $orden->sucursal->url_print_barra,
            );

            foreach ($detalle as $key) {
                /*if($key->cantidad > $key->contador_comanda){
                    $producto = Productos::find($key->id_producto);
                    
                    $datos_print["items"][] = array(
                        "producto" => $producto->nombre_producto,
                        "cantidad" => $key->cantidad,
                        "observaciones" => $key->observaciones
                    );
                    $od = OrdenDetalle::find($key->id_detalle_orden);
                    $od->contador_comanda = $key->contador_comanda + $key->cantidad;
                    $od->save();
                }*/


                if($key->cantidad > $key->contador_comanda){
                    $producto = Productos::find($key->id_producto);
                    if(empty($key->detalle_combo)){
                        if($producto->tipo_producto_combo != 2){
                            $datos_print["items"][] = array(
                                "producto" => $producto->nombre_producto,
                                "cantidad" => $key->cantidad,
                                "observaciones" => $key->observaciones
                            );
                        }
                    }
                    else{
                        $key->detalle_combo = json_decode($key->detalle_combo, true);
                        foreach ($key->detalle_combo as $value) {
                            $producto_combo = Productos::find($value["id_producto"]);
                            if($producto_combo->imprimir_barra==1){
                                $datos_print["items"][] = array(
                                    "producto" => '['.$producto->nombre_producto.'] '.$value["nombre_producto"],
                                    "cantidad" => $value["cantidad"],
                                    "observaciones" => ""
                                );
                            }
                        }
                    }
                    $od = OrdenDetalle::find($key->id_detalle_orden);
                    $od->contador_comanda = $key->contador_comanda + $key->cantidad;
                    $od->save();
                }


            }
            
            return response()->json($datos_print);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function itemsPrintLlevar($id){
        try{
            $orden = Orden::where("id_orden", $id)->first();
            //dd($orden);

            $usuario = User::where("id", $orden->id_usuario)->first();

            $detalle = OrdenDetalle::where('id_orden', $id)->get();
            
            $mesa_zona = "Para Llevar";
            

            $datos_print = array(
                "restaurante" => $orden->sucursal->nombre_sucursal,
                "mesa" => $mesa_zona,
                "mozo" => $usuario->name,
                "url_print" => $orden->sucursal->url_print_cocina,
            );

            foreach ($detalle as $key) {
                $cantidad_print = $key->cantidad - $key->contador_comanda;
                if($key->cantidad > $key->contador_comanda){
                    $producto = Productos::find($key->id_producto);
                    if(empty($key->detalle_combo)){
                        if($producto->tipo_producto_combo != 2){
                            $datos_print["items"][] = array(
                                "producto" => $producto->nombre_producto,
                                "cantidad" => $key->cantidad,
                                "observaciones" => $key->observaciones
                            );
                        }
                    }
                    else{
                        $key->detalle_combo = json_decode($key->detalle_combo, true);
                        foreach ($key->detalle_combo as $value) {
                            $producto_combo = Productos::find($value["id_producto"]);
                            if($producto_combo->imprimir_cocina==1){
                                $datos_print["items"][] = array(
                                    "producto" => '['.$producto->nombre_producto.'] '.$value["nombre_producto"],
                                    "cantidad" => $value["cantidad"],
                                    "observaciones" => ""
                                );
                            }
                        }
                    }
                    $od = OrdenDetalle::find($key->id_detalle_orden);
                    $od->contador_comanda += $cantidad_print;
                    $od->save();
                }
            }
            
            return response()->json($datos_print);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
