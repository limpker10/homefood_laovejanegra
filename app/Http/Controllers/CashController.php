<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\CajaDetalle;
use App\Models\Comprobante;
use App\Models\MedioPago;
use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Models\MesaOrden;
use App\Models\Egresos;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use Carbon\Carbon;

class CashController extends Controller
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
        try {
            $usuario_logeado = auth('sanctum')->user();
            $this->validate($request, [
                'monto_apertura' => 'required',
            ]);
            $create_time = Carbon::now();
            $caja = Caja::create([
                'fecha_apertura'  => $create_time->toDateTimeString(),//date('Y-m-d H:i:s'),
                'monto_apertura' => (float)$request->monto_apertura,
                'id_usuario' => $usuario_logeado->id,
                'id_sucursal' => $request->id_sucursal,
                'rubro' => $request->rubro
            ]);
            return response()->json($caja, 200);
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
            $caja = Caja::findOrFail($id);
            
            $total_comprobantes = DB::select('SELECT SUM(prc_comprobante.total) as total FROM prc_comprobante WHERE prc_comprobante.id_caja='.$id.' AND prc_comprobante.id_estado_comprobante = 1');
            $total_comprobantes = $total_comprobantes[0]->total;
            $total_egreso = DB::select('SELECT ROUND(IFNULL(SUM(prc_egresos.monto), 0),2) as egresos FROM prc_egresos WHERE prc_egresos.id_caja='.$id.' AND prc_egresos.estado=1');
            $total_egreso = $total_egreso[0]->egresos;
            $total = (($caja->monto_apertura + $total_comprobantes) - $total_egreso);

            //$caja->update($request->all());
            $caja->fecha_cierre = date('Y-m-d H:i:s');
            $caja->monto_cierre = $total;
            $caja->save();

            foreach ($request->detalle as $value) {
                $value['id_caja']=$id;
                CajaDetalle::create(collect($value)->all());
            }

            //Eliminamos ordenes pendientes
            $ordenes = Orden::get();
            foreach($ordenes as $ord){
                MesaOrden::where("id_orden", $ord->id_orden)->delete();
                $detalle = OrdenDetalle::where('id_orden', $ord->id_orden)->get();
                foreach ($detalle as $key) {
                    OrdenDetalle::findOrFail($key->id_detalle_orden)->delete();
                }
                $orden = Orden::findOrFail($ord->id_orden);
                $orden->delete();
            }

            return response()->json($caja, 200);

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
        //
    }

    public function vista($id_caja, $id_sucursal){
        $document = DB::select('CALL prd_close_cash(?, ?)',array($id_caja, $id_sucursal));
        $detail = Comprobante::where('id_caja',$id_caja)->where('id_estado_comprobante',1)->get();
        $egresos = Egresos::where('id_caja',$id_caja)->where('estado',1)->get();
        $caja = Caja::findOrFail($id_caja);
        $total_comprobantes = DB::select('SELECT SUM(prc_comprobante.total) as total 
        FROM prc_comprobante WHERE prc_comprobante.id_caja='.$id_caja.'
        AND prc_comprobante.id_estado_comprobante IN (1,4,5)');
        $total_comprobantes = $total_comprobantes[0]->total;
        $total_egreso = DB::select('SELECT ROUND(IFNULL(SUM(prc_egresos.monto), 0),2) as egresos 
                        FROM prc_egresos WHERE prc_egresos.id_caja='.$id_caja.'
                        AND prc_egresos.estado=1');
        $total_egreso = $total_egreso[0]->egresos;
        $total = (($caja->monto_apertura + $total_comprobantes) - $total_egreso);


        $total_comprobantes_efectivo = DB::select('SELECT SUM(prc_comprobante.total) as total 
        FROM prc_comprobante WHERE prc_comprobante.id_caja='.$id_caja.'
        AND prc_comprobante.id_estado_comprobante IN (1,4,5) AND prc_comprobante.id_medio_pago=1');
        $total_comprobantes_efectivo = $total_comprobantes_efectivo[0]->total;
        $total_efectivo = (($caja->monto_apertura + $total_comprobantes_efectivo) - $total_egreso);

        $productos =  DB::select('SELECT prc_comprobante_detalle.codigo_producto,prc_comprobante_detalle.nombre_producto, SUM(prc_comprobante_detalle.precio_total) AS precio_total, SUM(prc_comprobante_detalle.cantidad) AS cantidad
        FROM prc_comprobante_detalle
        INNER JOIN prc_comprobante ON  prc_comprobante_detalle.id_comprobante = prc_comprobante.id_comprobante
        INNER JOIN mst_producto ON prc_comprobante_detalle.id_producto = mst_producto.id_producto
        WHERE prc_comprobante.id_caja = '.$id_caja.' AND mst_producto.tipo_producto_combo = 3  AND prc_comprobante.id_estado_comprobante = 1
        GROUP BY prc_comprobante_detalle.codigo_producto');


        return view('ticket_cierre', compact('document','detail','egresos','caja','total','productos','total_efectivo'));
    }

    public function generarPDF($id){
        $caja = Caja::findOrFail($id);
        $html = $this->vista($id, $caja->id_sucursal)->render();
        $file = 'pdf_'.'pdf';
        $mpdf = new Mpdf(
            ['mode' => 'utf-8',
            'format' => [80, 360],
            'margin_top' => 2,
            'margin_right' => 5,
            'margin_bottom' => 0,
            'margin_left' => 5 ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output($file,'I');
    }


    public function getCajasAll(Request $request) {
        $from = date($request->data["fecha_inicio"]);
        $to = date($request->data["fecha_fin"]);
        $rubro = $request["rubro"];
        $usuario_logeado = auth('sanctum')->user();

        if(!empty($request->data["fecha_inicio"]) && !empty($request->data["fecha_fin"])){
            $datos = Caja::whereBetween('fecha_apertura', [$from, $to])->where("id_sucursal",$usuario_logeado->id_sucursal);
        }else{
            $datos = Caja::where("id_sucursal",$usuario_logeado->id_sucursal);
        }
        $datos->where('rubro', $rubro);
        return $datos->orderBy('fecha_apertura','desc')->paginate($request->perpage);
    }

    
    public function getDetalleCaja($id){
        try {
            $detail = CajaDetalle::where('id_caja', $id)->get();
            return $detail;
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }  
    }


    public function getMontosDelDia($id, $rubro, $flag){
        
        try{
            if($flag == 1){
                $caja = Caja::where("id_sucursal",$id)
                ->whereNull("fecha_cierre")
                ->where("rubro", $rubro)
                ->first();
            }else{
                $caja = Caja::where("id_caja",$id)
                ->whereNull("fecha_cierre")
                ->where("rubro", $rubro)
                ->first();
            }
            $egresos = DB::select('SELECT ROUND(IFNULL(SUM(prc_egresos.monto), 0),2) AS total_egreso
                    FROM prc_egresos 
                    WHERE prc_egresos.id_caja = '.$caja->id_caja.'
                    AND prc_egresos.estado=1');
            $data = DB::select('SELECT mst_medio_pago.id_medio_pago,
                    mst_medio_pago.medio_pago, 
                    ROUND(IFNULL(SUM(comprobantes.total), 0),2) AS monto
                    FROM mst_medio_pago
                    LEFT JOIN (
                        SELECT * 
                        FROM prc_comprobante 
                        WHERE prc_comprobante.id_caja = '.$caja->id_caja.'
                        AND prc_comprobante.id_estado_comprobante IN (1,4,5)
                    )comprobantes ON mst_medio_pago.id_medio_pago = comprobantes.id_medio_pago
                    GROUP BY mst_medio_pago.id_medio_pago');

            $caja_cierre =[ 'id_caja' => $caja->id_caja, 
                            'fecha_apertura' => $caja->fecha_apertura, 
                            'fecha_cierre' => $caja->fecha_cierre, 
                            'monto_apertura' => $caja->monto_apertura, 
                            'id_usuario' => $caja->id_usuario,
                            'id_sucursal' => $caja->id_sucursal,
                            'usuario' => $caja->usuario,
                            'detalle' => $data,
                            'egresos' =>  $egresos[0]->total_egreso
                        ];
            return response()->json($caja_cierre, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        } 
        
    }

    
}
