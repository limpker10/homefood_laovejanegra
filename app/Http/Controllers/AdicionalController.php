<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Adicionales;
use App\Models\AdicionalesDetalle;
use App\Models\ComboProducto;

class AdicionalController extends Controller
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
        try{
            $adicional = Adicionales::create($request->all());
            foreach ($request->productos_adicional as $value) {
                AdicionalesDetalle::create([
                    "id_adicional" => $adicional['id_adicional'],
                    "id_producto" => $value['id_producto'],
                    "cantidad" => $value['cantidad'],
                    "requerido" => $value['requerido'],
                    "precio_adicional" => ((float)$value['precio_adicional']) > 0.00 ? 1 : 0,
                    "precio" => $value['precio'],
                ]);
            }
            $combo = ComboProducto::create([
                "id_combo" => $request->id_combo,
                "id_adicional" => $adicional->id_adicional,
                "cantidad" => 1,
            ]);
            return response()->json($combo, 200);
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
            $combo = Adicionales::findOrFail($id);
            return response()->json($combo, 200);
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
        //
    }
}
