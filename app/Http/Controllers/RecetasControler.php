<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recetas;

class RecetasControler extends Controller
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
            $receta = Recetas::active()->get();
            return response()->json($receta, 200);
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
            $receta = Recetas::create($request->all());
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
            $receta = Recetas::findOrFail($id);
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
            $receta = Recetas::findOrFail($id);
            $receta->update( $request->all());
            return response()->json($receta, 200);
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
            $receta = Recetas::findOrFail($id);
            $receta->remove();
            return response()->json(['message' => 'The receta has been deleted'], 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getRecetas(Request $request) {
        $usuario_logeado = auth('sanctum')->user();
        if(!empty($request->data)){
            $datos = Recetas::where("estado", 1)->where("nombre_receta", "LIKE", "%".$request->data."%")->paginate($request->perpage);
        }else{
            $datos = Recetas::where("estado", 1)->paginate($request->perpage);
        }
        
        return $datos;
    }
}
