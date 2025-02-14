<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Productos;
use App\Models\ProductosSucursal;
use App\Models\RecetasProducto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
    public function __constructor(){
        $this->middleware('auth:sanctum');
    }
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
            $producto = Productos::create($request->all());
            if(isset($request->recetas_productos) && !empty($request->recetas_productos) && count($request->recetas_productos) > 0){
                foreach ($request->recetas_productos as $value) {
                    RecetasProducto::create([
                        'id_receta'  => $value,
                        'id_producto' => $producto->id_producto
                    ]);
                }
            }

            foreach($request->sucursales as $sucursal){
                ProductosSucursal::create([
                    "id_producto" => $producto->id_producto,
                    "id_sucursal" => $sucursal,
                    "stock" => $producto->stock,
                    "precio" => $producto->precio
                ]);
            }
            return response()->json($producto, 200);

        }catch(\Exception $e){
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
    /*public function update(Request $request, $id)
    {
        try{
            $producto = Productos::findOrFail($id);
            $producto->update($request->all());
            //dd($request->sucursales);
            if(count($request->sucursales) > 0){

                foreach($request->sucursales as $suc){
                    ProductosSucursal::where("id_producto", $request->id_producto)->where("id_sucursal", $suc)->update(['precio' => $request->precio]);
                }
            }

            return response()->json($producto, 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }*/
    public function update(Request $request, $id)
    {
        try{

            $producto = Productos::findOrFail($id);
            $producto->update($request->all());
            //Array de sucursales de la categoria antes de actualizar
            $sucursalesBD = ProductosSucursal::select("id_sucursal","estado")->where("id_producto",$id)->get();
            $sucursalesUpdate = $request->sucursales;
            $alreadyCreated = array();
            $desactivated = array();
            $activated = array();
            //Ya existentes
            foreach ($sucursalesBD as $sucursalBD) {
                if(in_array( (int) $sucursalBD->id_sucursal, $sucursalesUpdate)){
                    $activar = ProductosSucursal::where("id_sucursal",$sucursalBD->id_sucursal)
                        ->where("id_producto",$producto->id_producto)
                        ->first();
                    $activar->estado = 1;
                    $activar->precio = $request->precio;
                    $activar->save();
                    array_push($activated,$activar);
                    array_push($alreadyCreated,(int) $sucursalBD->id_sucursal);
                }else{
                    $desactivar = ProductosSucursal::where("id_sucursal",$sucursalBD->id_sucursal)
                        ->where("id_producto",$producto->id_producto)
                        ->first();
                    $desactivar->precio = $request->precio;
                    $desactivar->estado = 0;
                    $desactivar->save();
                    array_push($desactivated,$desactivar);
                }
            }
            //No existentes
            $creados = array();
            foreach ($sucursalesUpdate as $create) {
                if(!in_array($create,$alreadyCreated)){
                    $crear = [
                        "id_producto" => $producto->id_producto,
                        "id_sucursal" => $create
                    ];
                    //ProductosSucursal::create($crear);
                    $creado = ProductosSucursal::create([
                        "id_producto" => $producto->id_producto,
                        "id_sucursal" => $create,
                        "stock" => 0,
                        "precio" =>  $producto->precio,
                    ]);
                    array_push($creados,$creado);
                }
            }


            //recetas
            $alreadyCreated = array();
            $desactivated = array();
            $recetasBD = RecetasProducto::select("id_producto","id_receta","estado")->where("id_producto",$id)->get();
            $recetasIDs = $request->recetas;

            //Ya existentes

            foreach ($recetasBD as $recetaBD) {
                if(in_array( (int) $recetaBD->id_receta, $recetasIDs)){

                    $activar = RecetasProducto::where("id_receta",$recetaBD->id_receta)
                        ->where("id_producto",$producto->id_producto)
                        ->first();

                    $activar->estado = 1;
                    $activar->save();
                    array_push($alreadyCreated,(int) $recetaBD->id_receta);
                }else{
                    $desactivar = RecetasProducto::where("id_receta",$recetaBD->id_receta)
                        ->where("id_producto",$producto->id_producto)
                        ->first();

                    $desactivar->estado = 0;
                    $desactivar->save();

                    array_push($desactivated,$desactivar);
                }
            }
            //No existentes
            $recetasCreadas = array();
            foreach ($recetasIDs as $create) {
                if(!in_array($create,$alreadyCreated)){
                    $creado = RecetasProducto::create([
                        "id_producto" => $producto->id_producto,
                        "id_receta" => $create,
                        "estado" => 1,
                    ]);
                    array_push($recetasCreadas,$creado);
                }
            }

            return response()->json($desactivated,200);

        }catch(\Exception $e){
            //DB::rollback();
            return response()->json($e->getMessage(),500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $producto = Productos::findOrFail($id);
            $upd_data = array(
                'estado'  =>  0
            );
            $producto->update($upd_data);
            return response()->json($producto, 200);

        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getProductos(Request $request){
        $usuario_logeado = auth('sanctum')->user();
        $datos = Productos::where([
            ["estado", 1]
        ]);

        if(!empty($request->data)){
            $datos = $datos->where("nombre_producto", "LIKE", "%".$request->data."%")->orWhere("codigo_producto", "LIKE", "%".$request->data."%");
        }

        return $datos->paginate($request->perpage);
    }

    public function activeProductos(Request $request,$id){
        try{
            $producto = Productos::findOrFail($id);
            $upd_data = array(
                'activo'  =>  $request->activo
            );
            $producto->update($upd_data);
            return response()->json($producto, 200);

        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function activeProductosGroup(Request $request){
        try {
            if($request->selectAll){
                $datos = Productos::disable($request);
            }
            else{
                $datos = Productos::groupId($request->groupid)->disable($request);
            }
            return response()->json(['message' => 'Productos Deshabilitados'],200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function stock(Request $request){
        try {

            $usuario_logeado = auth('sanctum')->user();

            $datos = ProductosSucursal::with(['sucursal', 'producto'])
                    ->whereHas('producto',function (Builder $query) {
                        return $query->where('tipo_producto_combo',3);
                    })
                    ->where('id_sucursal',$usuario_logeado->id_sucursal)
                    ->paginate($request->perpage);
            return response()->json($datos,200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function updateStock(Request $request){
        DB::beginTransaction();
        try {
            $productoS = ProductosSucursal::findOrFail($request->id);
            $productoG = Productos::findOrFail($productoS->id_producto);
            $productoG->update(['stock' => ($productoG->stock - $productoS->stock) + $request->stock]);
            $productoS->update(['stock' => $request->stock]);

            DB::commit();

            return response()->json($productoS);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage(), 500);
        }
    }
}
