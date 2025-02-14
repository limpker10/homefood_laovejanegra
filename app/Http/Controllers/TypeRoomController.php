<?php

namespace App\Http\Controllers;

use App\Models\TipoHabitacion;
use Illuminate\Http\Request;

class TypeRoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCategorias(Request $request){
        try {
            $rows = $request->perpage;
            return TipoHabitacion::where('estado',1)->paginate($rows);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function index()
    {
        try {
            return TipoHabitacion::all();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            return TipoHabitacion::create($request->all());
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TipoHabitacion  $tipoHabitacion
     * @return \Illuminate\Http\Response
     */
    public function show(TipoHabitacion $tipoHabitacion)
    {
        try{
            return $tipoHabitacion;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TipoHabitacion  $tipoHabitacion
     * @return \Illuminate\Http\Response
     */
    public function edit(TipoHabitacion $tipoHabitacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TipoHabitacion  $tipoHabitacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $tipohabitacion =  TipoHabitacion::findOrFail($id);
            $tipohabitacion->update($request->all());
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TipoHabitacion  $tipoHabitacion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $tipohabitacion =  TipoHabitacion::findOrFail($id);
            $upd_data = array(
                'estado'  =>  0
            );
            $tipohabitacion->update($upd_data);
            //$tipoHabitacion->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
