<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sucursales;

class SucursalesController extends Controller
{
    public function __constructor(){
        $this->middleware('auth:sanctum');
    }

    public function index(){
        try{
            $sucursal = Sucursales::active()->get();
            return response()->json($sucursal, 200);
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
            $sucursal = Sucursales::create($request->all());
            return response()->json($sucursal, 200);

        }catch(Exception $e){
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
            //obtiene la sucursal con sus zonas
            $sucursal = Sucursales::with('zonas')->findOrFail($id);
            return response()->json($sucursal, 200);
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
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
            $sucursal = Sucursales::findOrFail($id);
            $sucursal->update($request->all());
            return response()->json($sucursal, 200);

        }catch(Exception $e){
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
            $sucursal = Sucursales::findOrFail($id);
            $upd_data = array(
                'estado'  =>  0
            );
            $sucursal->update($upd_data);
            return response()->json($sucursal, 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    //BUG DE FILTROS
    public function getSucursales(Request $request){
        $usuario_logeado = auth('sanctum')->user();
        $datos = Sucursales::where([
            ["estado", 1],
        ]);
        
        if(!empty($request->data)){
            $datos = $datos->where("nombre_sucursal", "LIKE", "%".$request->data."%")->where("estado", 1);
        }

        return $datos->paginate($request->perpage);
    }
}
