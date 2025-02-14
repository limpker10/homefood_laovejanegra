<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Models\MesaOrden;
use App\Models\Mesas;
use Mpdf\Mpdf;
use App\Events\NotificactionsAdminEvent;

class OrdenController extends Controller
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
            /*$usuario_logeado = auth('sanctum')->user();
            $request->id_sucursal = $usuario_logeado->id_sucursal;*/
            $request = Orden::normalizeRequest($request);
            $orden = Orden::create($request->all());

            if(!empty($request->id_mesa)){
                $orden_mesa = MesaOrden::create([
                    'id_orden' => $orden->id_orden,
                    'id_mesa' => $request->id_mesa
                ]);
                //event(new NotificactionsAdminEvent($orden_mesa->getData()));
                return response()->json($orden_mesa->getData(), 200);
            }
            else{
                return response()->json($orden->getData(), 200);
            }
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
            $orden = Orden::findOrFail($id);
            return response()->json($orden, 200);
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
            $orden = Orden::findOrFail($id);
            $orden->update($request);
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
            $detalle = OrdenDetalle::where('id_orden',$id)->get();
            foreach ($detalle as $key) {
                OrdenDetalle::findOrFail($key->id_detalle_orden)->delete();
            }
            $orden = Orden::findOrFail($id);
            $orden->delete();
            return response()->json(['message' => 'The orden has been deleted'], 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
    

    public function updateField(Request $request, $id){
        try{
            $orden = Orden::findOrFail($id);
            $orden->updateField($request->field,$request->value);
            return response()->json($orden->getData(), 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }   
    }

    public function getOrderByBranch($id){
        try {

            /*$crashedCarIds = CrashedCar::pluck('car_id')->all();
            $cars = Car::whereNotIn('id', $crashedCarIds)->select(...)->get();*/

            $ordenes_mesas = MesaOrden::pluck('id_orden')->all();
            $ordenesMesa = Orden::whereNotIn('id_orden', $ordenes_mesas)->where('id_sucursal',$id)->get();
            return response()->json($ordenesMesa, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(),500);
        }
    }


    public function vista($id){
        $document = Orden::findOrFail($id);
        $detail = OrdenDetalle::where('id_orden',$id)->get();
        $mesa = MesaOrden::where('id_orden',$id)->first();
        if(empty($mesa)){
            $mesa = "Para llevar";
            $zona = "";
        }
        else{
            $zona = $mesa->mesa->zona["nombre"];
            $mesa = $mesa->mesa->nro_mesa;
        }
        return view('ticket_cuenta', compact('document','detail','mesa','zona'));
    }
    public function generarPDF($id){
        $html = $this->vista($id)->render();
        $file = 'pdf_'.'pdf';
        $mpdf = new Mpdf(
            ['mode' => 'utf-8',
            'format' => [80, 180],
            'margin_top' => 2,
            'margin_right' => 5,
            'margin_bottom' => 0,
            'margin_left' => 5 ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output($file,'I');
    }


}
