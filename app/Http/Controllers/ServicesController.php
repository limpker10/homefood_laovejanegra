<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Query\JoinClause;
use App\Models\TipoDocumentos;
use App\Models\TipoComprobantes;
use App\Models\EstadoComprobante;
use App\Models\Categorias;
use App\Models\Insumos;
use App\Models\Productos;
use App\Models\Proveedores;
use App\Models\Clientes;
use App\Models\RolUsuario;
use App\Models\CategoriaSucursal;
use App\Models\MedioPago;
use App\Models\Monedas;
use App\Models\Serie;
use App\Models\EgresosMotivos;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ServicesController extends Controller
{
    public function __constructor(){
        $this->middleware('auth:sanctum');
    }

    public function getTiposDocumentos(){
        try{
            $tipo_doc = TipoDocumentos::get();
            return response()->json($tipo_doc, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getTiposComprobante(){
        try{
            $tipo_comp = TipoComprobantes::get();
            return response()->json($tipo_comp, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getCategoriasPorTipo($id){
        try{
            $tipo_doc = Categorias::where('tipo_producto_insumo',$id)->where('estado',1)->get();
            return response()->json($tipo_doc, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getInsumos(){
        try{
            $insumos = Insumos::where('estado', 1)->get();
            $data = response()->json(['count'=>$insumos->count(), 'entries'=>$insumos]);
            return $data;
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getProductos(){
        try{
            $productos = Productos::where('estado', 1)->get();
            $data = response()->json(['count'=>$productos->count(), 'entries'=>$productos]);
            return $data;
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function searchProvider(){
        try{
            $proveedores = Proveedores::where('estado', 1)->get();
            $data = response()->json(['count'=>$proveedores->count(), 'entries'=>$proveedores]);
            return $data;
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getProductosInsumos(){
        try{
            //$productos = Productos::where([["estado", 1], ["tipo_producto_combo",3]])->get();
            $productos = Productos::where([["estado", 1]])->get();
            $insumos = Insumos::where('estado', 1)->get();
            $data_merge = array();
            $count = 0;
            foreach ($productos as $key) {
                array_push($data_merge,$key);
                $count++;
            }
            foreach ($insumos as $key) {
                array_push($data_merge,$key);
                $count++;
            }
            $data = response()->json(['count'=>$count, 'entries'=>$data_merge]);
            return $data;
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getRoles(){
        try{
            $roles = RolUsuario::get();
            return response()->json($roles, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getCategoriasTipoSucursal($id){
        $usuario_logeado = auth('sanctum')->user();
        try{
            $categorias = CategoriaSucursal::where([
                //["tipo_producto_insumo", $id],
                ["id_sucursal",$usuario_logeado->id_sucursal]
            ])
            ->whereHas('categoria',function  (Builder $query) use($id) {
                $data = $query->where('estado',1)->where("tipo_producto_insumo",$id);
                return $data;
            })->get();
            return response()->json($categorias, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getProductosSucursal($id){
        $usuario_logeado = auth('sanctum')->user();
        try{ /*
            $latestImgesProducts = DB::table('mst_images_productos')
                   ->select('id_producto', 'filename', 'path', DB::raw('MAX(created_at) as created_at'))
                   ->groupBy('id_producto');
            $items = DB::table('mst_producto_sucursal')
                ->join('mst_producto', 'mst_producto.id_producto', '=', 'mst_producto_sucursal.id_producto')
                ->join('mst_categorias', 'mst_categorias.id_categoria', '=', 'mst_producto.id_categoria')
                ->join('mst_categoria_sucursal', 'mst_categoria_sucursal.id_categoria', '=', 'mst_categorias.id_categoria')
                ->join('mst_unidades_medida', 'mst_unidades_medida.id_unidad_medida', '=', 'mst_producto.id_unidad_medida')
                //->leftJoin('mst_images_productos', 'mst_images_productos.id_producto', '=', 'mst_producto.id_producto')
                ->joinSub($latestImgesProducts, 'latest_images', function ($leftJoinSub) {
                    $leftJoinSub->on('mst_producto.id_producto', '=', 'latest_images.id_producto');
                })
                ->select('mst_producto_sucursal.*',
                'mst_producto.nombre_producto',
                'mst_producto.estado',
                'mst_unidades_medida.unidad_medida',
                'mst_unidades_medida.id_unidad_medida',
                'mst_unidades_medida.simbolo',
                'mst_producto.nombre_producto',
                'mst_producto.precio',
                'mst_categorias.nombre',
                'mst_categoria_sucursal.*',
                //'mst_images_productos.filename',
                //'mst_images_productos.path',
                'mst_producto.codigo_producto',
                'mst_producto.tipo_producto_combo',
                'mst_producto.cocina')
                ->where('mst_producto_sucursal.id_sucursal',$usuario_logeado->id_sucursal)
                //->where('mst_categorias.tipo_producto_insumo', 1)
                ->where('mst_producto.estado', '1')
                ->where('mst_producto.activo',1)
                ->where('mst_categorias.estado',1);
            if($id!=0){
                $items = $items->where('mst_categoria_sucursal.id_categoria_sucursal', $id);
            }
            */
            $items = DB::table('mst_producto_sucursal')
                ->join('mst_producto', 'mst_producto.id_producto', '=', 'mst_producto_sucursal.id_producto')
                ->join('mst_categorias', 'mst_categorias.id_categoria', '=', 'mst_producto.id_categoria')
                ->join('mst_categoria_sucursal', 'mst_categoria_sucursal.id_categoria', '=', 'mst_categorias.id_categoria')
                ->join('mst_unidades_medida', 'mst_unidades_medida.id_unidad_medida', '=', 'mst_producto.id_unidad_medida')
                ->leftJoin('mst_images_productos', 'mst_images_productos.id_producto', '=', 'mst_producto.id_producto')
                ->select('mst_producto_sucursal.*', 
                'mst_producto.nombre_producto', 
                'mst_producto.estado', 
                'mst_unidades_medida.unidad_medida',
                'mst_unidades_medida.simbolo',
                'mst_producto.nombre_producto',
                'mst_producto.tipo_producto_combo',
                'mst_categorias.nombre',
                'mst_producto.cocina',
                'mst_producto.imprimir_cocina',
                'mst_producto.imprimir_barra',
                'mst_categoria_sucursal.*',
                'mst_images_productos.filename',
                'mst_images_productos.path')
                ->where('mst_producto_sucursal.id_sucursal',$usuario_logeado->id_sucursal)
                ->where('mst_producto.estado', '1')
                ->where('mst_producto.activo',1)
                ->where('mst_categorias.estado',1);
            if($id!=0){
                $items = $items->where('mst_categoria_sucursal.id_categoria_sucursal', $id);
            }
            $items = $items->get();
            return response()->json($items, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function searchCustomer(){
        try{
            $clientes = Clientes::where('estado', 1)->get();
            $data = response()->json(['count'=>$clientes->count(), 'entries'=>$clientes]);
            return $data;
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }


    public function getMetodosPago(){
        try{
            $metodos = MedioPago::get();
            return response()->json($metodos, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getMoneda(){
        try{
            $monedas = Monedas::get();
            return response()->json($monedas, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getSerieComprobante($id){

        $usuario_logeado = auth('sanctum')->user();
        try{
            $serie = Serie::where([["id_tipo_comprobante", $id], ["id_sucursal",$usuario_logeado->id_sucursal]])->get();
            return response()->json($serie, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function searchRuc(Request $request){
        $url_consulta = config('global.url_api_ruc');

        $response = Http::get($url_consulta.$request->ruc);

        return $response->json();
    }

    public function searchDni(Request $request){
        $url_consulta = config('global.url_api_dni');

        $response = Http::get($url_consulta.$request->dni);

        return $response->json();
    }


    public function getListMotivosEgreso(){
        try{
            $motivos = EgresosMotivos::where('estado', 1)->get();
            return response()->json($motivos, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getEstadoComp() {
        try{
            $estado = EstadoComprobante::get();
            return response()->json($estado, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getUserBranchs($id){
        try{
            $usuario_logeado = auth('sanctum')->user();

            $data = DB::select('SELECT mst_sucursales.*, caja_dia.id_caja
            FROM mst_sucursales
            LEFT JOIN (
                SELECT * FROM prc_caja
                WHERE prc_caja.fecha_cierre IS NULL
                AND prc_caja.rubro = "'.$id.'"
            )caja_dia ON mst_sucursales.id_sucursal = caja_dia.id_sucursal
            WHERE mst_sucursales.id_sucursal ='.$usuario_logeado->id_sucursal.'');

            $sinCerrar = DB::select("SELECT id_caja, fecha_apertura,id_sucursal	FROM prc_caja WHERE fecha_cierre IS NULL AND rubro = '$id' ORDER BY id_caja DESC");
            return response()->json(['data' => $data,'pendientes' => $sinCerrar], 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function buscarDniRuc(Request $request)
    {/*
        $urlDni = "http://bytesoluciones.com/apidnix/apidni.php?dni=";
        $urlRuc = "http://144.217.215.6/sunat/libre.php?ruc=";
        $url = "";
        if ($request->tipoDoc == 1) {
            $url = $urlRuc.$request->numDoc;
            $json = file_get_contents($url);
            $datos = json_decode($json,true);
            return $datos;
        } elseif ($request->tipoDoc == 2) {
            $url = $urlDni.$request->numDoc;
            $json = file_get_contents($url);
            $datos = json_decode($json,true);
            return $datos;
        } else {
            echo "Error";
        }*/

        
        $urlDni = "https://apiperu.dev/api/dni/"; //"http://bytesoluciones.com/apidnix/apidni.php?dni=";
		$urlRuc = "https://apiperu.dev/api/ruc/";
		$token = "7c0d548772a5b9c006251f7a404bbaa955c5b201101c4fb1d3c021b02bf0ae5f";

		$urlDniBackup = "https://api.apis.net.pe/v1/dni?numero=";
		$urlRucBackup = "https://api.apis.net.pe/v1/ruc?numero=";
		$backupToken = "apis-token-1238.fDzMjc4tYo9lBD53v-mFlOFDWWc7yaU8";

		$url = "";
		if ($request->tipoDoc == 1) {
			// Consulta a la API principal
			$url = $urlRuc . $request->numDoc . "?api_token=" . $token;
			$json = file_get_contents($url);
			$datos = json_decode($json, true);

			if ($datos["success"] == true) {
				$retorno = [
					"success" => true,
					"nombre_o_razon_social" => $datos["data"]["nombre_o_razon_social"],
					"direccion_completa" => $datos["data"]["direccion_completa"]
				];
			} else {
				// API de respaldo
				$url = $urlRucBackup . $request->numDoc;
				$response = Http::withHeaders([
					'Authorization' => 'Bearer ' . $backupToken
				])->get($url);

				if ($response->successful()) {
					$backupDatos = $response->json();
					$retorno = [
						"success" => true,
						"nombre_o_razon_social" => $backupDatos["nombre"],
						"direccion_completa" => $backupDatos["direccion"]
					];
				}
			}
			return response()->json($retorno, 200);
		} elseif ($request->tipoDoc == 2) {
			// Consulta a la API principal
			// URL de la API principal
            $url = $urlDni . $request->numDoc . "?api_token=" . $token;

            try {
                // Límite de tiempo de 5 segundos para la API principal
                $response = Http::timeout(3)->get($url);
                $datos = $response->json();
        
                // Si la API principal responde correctamente, devolver los datos
                if (!empty($datos["success"]) && $datos["success"] === true) {
                    return response()->json([
                        "success" => true,
                        "result" => [
                            "Nombres" => $datos["data"]["nombres"] ?? '',
                            "Apellidos" => trim(($datos["data"]["apellido_paterno"] ?? '') . " " . ($datos["data"]["apellido_materno"] ?? ''))
                        ]
                    ], 200);
                }
            } catch (\Exception $e) {
                Log::error("Error o timeout en API principal: " . $e->getMessage());
            }
            try {
                $urlBackup = $urlDniBackup . $request->numDoc;
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $backupToken
                ])->get($urlBackup);
        
                if ($response->successful()) {
                    $backupDatos = $response->json();
        
                    return response()->json([
                        "success" => true,
                        "result" => [
                            "Nombres" => $backupDatos["nombres"] ?? '',
                            "Apellidos" => trim(($backupDatos["apellidoPaterno"] ?? '') . " " . ($backupDatos["apellidoMaterno"] ?? ''))
                        ]
                    ], 200);
                } else {
                    throw new \Exception("Error en API de respaldo");
                }
            } catch (\Exception $e) {
                Log::error("Error al consultar API de respaldo: " . $e->getMessage());
        
                return response()->json([
                    "success" => false,
                    "message" => "No se pudo obtener información del DNI en ninguna de las APIs."
                ], 500);
            }
		}
    }
    public function searchProviderByDocument(Request $request){
        try{
            if(!empty($request->nro_doc)){
                $proveedores = Proveedores::where('estado', 1)
                    ->where('nro_doc',$request->nro_doc)
                    ->first();
                return response()->json($proveedores);
            }

        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
    
    public function getProductosSucursalFetch(){
        $usuario_logeado = auth('sanctum')->user();
        try{
            $items = DB::table('mst_producto_sucursal')
                ->join('mst_producto', 'mst_producto.id_producto', '=', 'mst_producto_sucursal.id_producto')
                ->join('mst_categorias', 'mst_categorias.id_categoria', '=', 'mst_producto.id_categoria')
                ->join('mst_categoria_sucursal', 'mst_categoria_sucursal.id_categoria', '=', 'mst_categorias.id_categoria')
                ->join('mst_unidades_medida', 'mst_unidades_medida.id_unidad_medida', '=', 'mst_producto.id_unidad_medida')
                ->leftJoin('mst_images_productos', 'mst_images_productos.id_producto', '=', 'mst_producto.id_producto')
                ->select('mst_producto_sucursal.*', 
                'mst_producto.nombre_producto', 
                'mst_producto.estado', 
                'mst_unidades_medida.unidad_medida',
                'mst_unidades_medida.simbolo',
                'mst_producto.nombre_producto',
                'mst_producto.tipo_producto_combo',
                'mst_categorias.nombre',
                'mst_categoria_sucursal.*',
                'mst_images_productos.filename',
                'mst_images_productos.path')
                ->where('mst_producto_sucursal.id_sucursal',$usuario_logeado->id_sucursal)
                ->where('mst_producto.estado', '1')
                ->where('mst_producto.activo',1)
                ->where('mst_categorias.estado',1);
            
            $items = $items->get();
            return response()->json(['count'=>$items->count(), 'entries'=>$items], 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

}
