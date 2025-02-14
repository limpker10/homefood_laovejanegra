<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecetasDetalle;

class RecipeDetailController extends Controller
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
            $receta = RecetasDetalle::create($request->all());
            return response()->json($receta, 200);
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
            $receta = RecetasDetalle::where('id_receta',$id)->where("estado", 1)->get();
            return response()->json($receta, 200);
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
            $receta = RecetasDetalle::where("id_receta", $id)->where("id_insumo", $request->id_insumo);
            $receta->update( $request->all());
            return response()->json($receta, 200);
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
            $receta = RecetasDetalle::findOrFail($id);
            $upd_data = array(
                'estado'  =>  0
            );
            $receta->update($upd_data);
            return response()->json($receta, 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}
