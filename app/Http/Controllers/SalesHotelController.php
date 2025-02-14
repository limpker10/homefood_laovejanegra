<?php

namespace App\Http\Controllers;
use App\Models\Comprobante;
use App\Models\ComprobanteDetalle;
use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Models\MesaOrden;
use App\Models\Productos;

use App\Models\Reservas;
use App\Models\ReservaDetalle;
use App\Models\ReservaHabitaciones;
use App\Models\Habitaciones;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Mpdf\Mpdf;
class SalesHotelController extends Controller
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
        
        DB::beginTransaction();
        try{
            
            $usuario_logeado = auth('sanctum')->user();
            $request_header = Comprobante::normalizeRequest($request->header,1);

            $comprobantes = Comprobante::create(collect($request_header)->all());

            foreach ($request->detail as $value) {
                $producto = Productos::find($value["id_producto"]);
                $value = ComprobanteDetalle::normalizeRequest($value,$comprobantes->id_comprobante);
                $value['codigo_producto']=$producto["codigo_producto"];
                ComprobanteDetalle::create(collect($value)->all());
                /*$product = Productos::find($value["id_producto"]);
                if($product->tipo_producto_combo == 3){
                    $product->decrement('stock', $value["cantidad"]);
                }*/
                
            }
            $value = ComprobanteDetalle::normalizeRequest($request->alojamiento,$comprobantes->id_comprobante);
            ComprobanteDetalle::create(collect($value)->all());

            if($comprobantes->id_tipo_comprobante==3){

                $comprobante = Comprobante::find($comprobantes->id_comprobante);
                $comprobante->id_estado_comprobante  = 1;
                $comprobante->save();

                DB::commit();

                $pdf_document = '/inovicePDF/'.$comprobantes->id_comprobante.'&embedded=true';
                return response()->json(['success'=> true,'pdf'=>$pdf_document,'pdf_sunat'=>$pdf_document, 'message' => 'Comprobante creado y validado.', 'id_comprobante' => $comprobantes->id_comprobante, 'print'=>$usuario_logeado->sucursal['impresion_automatica']]);
            }
            else{
                $comprobante         = Comprobante::find($comprobantes->id_comprobante);
                $comprobante_detalle = ComprobanteDetalle::where("id_comprobante", $comprobantes->id_comprobante)->get();
                $items = array();

                //Array Api - Boleta Gravada Items[]
                foreach($comprobante_detalle as $cmp_d){
                    $codigo = explode('-',$cmp_d["codigo_producto"]);
                    if("ALJ00"==$codigo[0]){
                        $product_i = [
                            'codigo_producto' => $codigo[0],
                            'id_producto' => $codigo[1],
                            'nombre_producto'=>$cmp_d["nombre_producto"],
                            'unidad_medida' => [
                                'codigo_sunat' => 'ZZ'
                            ]
                        ];
                    }
                    else{
                        $product_i = Productos::find($cmp_d->id_producto );
                    }
                    $valor_unitario = $cmp_d->precio_unitario / 1.10;
                    $total_base_igv = $cmp_d->precio_total / 1.10;//subtotal
                    $total_igv      = $cmp_d->precio_total - $total_base_igv;
                    
                    $items[] = array(
                        "codigo_interno"             => $product_i["codigo_producto"]."-".$product_i["id_producto"],
                        "descripcion"                => $product_i["nombre_producto"],
                        "codigo_producto_sunat"      => "",
                        "codigo_producto_gsl"        => "",
                        "unidad_de_medida"           => $product_i["unidad_medida"]["codigo_sunat"],
                        "cantidad"                   => $cmp_d->cantidad,
                        "valor_unitario"             => number_format($valor_unitario, 2, '.', ''),
                        "codigo_tipo_precio"         => "01",
                        "precio_unitario"            => number_format($cmp_d->precio_unitario, 2, '.', ''),
                        "codigo_tipo_afectacion_igv" => "10",
                        "total_base_igv"             => number_format($total_base_igv, 2, '.', ''),
                        "porcentaje_igv"             => 10,
                        "total_igv"                  => number_format($total_igv, 2, '.', ''),
                        "total_impuestos"            => number_format($total_igv, 2, '.', ''),
                        "total_valor_item"           => number_format($total_base_igv, 2, '.', ''),
                        "total_item"                 => $cmp_d->precio_total,
                    );
                }

                //Array Api - Boleta Gravada
                
                $codigo_tipo_documento           = $comprobante->id_tipo_comprobante != 1? "01" : "03"; //Factura : Boleta
                //$codigo_tipo_documento_identidad = $comprobante->id_tipo_comprobante != 1? "1" : "6"; //DNI : RUC

                $codigo_tdi = "1";
                $stlen = strlen($comprobante->nro_documento);
                switch ($stlen) {
                    case 8:
                        $codigo_tdi = "1";
                        break;
                    case 11:
                        $codigo_tdi = "6";
                        break;
                    case 9:
                        $codigo_tdi = "4";
                        break;
                    case 12:
                        $codigo_tdi = "7";
                        break;
                    default:
                        $codigo_tdi = "0";
                        break;
                }
                $codigo_tipo_documento_identidad = $codigo_tdi;//strlen($comprobante->nro_documento) == 8 ? "1" : "6"; //DNI : RUC
                
                $hora_de_emision = date("h:i:s");
                
                /** TEMPORAL FIX */

                $total = $comprobante->total;
                $subtotal = number_format($total/1.10, 2, '.', '');
                $igv = number_format($total-$subtotal, 2, '.', '');

                $comp_arr = array(
                    "serie_documento"        => $comprobante->serie->serie,
                    "numero_documento"       => '#',//$comprobante->correlativo,
                    "fecha_de_emision"       => $comprobante->fecha_emision,
                    "hora_de_emision"        => $hora_de_emision, //extraer hora de emision de request
                    "codigo_tipo_operacion"  => "0101", //Operacion Sunat
                    "codigo_tipo_documento"  => $codigo_tipo_documento,
                    "codigo_tipo_moneda"     => "PEN",
                    "fecha_de_vencimiento"   => $comprobante->fecha_emision, //la misma fecha de emision
                    "numero_orden_de_compra" => "", //Opcional
                    "formato_pdf"            => $comprobante->formato_impresion,
                    "datos_del_emisor" => array(
                        "codigo_pais"                      => "PE",
                        "ubigeo"                           => "040101",
                        "direccion"                        => $comprobante->sucursal['direccion_fiscal'],
                        "correo_electronico"               => $comprobante->sucursal['email'], 
                        "telefono"                         => $comprobante->sucursal['telefono'], 
                        "codigo_del_domicilio_fiscal"      => $comprobante->sucursal['cod_domicilio_fiscal'], 
                    ),
                    "documento_afectado" => array(
                        "external_id"    => $comprobante->external_id,
                    ),
                    "datos_del_cliente_o_receptor" => array(
                        "codigo_tipo_documento_identidad"    => $codigo_tipo_documento_identidad,
                        "numero_documento"                   => $comprobante->nro_documento,
                        "apellidos_y_nombres_o_razon_social" => $comprobante->nombre_cliente,
                        "codigo_pais"                        => "PE",
                        "ubigeo"                             => "",
                        "direccion"                          => $comprobante->direccion_cliente,
                        "correo_electronico"                 => "", //Incluir correo
                        "telefono"                           => ""  //Incluir telefono
                    ),
                    "totales" => array(
                        "total_exportacion"            => 0.00,
                        "total_operaciones_gravadas"   => number_format($subtotal, 2, '.', ''),
                        "total_operaciones_inafectas"  => 0.00,
                        "total_operaciones_exoneradas" => 0.00,
                        "total_operaciones_gratuita"   => 0.00,
                        "total_igv"                    => number_format($igv,2, '.', ''),
                        "total_impuestos"              => number_format($igv,2, '.', ''), //Total de Impuestos?
                        "total_valor"                  => number_format($subtotal, 2, '.', ''),
                        "total_venta"                  => $comprobante->total,
                    ),
                    "items" => $items,
                    
                    "informacion_adicional" => 'Forma de Pago: '.$comprobante->medio_pago["medio_pago"] .' '. $request->header["informacion_adicional"],
                    //"acciones" => array("formato_pdf" => "ticket")
                    "acciones" => array("enviar_xml_firmado"=> false, "enviar_email" => false, "formato_pdf" => "ticket"),
                );

                if($comprobante->total_dscto>0){
                    $base = number_format(($comprobante->total + $comprobante->total_dscto) / 1.10, 2, '.', '');
                    $dscto_m = number_format($comprobante->total_dscto / 1.10, 2, '.', '');
                    $factor = number_format($dscto_m/$base, 5, '.', '');

                    $comp_arr["totales"]["total_descuentos"] = $comprobante->total_dscto;

                    $comp_arr["descuentos"] = array(
                        array(
                            "codigo" => "02",
                            "descripcion" => "Descuento Global afecta a la base imponible",
                            "factor" => $factor,
                            "monto" => $dscto_m,
                            "base" => $base
                        )
                    );
                }

                //Envio de Json a la API - Boleta Gravada
                $comp_sunat = json_encode($comp_arr);

                $token = $comprobante->sucursal['token_facturador'];
                $ruta = $comprobante->sucursal['facturador'];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $ruta."/api/documents");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ));
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS,$comp_sunat);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $respuesta  = curl_exec($ch);
                curl_close($ch);
                $res_decode = json_decode($respuesta,true);

                if($res_decode['success'] == true){
                    $external_id   = $res_decode['data']['external_id'];
                    $pdf_document  = $res_decode['links']['pdf'];

                    $comprobante->external_id = $external_id;
                    $comprobante->id_estado_comprobante  = 1;
                    $comprobante->save();
                    DB::commit();

                    $pdf_document_local = '/inovicePDF/'.$comprobantes->id_comprobante.'&embedded=true';
                    return response()->json(['success'=> true,'pdf'=>$pdf_document_local,'pdf_sunat'=>$pdf_document, 'message' => 'Comprobante creado y validado.', 'id_comprobante' => $comprobantes->id_comprobante, 'print'=>$usuario_logeado->sucursal['impresion_automatica']]);
                }else{
                    $comprobante->id_estado_comprobante  = 3;
                    $comprobante->save();
                    DB::rollBack();
                    return response()->json([ 'error' => $respuesta,'success'=> false, 'message' => 'Error en la comprobancion con la SUNAT', 'id_comprobante'=>$comprobantes->id_comprobante]);
                }
            }

            
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json($e->getMessage(),500);
        }
    }

    public function storeByRoom(Request $request, $id)
    {

        DB::beginTransaction();
        try{
            $usuario_logeado = auth('sanctum')->user();
            $orden = MesaOrden::where('id_orden',$id)->first();
            $request_header = Comprobante::normalizeRequest($request->header,2);
            if(empty($orden)){
                $request_header['id_mesa'] = null; 
            }
            else{
                $request_header['id_mesa'] = $orden->id_mesa; 
            }
            $request_header['id_mozo'] = $request->header["id_usuario"];

            $comprobantes = Comprobante::create(collect($request_header)->all());

            /*foreach ($request->detail as $value) {
                $value = ComprobanteDetalle::normalizeRequest($value,$comprobantes->id_comprobante);
                ComprobanteDetalle::create(collect($value)->all());
                $product = Productos::find($value["id_producto"]);
                if($product->tipo_producto_combo == 3){
                    $product->decrement('stock', $value["cantidad"]);
                }
                
            }*/

            foreach ($request->detail as $value) {
                //$value = ComprobanteDetalle::normalizeRequest($value,$comprobantes->id_comprobante);
                $value['id_comprobante']=$comprobantes->id_comprobante;
                if($value['tipo_producto']==2){
                    $value['id_combo']=$request['id_producto'];
                }
                ComprobanteDetalle::create(collect($value)->all());
                $product = Productos::find($value["id_producto"]);
                $product_sucursal = ProductosSucursal::where('id_producto',$value["id_producto"])
                                    ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                    ->first();
                if($product->tipo_producto_combo == 3){
                    //$product->decrement('stock', $value["cantidad"]);
                    $product_sucursal->decrement('stock', $value["cantidad"]);
                }

                //RECETA DESCUENTO
                if($product->recetas){
                    foreach ($product->recetas as $receta) {
                        $receta_detalle = RecetasDetalle::where('id_receta',$receta->id_receta)->get();
                        foreach ($receta_detalle as $insumo_receta) {
                            //$insumo = Insumos::findOrFail($insumo_receta["id_insumo"]);
                            $insumo = InsumosSucursal::where('id_insumo',$insumo_receta["id_insumo"])
                                    ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                    ->first();
                            $cantidad = $insumo_receta["cantidad"] * $request->cantidad;
                            $cant =  ($insumo['stock'] - $cantidad);
                            $insumo->stock = $cant;
                            $insumo->save();
                            var_dump($insumo->stock);
                        }
                    }
                }
                
            }

            if($comprobantes->id_tipo_comprobante==3){

                $orden = MesaOrden::where('id_orden',$id)->first();
                if(isset($orden)){
                    $orden->delete();
                }
                $this->removeOrder($id);

                $comprobante = Comprobante::find($comprobantes->id_comprobante);
                $comprobante->id_estado_comprobante  = 1;
                $comprobante->save();

                DB::commit();

                $pdf_document = '/generarTicketPDF/'.$comprobantes->id_comprobante.'&embedded=true';
                return response()->json(['success'=> true,'pdf'=>$pdf_document, 'message' => 'Comprobante creado y validado.', 'id_comprobante' => $comprobantes->id_comprobante]);
            }
            else{
                $comprobante         = Comprobante::find($comprobantes->id_comprobante);
                $comprobante_detalle = ComprobanteDetalle::where("id_comprobante", $comprobantes->id_comprobante)->get();
                $items = array();

                //Array Api - Boleta Gravada Items[]
                foreach($comprobante_detalle as $cmp_d){
                    $product_i = Productos::find($cmp_d->id_producto );
                    $valor_unitario = $cmp_d->precio_unitario / 1.10;
                    $total_base_igv = $cmp_d->precio_total / 1.10;//subtotal
                    $total_igv      = $cmp_d->precio_total - $total_base_igv;

                    $items[] = array(
                        "codigo_interno"             => $product_i->codigo_producto."-".$product_i->id_producto,
                        "descripcion"                => $product_i->nombre_producto,
                        "codigo_producto_sunat"      => "",
                        "codigo_producto_gsl"        => "",
                        "unidad_de_medida"           => $product_i->unidad_medida->codigo_sunat,
                        "cantidad"                   => $cmp_d->cantidad,
                        "valor_unitario"             => number_format($valor_unitario, 2, '.', ''),
                        "codigo_tipo_precio"         => "01",
                        "precio_unitario"            => number_format($cmp_d->precio_unitario, 2, '.', ''),
                        "codigo_tipo_afectacion_igv" => "10",
                        "total_base_igv"             => number_format($total_base_igv, 2, '.', ''),
                        "porcentaje_igv"             => 10,
                        "total_igv"                  => number_format($total_igv, 2, '.', ''),
                        "total_impuestos"            => number_format($total_igv, 2, '.', ''),
                        "total_valor_item"           => number_format($total_base_igv, 2, '.', ''),
                        "total_item"                 => $cmp_d->precio_total,
                    );
                }

                //Array Api - Boleta Gravada
                $codigo_tipo_documento           = $comprobante->id_tipo_comprobante != 1? "01" : "03"; //Factura : Boleta
                //$codigo_tipo_documento_identidad = $comprobante->id_tipo_comprobante != 1? "1" : "6"; //DNI : RUC
                $codigo_tdi = "1";
                $stlen = strlen($comprobante->nro_documento);
                switch ($stlen) {
                    case 8:
                        $codigo_tdi = "1";
                        break;
                    case 11:
                        $codigo_tdi = "6";
                        break;
                    case 9:
                        $codigo_tdi = "4";
                        break;
                    case 12:
                        $codigo_tdi = "7";
                        break;
                    default:
                        $codigo_tdi = "0";
                        break;
                }
                $codigo_tipo_documento_identidad = $codigo_tdi; //strlen($comprobante->nro_documento) == 8 ? "1" : "6"; //DNI : RUC
                
                $hora_de_emision = date("h:i:s");
                
                /** TEMPORAL FIX */

                    $total = $comprobante->total;
                    $subtotal = number_format($total/1.10, 2, '.', '');
                    $igv = number_format($total-$subtotal, 2, '.', '');
                    
                $comp_arr = array(
                    "serie_documento"        => $comprobante->serie->serie,
                    "numero_documento"       => '#',//$comprobante->correlativo,
                    "fecha_de_emision"       => $comprobante->fecha_emision,
                    "hora_de_emision"        => $hora_de_emision, //extraer hora de emision de request
                    "codigo_tipo_operacion"  => "0101", //Operacion Sunat
                    "codigo_tipo_documento"  => $codigo_tipo_documento,
                    "codigo_tipo_moneda"     => "PEN",
                    "fecha_de_vencimiento"   => $comprobante->fecha_emision, //la misma fecha de emision
                    "numero_orden_de_compra" => "", //Opcional
                    "formato_pdf"            => $comprobante->formato_impresion,
                    "datos_del_emisor" => array(
                        "codigo_pais"                      => "PE",
                        "ubigeo"                           => "040101",
                        "direccion"                        => $comprobante->sucursal['direccion_fiscal'],
                        "correo_electronico"               => $comprobante->sucursal['email'], 
                        "telefono"                         => $comprobante->sucursal['telefono'], 
                        "codigo_del_domicilio_fiscal"      => $comprobante->sucursal['cod_domicilio_fiscal'], 
                    ),
                    "documento_afectado" => array(
                        "external_id"    => $comprobante->external_id,
                    ),
                    "datos_del_cliente_o_receptor" => array(
                        "codigo_tipo_documento_identidad"    => $codigo_tipo_documento_identidad,
                        "numero_documento"                   => $comprobante->nro_documento,
                        "apellidos_y_nombres_o_razon_social" => $comprobante->nombre_cliente,
                        "codigo_pais"                        => "PE",
                        "ubigeo"                             => "",
                        "direccion"                          => $comprobante->direccion_cliente,
                        "correo_electronico"                 => "", //Incluir correo
                        "telefono"                           => ""  //Incluir telefono
                    ),
                    "totales" => array(
                        "total_exportacion"            => 0.00,
                            "total_operaciones_gravadas"   => number_format($subtotal, 2, '.', ''),
                            "total_operaciones_inafectas"  => 0.00,
                            "total_operaciones_exoneradas" => 0.00,
                            "total_operaciones_gratuita"   => 0.00,
                            "total_igv"                    => number_format($igv,2, '.', ''),
                            "total_impuestos"              => number_format($igv,2, '.', ''), //Total de Impuestos?
                            "total_valor"                  => number_format($subtotal, 2, '.', ''),
                            "total_venta"                  => $comprobante->total,
                    ),
                    "items" => $items,
                    "informacion_adicional" => 'Forma de Pago: '.$comprobante->medio_pago["medio_pago"] .' '. $request->header["informacion_adicional"],
                    "acciones" => array("enviar_xml_firmado"=> false, "enviar_email" => false, "formato_pdf" => "ticket")
                );

                if($comprobante->total_dscto>0){
                    $base = number_format(($comprobante->total + $comprobante->total_dscto) / 1.10, 2, '.', '');
                    $dscto_m = number_format($comprobante->total_dscto / 1.10, 2, '.', '');
                    $factor = number_format($dscto_m/$base, 5, '.', '');

                    $comp_arr["totales"]["total_descuentos"] = $comprobante->total_dscto;

                    $comp_arr["descuentos"] = array(
                        array(
                            "codigo" => "02",
                            "descripcion" => "Descuento Global afecta a la base imponible",
                            "factor" => $factor,
                            "monto" => $dscto_m,
                            "base" => $base
                        )
                    );
                }
                //Envio de Json a la API - Boleta Gravada
                $comp_sunat = json_encode($comp_arr);

                $token = $comprobante->sucursal['token_facturador'];
                $ruta = $comprobante->sucursal['facturador'];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $ruta."/api/documents");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ));
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS,$comp_sunat);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $respuesta  = curl_exec($ch);
                curl_close($ch);
                $res_decode = json_decode($respuesta,true);
                //var_dump($res_decode);
                $orden = MesaOrden::where('id_orden',$id)->first();
                if(isset($orden)){
                    $orden->delete();
                }
                $this->removeOrder($id);

                if($res_decode['success'] == true){
                    $external_id   = $res_decode['data']['external_id'];
                    $pdf_document  = $res_decode['links']['pdf'];
                    $doc_correlativo  = explode("-", $res_decode['data']['number']);

                    $comprobante->external_id = $external_id;
                    $comprobante->id_estado_comprobante  = 1;
                    $comprobante->correlativo  = $doc_correlativo[1];
                    $comprobante->save();
                    
                    $pdf_cpe_embed = $ruta."print/document/".$external_id.'/ticket';

                    DB::commit();
                    
                    return response()->json(['success'=> true,'pdf'=>$pdf_cpe_embed, 'message' => 'Comprobante creado y validado.', 'id_comprobante' => $comprobantes->id_comprobante, 'print'=>$usuario_logeado->sucursal['impresion_automatica']]);
                }else{
                    $comprobante->id_estado_comprobante  = 3;
                    $comprobante->save();

                    DB::rollBack();

                    return response()->json(['success'=> false, 'message' => 'Error en la comprobancion con la SUNAT', 'id_comprobante'=>$comprobantes->id_comprobante, 'error' => $respuesta]);
                }
            }

            
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json($e->getMessage(),500);
        }
    }

    public function vista($id){
        $document = Comprobante::findOrFail($id);
        $detail = ComprobanteDetalle::where('id_comprobante',$id)->get();
        return view('ticket_alojamiento', compact('document','detail'));
    }

    public function generarPDF($id){
        $html = $this->vista($id)->render();
        $file = 'pdf_'.'pdf';
        $mpdf = new Mpdf(
            ['mode' => 'utf-8',
            'format' => [80, 220],
            'margin_top' => 2,
            'margin_right' => 5,
            'margin_bottom' => 0,
            'margin_left' => 5 ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output($file,'I');
        //return  $html;
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
            $comprobante = Comprobante::findOrFail($id);
            return response()->json($comprobante, 200);
        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function showDetail($id)
    {
        try{
            $comprobante = ComprobanteDetalle::where('id_comprobante',$id)->get();
            return response()->json($comprobante, 200);
        }catch(Exception $e){
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

    public function anularComprobante(Request $request, $id){
        try{
            $comprobante = Comprobante::find($id);
            $upd_data = array(
                'id_estado_comprobante' => 2,
                'motivo_anulacion'  =>  $request->motivo_anulacion,
                'fecha_anulacion'  =>  date('Y-m-d'),
            );
            $comprobante->update($upd_data);
            
            $token = $comprobante->sucursal['token_facturador'];
            $ruta = $comprobante->sucursal['facturador'];
            $flag = 0;

            $fecha_de_emision_de_documentos = date("Y-m-d", strtotime($comprobante->fecha_emision));

            if($comprobante->id_tipo_comprobante == 2){
                $ruta_envio_cpe = "/api/documents/send";
                $ruta_anulacion = "/api/voided"; //ruta post para acceder a API

                //Array API-Anulacion Documentos[]
                $documentos_arr[] = array(
                    "external_id"      => $comprobante->external_id,
                    "motivo_anulacion" => $comprobante->motivo_anulacion
                );
                //Array API-Anulacion
                $inv_voided = array(
                    "fecha_de_emision_de_documentos" => $fecha_de_emision_de_documentos,
                    "documentos" => $documentos_arr
                );

                //Primero enviamos Factura a Sunat
                $envio_cpe_json = array("external_id" => $comprobante->external_id);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $ruta.$ruta_envio_cpe);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ));
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($envio_cpe_json));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $respuesta  = curl_exec($ch);
                curl_close($ch);
                //FIN envio CPE
                //dd($respuesta);

                $flag = 1;


            }else if($comprobante->id_tipo_comprobante == 1){
                $ruta_envio_cpe = "/api/summaries";
                $ruta_anulacion = "/api/summaries";

                //Array API-Resumen Diario Anulacion Documentos[]
                $documentos_arr[] = array(
                    "external_id"      => $comprobante->external_id,
                    "motivo_anulacion" => $comprobante->motivo_anulacion
                );

                //Array API-Resumen Diario Anulacion
                $inv_voided = array(
                    "fecha_de_emision_de_documentos" => $fecha_de_emision_de_documentos,
                    "codigo_tipo_proceso" => "3",
                    "documentos" => $documentos_arr
                );

                //ENVIO RESUMEN DIARIO
                $envio_cpe_json = array("fecha_de_emision_de_documentos" => $fecha_de_emision_de_documentos, "codigo_tipo_proceso" => "1");
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $ruta.$ruta_envio_cpe);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ));
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($envio_cpe_json));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                $respuesta  = curl_exec($ch);
                curl_close($ch);
                //FIN ENVIO RESUMEN DIARIO
                //dd($respuesta);
                $flag = 1;


            }
            
            if($flag == 1){

                //Conversion de Array a Json
                $convertido = json_encode($inv_voided);
                //echo $convertido;
        
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $ruta.$ruta_anulacion);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ));
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $convertido);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $respuesta  = curl_exec($ch);
                curl_close($ch);
        
                //dd($respuesta);
        
                $res_decode = json_decode($respuesta,true);
            }
            
            return response()->json(['message' =>'Comprobante anulado.'], 200);

             
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}
