<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MesaOrden;
use App\Models\Orden;
use App\Models\OrdenDetalle;

class MesasOrdenController extends Controller
{
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
        //
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
            $ordenesMesa = MesaOrden::where('id_mesa',$id)->get();
            return response()->json($ordenesMesa, 200);
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
        //
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
            $orden_mesa = MesaOrden::where('id_orden', $id)->first();

            $detalle = OrdenDetalle::where('id_orden',$orden_mesa->id_orden)->get();
            foreach ($detalle as $key) {
                OrdenDetalle::findOrFail($key->id_detalle_orden)->delete();
            }
            
            $orden_mesa->delete();
            $orden = Orden::findOrFail($orden_mesa->id_orden);

            $orden->delete();

            return response()->json(['message' => 'The orden has been deleted'], 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function updateStatus($id){
        try{
            $orden_mesa = Orden::findOrFail($id);
            $orden_mesa->status = 1;
            $orden_mesa->save();
            return response()->json(['message' => 'The orden has been update'], 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}
