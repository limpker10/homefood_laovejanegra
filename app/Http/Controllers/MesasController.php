<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mesas;
use Illuminate\Database\Eloquent\Builder;

class MesasController extends Controller
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
            $mesas = Mesas::where('estado',1)
                    ->withCount(['ordenes'])
                    ->get();
            return response()->json($mesas, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
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
            //dd($request);
            $table = Mesas::create($request->all());
            return response()->json($table, 200);
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
            $mesa = Mesas::where('id_mesa',$id)->withCount(['ordenes'])->first();
            return response()->json($mesa, 200);
        }
        catch(\Exception $e){
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
        try{
            $table = Mesas::findOrFail($id);
            $table->update( $request->all());
            return response()->json($table, 200);
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
            $table = Mesas::findOrFail($id);
            $table->remove();
            return response()->json(['message' => 'The table has been deleted'], 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
    
    
    public function getMesas(Request $request) {

        $usuario_logeado = auth('sanctum')->user();
        $datos = Mesas::join('mst_zonas_pos','mst_zonas_pos.id_zona','=','mst_mesas.id_zona')
        ->select('mst_zonas_pos.id_sucursal as id_sucursal','mst_mesas.*')
        ->orderByRaw(
            "CASE WHEN mst_zonas_pos.id_sucursal = ".$usuario_logeado->id_sucursal." THEN 1 ELSE 2 END"
        )->where("mst_mesas.estado",1);
        if(!empty($request->data)){
            $datos = $datos->where("mst_mesas.nro_mesa", "LIKE", "%".$request->data."%");
        }
        $datos = $datos->paginate($request->perpage);

        /**
         * Ordenado (se muestra primero los datos de la sucursal actual) se puede hacer groupby primero. lo revisare despues
         */
        return $datos;
    }

    public function getTableByBranch($id){
        try{
            $mesas = Mesas::where('estado',1)
                    ->where('id_sucursal',$id)
                    ->withCount(['ordenes', 'ordenes_detalle'])
                    ->get();
            return response()->json($mesas, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}
