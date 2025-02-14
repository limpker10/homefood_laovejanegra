<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Egresos;
use App\Models\Caja;

class ExpenseController extends Controller
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
            $usuario_logeado = auth('sanctum')->user();
            //normalize request
            $caja = Caja::where("id_sucursal",$usuario_logeado->id_sucursal)
                    ->whereNull("fecha_cierre")
                    ->where('rubro',$request->rubro)
                    ->first();
            if(empty($caja)){
                return response()->json([
                    'status' => false //No se ha abierto aun una caja.
                ], 200);
            }else{
                /*$egreso = Egresos::create([
                    'fecha_egreso'  => date('Y-m-d H:i:s'),
                    'monto'         => $request->monto,
                    'detalle'       => $request->detalle,
                    'id_usuario'    => $usuario_logeado->id,
                    'id_sucursal'   => $usuario_logeado->id_sucursal,
                    'id_caja'    => $caja->id_caja,
                    'id_motivo_egreso'  => $request->id_motivo_egreso,
                    'rubro' => $request->rubro,
                ]);*/
                $egreso = Egresos::updateOrCreate([
                    'fecha_egreso'  => date('Y-m-d H:i:s'),
                    'monto'         => $request->monto,
                    'detalle'       => $request->detalle,
                    'id_usuario'    => $usuario_logeado->id,
                    'id_sucursal'   => $usuario_logeado->id_sucursal,
                    'id_motivo_egreso'  => $request->id_motivo_egreso,
                    'rubro' => $request->rubro],
                    ['caja_chica'  => $request->caja_chica ? 1 : 0,
                    'id_caja'    => $request->caja_chica ? $caja->id_caja : 0
                ]);

                return response()->json([
                    'status' => true,
                    'egresos' => $egreso,
                    'caja' => $caja
                ], 200);
            }

            //return response()->json($egreso, 200);
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
            $deleted = Egresos::findOrFail($id);
            $deleted->estado = 0;
            $deleted->save();
            return response()->json($deleted,200);
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getEgresosAll(Request $request){
        try{
            $from = date($request->data["fecha_inicio"]);
            $to = date($request->data["fecha_fin"]);
            $rubro = $request["rubro"];

            $usuario_logeado = auth('sanctum')->user();
            if(!empty($request->data["fecha_inicio"]) && !empty($request->data["fecha_fin"])){
                $datos = Egresos::whereBetween('fecha_egreso', [$from." 00:00:00", $to." 23:59:59"])
                    ->where("id_sucursal",$usuario_logeado->id_sucursal)
                    ->orderBy('fecha_egreso','desc');
            }else{
                $datos = Egresos::where("id_sucursal",$usuario_logeado->id_sucursal)
                    ->orderBy('fecha_egreso','desc');
            }
            $datos->where('rubro', $rubro)
                ->where('estado','=',1);

            return $datos->paginate($request->perpage);
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }

    }
}
