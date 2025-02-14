<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EgresosMotivos;

class ReasonExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $egresos = EgresosMotivos::active()->get();
            return response()->json($egresos, 200);
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
            $egresos = EgresosMotivos::create($request->all());
            return response()->json($egresos, 200);
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
            $egresos = EgresosMotivos::findOrFail($id);
            $egresos->update( $request->all());
            return response()->json($egresos, 200);
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
            $egresos = EgresosMotivos::findOrFail($id);
            $egresos->remove();
            return response()->json(['message' => 'The egresos has been deleted'], 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getEgresosMotivos(Request $request) {
        $usuario_logeado = auth('sanctum')->user();
        if(!empty($request->data)){
            $datos = EgresosMotivos::where("motivo", "LIKE", "%".$request->data."%")->where("estado", 1)->paginate($request->perpage);
        }else{
            $datos = EgresosMotivos::where("estado", 1)->paginate($request->perpage);
        }
        
        return $datos;
    }
}
