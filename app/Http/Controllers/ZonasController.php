<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zonas;
use App\Models\Sucursales;

class ZonasController extends Controller
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
            $usuario_logeado = auth('sanctum')->user();
            $zones = Zonas::active()->orderByRaw(
                "CASE WHEN id_sucursal = ".$usuario_logeado->id_sucursal." THEN 1 ELSE 2 END"
            )->get();
            return response()->json($zones, 200);        }
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
            $zone = Zonas::create($request->all());
            return response()->json($zone, 200);
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
            $zone = Zonas::findOrFail($id);
            $zone->update( $request->all());
            return response()->json($zone, 200);
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
            $zone = Zonas::findOrFail($id);
            $zone->remove();
            return response()->json(['message' => 'The zone has been deleted'], 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
    
    public function getZonas(Request $request) {
        $usuario_logeado = auth('sanctum')->user();
        
        if(!empty($request->data)){
            $datos = Zonas::where("estado", 1)->where("nombre", "LIKE", "%".$request->data."%");
        }else{
            $datos = Zonas::where("estado", 1);
        }
        $datos = $datos->orderByRaw(
            "CASE WHEN id_sucursal = ".$usuario_logeado->id_sucursal." THEN 1 ELSE 2 END"
        )->paginate($request->perpage);
        return $datos;
    }
}
