<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Egresos;
use App\Models\Caja;
use App\Models\Comprobante;
use App\Models\Compras;


use App\Models\Insumos;
use App\Models\Productos;
use App\Models\ProductosSucursal;

class ReportController extends Controller
{
    public function getRanking(Request $request){
        try{
            $usuario_logeado = auth('sanctum')->user();
            $data = DB::select("SELECT 
            prc_comprobante_detalle.nombre_producto AS nombre_producto, 
            SUM(prc_comprobante_detalle.cantidad) as cantidad,
            SUM(prc_comprobante_detalle.precio_total) as precio_total,
            mst_producto.codigo_producto as codigo_producto,
            mst_categorias.nombre as nombre_categoria
            FROM prc_comprobante
            INNER JOIN prc_comprobante_detalle ON prc_comprobante.id_comprobante = prc_comprobante_detalle.id_comprobante
            INNER JOIN mst_producto ON prc_comprobante_detalle.id_producto = mst_producto.id_producto
            INNER JOIN mst_categorias ON mst_producto.id_categoria = mst_categorias.id_categoria
            WHERE prc_comprobante.id_estado_comprobante <> 2
            AND DATE(prc_comprobante.created_at) BETWEEN '".$request->fecha_inicio."' AND '".$request->fecha_fin."'
            GROUP BY prc_comprobante_detalle.nombre_producto
            ORDER BY cantidad DESC");
            return response()->json($data, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getReportIncomes(Request $request){
        try{
            $from = date($request->data["fecha_inicio"]);
            $to = date($request->data["fecha_fin"]);
            $usuario_logeado = auth('sanctum')->user();

            if(empty($request->data["fecha_inicio"]) && empty($request->data["fecha_fin"]) && ($request->data["id_tipo_comprobante"]==0) && ($request->data["id_estado"]==0)){
                $datos = array();
            }
            if(!empty($request->data["fecha_inicio"]) && !empty($request->data["fecha_fin"]) && ($request->data["id_tipo_comprobante"]==0) && ($request->data["id_estado"]==0)){
                $datos = Comprobante::whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])
                ->where("id_sucursal",$usuario_logeado->id_sucursal)
                ->paginate($request->perpage);
            }
            return response()->json($datos, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getReportExpenses(Request $request){
        try{
            $from = date($request->data["fecha_inicio"]);
            $to = date($request->data["fecha_fin"]);
    
            $usuario_logeado = auth('sanctum')->user();
            if(!empty($request->data["fecha_inicio"]) && !empty($request->data["fecha_fin"])){
                $datos = Egresos::whereBetween('fecha_egreso', [$from." 00:00:00", $to." 23:59:59"])
                ->where("id_sucursal",$usuario_logeado->id_sucursal)
                ->paginate($request->perpage);
            }else{
                $datos = array();
            }
            return response()->json($datos, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getReportPurchases(Request $request){
        try{
            $from = date($request->data["fecha_inicio"]);
            $to = date($request->data["fecha_fin"]);
    
            $usuario_logeado = auth('sanctum')->user();
            if(!empty($request->data["fecha_inicio"]) && !empty($request->data["fecha_fin"])){
                $datos = Compras::where([
                    ["id_sucursal", $usuario_logeado->id_sucursal]
                ])
                ->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])
                ->paginate($request->perpage);
            }else{
                $datos = array();
            }
            return response()->json($datos, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }


    
    /**Consolidado */

    public function getReportConsolidated(Request $request){
        try{
            $usuario_logeado = auth('sanctum')->user();
            $datos_ventas = DB::table('prc_comprobante')
            ->whereBetween('prc_comprobante.created_at', [$request->fecha_inicio." 00:00:00", $request->fecha_fin." 23:59:59"])
            ->where("prc_comprobante.id_sucursal", $usuario_logeado->id_sucursal)
            ->where("prc_comprobante.id_estado_comprobante", 1)
            ->join('prc_comprobante_detalle', 'prc_comprobante.id_comprobante', '=', 'prc_comprobante_detalle.id_comprobante')
            ->join('mst_producto', 'prc_comprobante_detalle.id_producto', '=', 'mst_producto.id_producto')
            ->join('mst_categorias', 'mst_producto.id_categoria', '=', 'mst_categorias.id_categoria')
            ->select(
                DB::raw('mst_producto.codigo_producto as codigo_producto'),
                DB::raw('prc_comprobante_detalle.nombre_producto AS nombre_producto'),
                DB::raw('mst_categorias.nombre as nombre_categoria'), 
                DB::raw('SUM(prc_comprobante_detalle.cantidad) as cantidad'),
                DB::raw('SUM(prc_comprobante_detalle.precio_total) as precio_total'))
            ->groupBy('prc_comprobante_detalle.nombre_producto')
            ->orderBy('cantidad', 'desc')
            ->get();

            $datos_compras = DB::table('prc_compras')
            ->whereBetween('prc_compras.created_at', [$request->fecha_inicio." 00:00:00", $request->fecha_fin." 23:59:59"])
            ->where("prc_compras.id_sucursal", $usuario_logeado->id_sucursal)
            ->join('prc_compras_detalle', 'prc_compras.id_compra', '=', 'prc_compras_detalle.id_compra')
            ->leftJoin('mst_producto', 'prc_compras_detalle.id_producto', '=', 'mst_producto.id_producto')
            ->leftJoin('mst_insumos', 'prc_compras_detalle.id_insumo', '=', 'mst_insumos.id_insumo')
            ->leftJoin('mst_categorias AS mcp', 'mst_producto.id_categoria', '=', 'mcp.id_categoria')
            ->leftJoin('mst_categorias AS mci', 'mst_insumos.id_categoria', '=', 'mci.id_categoria')
            ->select(
                DB::raw('mst_producto.codigo_producto as codigo_producto'),
                DB::raw('prc_compras_detalle.nombre_producto_insumo AS nombre_producto'),
                DB::raw('mcp.nombre as nombre_categoria_producto'), 
                DB::raw('mci.nombre as nombre_categoria_insumo'), 
                DB::raw('SUM(prc_compras_detalle.cantidad) as cantidad'),
                DB::raw('SUM(prc_compras_detalle.total) as precio_total'))
            ->groupBy('prc_compras_detalle.nombre_producto_insumo')
            ->get();

            $datos_egresos = Egresos::whereBetween('fecha_egreso', [$request->fecha_inicio." 00:00:00", $request->fecha_fin." 23:59:59"])
            ->where("id_sucursal",$usuario_logeado->id_sucursal)
            ->where("estado", 1)
            ->get();

            $data = [];
            $data["ventas"] = $datos_ventas;
            $data["compras"] = $datos_compras;
            $data["egresos"] = $datos_egresos;


            return response()->json($data, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    /**Stock Insumos/Productos */

    public function getReportStock(Request $request){
        try{
            if($request->type=='supplies')
            {
                /** Insumos
                *Productos
                *ProductosSucursal */
                $data = DB::table('mst_insumos')
                        ->join('mst_categorias', 'mst_categorias.id_categoria', '=', 'mst_insumos.id_categoria')
                        ->join('mst_unidades_medida', 'mst_unidades_medida.id_unidad_medida', '=', 'mst_insumos.id_unidad_medida')
                        ->select(
                            DB::raw('mst_insumos.nombre_insumo as nombre_insumo_producto'),
                            DB::raw('mst_insumos.codigo_insumo as codigo_insumo_producto'),
                            DB::raw('mst_insumos.stock_minimo as stock_minimo'),
                            DB::raw('mst_insumos.stock as stock'),
                            DB::raw('mst_categorias.nombre as nombre_categoria'),
                            DB::raw('mst_unidades_medida.unidad_medida as unidad_medida'),
                        )
                        ->get();
            }
            if($request->type=='products'){
                $data = ProductosSucursal::get();
            }
            return response()->json($data, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}
