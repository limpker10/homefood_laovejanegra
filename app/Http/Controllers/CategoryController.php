<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categorias;
use App\Models\Sucursales;
use App\Models\CategoriaSucursal;

class CategoryController extends Controller
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
        try{
            $category = Categorias::active()->get();
            return response()->json($category, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
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
            $category = Categorias::create($request->item);
            foreach($request->sucursales as $sucursal){
                $categoria_sucursal = [
                    "id_categoria" => $category->id_categoria,
                    "id_sucursal" => $sucursal,
                    "color" => $category->color,
                    "tipo_producto_insumo" => $category->tipo_producto_insumo
                ];
                CategoriaSucursal::create($categoria_sucursal);
            }
            return response()->json($category, 200);
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
        try{
            $category = Categorias::findOrFail($id);
            $category->update( $request->item);
            //Array de sucursales de la categoria antes de actualizar
            $sucursales_categoria = CategoriaSucursal::select("id_sucursal","estado")->where("id_categoria",$id)->get();
            //Array de sucursales seleccionados en la actualizacion
            $sucursales_update = $request->sucursales;
            foreach($sucursales_categoria as $sucursal_cat){
                $contador_eliminado = 0; //contador para encontrar la sucursal eliminada
                foreach($sucursales_update as $sucursal_updt){
                    if($sucursal_updt == $sucursal_cat->id_sucursal){
                        $contador_eliminado++;
                        if($sucursal_cat->estado == 0){ //si solo ha sido desactivado
                            $sucursal_categoria = CategoriaSucursal::where('id_sucursal',$sucursal_cat->id_sucursal)
                                                    ->where('id_categoria',$category->id_categoria)
                                                    ->first();
                            $sucursal_categoria->estado = 1;
                            $sucursal_categoria->update();
                        }
                    }
                }
                if($contador_eliminado == 0){ //no hay coincidencias, se ha retirado de la seleccion
                    $sucursal_categoria = CategoriaSucursal::where('id_sucursal',$sucursal_cat->id_sucursal)
                                            ->where('id_categoria',$category->id_categoria)
                                            ->first();
                    $sucursal_categoria->estado = 0;
                    $sucursal_categoria->update();
                }
            }
            foreach($sucursales_update as $sucursal_updt){
                $contador_nuevo = 0; //contador para encontrar la sucursal nueva
                foreach($sucursales_categoria as $sucursal_cat){
                    if($sucursal_updt == $sucursal_cat->id_sucursal){
                        $contador_nuevo++;
                    }
                }
                if($contador_nuevo == 0){ //no hay coincidencias, se ha retirado de la seleccion
                    $categoria_sucursal = [
                        "id_categoria" => $category->id_categoria,
                        "id_sucursal" => $sucursal_updt
                    ];
                    CategoriaSucursal::create($categoria_sucursal);
                }
            }
            return response()->json($category, 200);
        }
        catch(\Exception $e){
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
        try {
            $category = Categorias::findOrFail($id);
            $category->remove();
            //array de sucursales de la categoria actual
            $sucursales_categoria = CategoriaSucursal::select("id_categoria_sucursal")->where("id_categoria",$id)->pluck('id_categoria_sucursal')->toArray();
            //eliminar las sucursales asociadas
            foreach($sucursales_categoria as $id_sucursal_cat){
                $sucursal_categoria = CategoriaSucursal::findOrFail($id_sucursal_cat);
                $sucursal_categoria->estado = 0;
                $sucursal_categoria->update();
                
            }
            return response()->json(['message' => 'The category has been deleted'], 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getCategorias(Request $request) {
        $usuario_logeado = auth('sanctum')->user();
        if(!empty($request->data)){
            $datos = Categorias::where("estado", 1)->where("nombre", "LIKE", "%".$request->data."%")->orWhere("color", "LIKE", "%".$request->data."%")->paginate($request->perpage);
        }else{
            $datos = Categorias::where("estado", 1)->paginate($request->perpage);
        }
        
        return $datos;
    }

    public function getSucursalesPorCategoria($id) {
        $datos = CategoriaSucursal::select("id_sucursal")->active()->where("id_categoria",$id)->pluck('id_sucursal')->toArray();
        
        return $datos;
    }
}
