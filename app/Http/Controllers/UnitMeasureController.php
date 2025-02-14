<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnidadesMedida;

class UnitMeasureController extends Controller
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
            $unit = UnidadesMedida::active()->get();
            return response()->json($unit, 200);
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
            $unit = UnidadesMedida::create($request->all());
            return response()->json($unit, 200);
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
            $unit = UnidadesMedida::findOrFail($id);
            $unit->update( $request->all());
            return response()->json($unit, 200);
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
            $Unit = UnidadesMedida::findOrFail($id);
            $Unit->remove();
            return response()->json(['message' => 'The Unit has been deleted'], 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getUnidadesMedida(Request $request) {
        $usuario_logeado = auth('sanctum')->user();
        if(!empty($request->data)){
            $datos = UnidadesMedida::where("estado", 1)->where("unidad_medida", "LIKE", "%".$request->data."%")->orWhere("simbolo", "LIKE", "%".$request->data."%")->paginate($request->perpage);
        }else{
            $datos = UnidadesMedida::where("estado", 1)->paginate($request->perpage);
        }
        
        return $datos;
    }
}
