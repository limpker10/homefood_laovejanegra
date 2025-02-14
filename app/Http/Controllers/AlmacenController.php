<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\AlmacenMovimientos;
use App\Models\InsumosSucursal;
use App\Models\ProductosSucursal;
use Illuminate\Support\Facades\DB;

class AlmacenController extends Controller
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

    public function getAlmacen(Request $request){
        try{
            $from = date($request->data["fecha_inicio"]);
            $to = date($request->data["fecha_fin"]);
            $usuario_logeado = auth('sanctum')->user();
            $datos = [];
            if(empty($request->data["fecha_inicio"]) && empty($request->data["fecha_fin"])){
                $datos = AlmacenMovimientos::where("id_sucursal",$usuario_logeado->id_sucursal)
                ->orderBy('created_at', 'desc')
                ->paginate($request->perpage);
            }
            if(!empty($request->data["fecha_inicio"]) && !empty($request->data["fecha_fin"])){
                $datos = AlmacenMovimientos::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])
                ->where("id_sucursal",$usuario_logeado->id_sucursal)
                ->orderBy('created_at', 'desc')
                ->paginate($request->perpage);
            }
            return $datos;
            //return $datos->paginate($request->perpage)->last();
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getAlmacenProductos(Request $request){
        try{
            $from = date($request->data["fecha_inicio"]);
            $to = date($request->data["fecha_fin"]);
            $usuario_logeado = auth('sanctum')->user();
            $datos = [];
            if(empty($request->data["fecha_inicio"]) && empty($request->data["fecha_fin"])){
                $datos = AlmacenMovimientos::where("id_sucursal",$usuario_logeado->id_sucursal)
                ->where('tipo_producto_insumo', 0)
                ->orderBy('created_at', 'desc')
                ->paginate($request->perpage);
            }
            if(!empty($request->data["fecha_inicio"]) && !empty($request->data["fecha_fin"])){
                $datos = AlmacenMovimientos::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])
                ->where("id_sucursal",$usuario_logeado->id_sucursal)
                ->where('tipo_producto_insumo', 0)
                ->orderBy('created_at', 'desc')
                ->paginate($request->perpage);
            }
            return $datos;
            //return $datos->paginate($request->perpage)->last();
        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getAlmacenInsumos(Request $request){
        try{
            $from = date($request->data["fecha_inicio"]);
            $to = date($request->data["fecha_fin"]);
            $usuario_logeado = auth('sanctum')->user();
            $datos = [];
            if(empty($request->data["fecha_inicio"]) && empty($request->data["fecha_fin"])){
                $datos = AlmacenMovimientos::where("id_sucursal",$usuario_logeado->id_sucursal)
                ->where('tipo_producto_insumo', 1)
                //->orderBy('created_at', 'desc')
                ->paginate($request->perpage);
            }
            if(!empty($request->data["fecha_inicio"]) && !empty($request->data["fecha_fin"])){
                $datos = AlmacenMovimientos::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])
                ->where("id_sucursal",$usuario_logeado->id_sucursal)
                ->where('tipo_producto_insumo', 1)
                ->orderBy('id_almacen_movimientos', 'desc')
                ->paginate($request->perpage);
            }
            return $datos;
            //return $datos->paginate($request->perpage)->last();
        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getInventarioInsumos(Request $request){
        try{
            $from = date($request->data["fecha_inicio"]);
            $to = date($request->data["fecha_fin"]);
            $usuario_logeado = auth('sanctum')->user();
            $datos = [];

            $datos = InsumosSucursal::where("id_sucursal",$usuario_logeado->id_sucursal)
            ->where('estado', 1)
            ->paginate($request->perpage);

            return $datos;
            //return $datos->paginate($request->perpage)->last();
        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }


    public function getInventarioProductos(Request $request){
        try{
            $from = date($request->data["fecha_inicio"]);
            $to = date($request->data["fecha_fin"]);
            $usuario_logeado = auth('sanctum')->user();
            $datos = [];

            $datos = DB::table('mst_producto')
            ->join('mst_producto_sucursal', 'mst_producto.id_producto', '=', 'mst_producto_sucursal.id_producto')
            ->join('mst_categorias', 'mst_categorias.id_categoria', '=', 'mst_producto.id_categoria')
            ->select('mst_categorias.nombre', 'mst_producto.nombre_producto', 'mst_producto.codigo_producto', 'mst_producto_sucursal.stock','mst_producto_sucursal.id_producto_sucursal')
            ->where('tipo_producto_combo', 3)
            ->paginate($request->perpage);

            return $datos;
            //return $datos->paginate($request->perpage)->last();
        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}
