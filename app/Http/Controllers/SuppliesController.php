<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Insumos;
use App\Models\InsumosSucursal;
use Illuminate\Support\Facades\DB;

class SuppliesController extends Controller
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
            $insumo = Insumos::create($request->all());
            foreach ($request->sucursales as $id_sucursal) {
                InsumosSucursal::create([
                    "id_insumo" => $insumo->id_insumo,
                    "id_sucursal" => $id_sucursal,
                    "stock" => $request->stock
                ]);
            }
            //$insumoSucursal = InsumosSucursal::create

            return response()->json($insumo, 200);
            //return response()->json($request->all());



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
        try{
            $insumo = Insumos::findOrFail($id);
            $insumo->update($request->all());
            

            //Array de sucursales de la categoria antes de actualizar
            $sucursales_categoria = InsumosSucursal::select("id_sucursal","estado")->where("id_insumo",$id)->get();
            //Array de sucursales seleccionados en la actualizacion
            $sucursales_update = $request->sucursales;
            foreach($sucursales_categoria as $sucursal_cat){
                $contador_eliminado = 0; //contador para encontrar la sucursal eliminada
                foreach($sucursales_update as $sucursal_updt){
                    if($sucursal_updt == $sucursal_cat->id_sucursal){
                        $contador_eliminado++;
                        if($sucursal_cat->estado == 0){ //si solo ha sido desactivado
                            $sucursal_categoria = InsumosSucursal::where('id_sucursal',$sucursal_cat->id_sucursal)
                                                    ->where('id_insumo',$insumo->id_insumo)
                                                    ->first();
                            $sucursal_categoria->estado = 1;
                            $sucursal_categoria->update();
                        }
                    }
                }
                if($contador_eliminado == 0){ //no hay coincidencias, se ha retirado de la seleccion
                    $sucursal_categoria = InsumosSucursal::where('id_sucursal',$sucursal_cat->id_sucursal)
                                            ->where('id_insumo',$insumo->id_insumo)
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
                        "id_insumo" => $insumo->id_insumo,
                        "id_sucursal" => $sucursal_updt
                    ];
                    InsumosSucursal::create($categoria_sucursal);
                }
            }


            return response()->json($insumo, 200);

        }catch(\Exception $e){
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
            $insumo = Insumos::findOrFail($id);
            $insumoSucursal = InsumosSucursal::where('id_insumo',$id)->first();
            $upd_data = array(
                'estado'  =>  0
            );
            $insumoSucursal->update(['estado'  =>  0]);
            $insumo->update($upd_data);
            return response()->json($insumoSucursal, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getInsumos(Request $request){
        $datos = Insumos::where([
            ["estado", 1]
        ]);
        
        if(!empty($request->data)){
            $datos = $datos->where("nombre_insumo", "LIKE", "%".$request->data."%")->orWhere("codigo_insumo", "LIKE", "%".$request->data."%");
        }

        return $datos->paginate($request->perpage);
    }
    
    public function stock(Request $request){
        try {
            $usuario_logeado = auth('sanctum')->user();
            $datos = InsumosSucursal::with(['sucursal', 'insumo'])
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
            $insumoS = InsumosSucursal::findOrFail($request->id);
            $insumoG = Insumos::findOrFail($insumoS->id_insumo);
            $insumoG->update(['stock' => ($insumoG->stock - $insumoS->stock) + $request->stock]);
            $insumoS->update(['stock' => $request->stock]);

            DB::commit();

            return response()->json($insumoS);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage(), 500);
        }
    }
    
    public function branch($id){
        $datos = InsumosSucursal::select("id_sucursal")
                                ->active()
                                ->where("id_insumo",$id)
                                ->pluck('id_sucursal')
                                ->toArray();
        
        return $datos;
    }

}
