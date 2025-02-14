<?php

namespace App\Http\Controllers;
use App\Models\AlmacenMovimientos;
use App\Models\Comprobante;
use App\Models\ComprobanteDetalle;
use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Models\MesaOrden;
use App\Models\Productos;
use App\Models\Insumos;
use App\Models\ProductosSucursal;
use App\Models\InsumosSucursal;
use App\Models\RecetasDetalle;
use App\Models\Serie;
use App\Models\Clientes;
use App\Models\Caja;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
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
            $request_header = Comprobante::normalizeRequest($request->header,2);
            $request_header['id_mozo'] = $usuario_logeado->id;
            $comprobantes = Comprobante::create(collect($request_header)->all());

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


            }

            if($comprobantes->id_tipo_comprobante==3){
                $comprobante = Comprobante::find($comprobantes->id_comprobante);
                $comprobante->id_estado_comprobante  = 1;
                $comprobante->save();
                $pdf_document = '/generarTicketPDF/'.$comprobantes->id_comprobante.'&embedded=true';
                return response()->json(['success'=> true,'pdf'=>$pdf_document, 'message' => 'Comprobante creado y validado.', 'id_comprobante' => $comprobantes->id_comprobante, 'print'=>$usuario_logeado->sucursal['impresion_automatica']]);
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
                $codigo_tipo_documento_identidad = $codigo_tdi;// strlen($comprobante->nro_documento) == 8 ? "1" : "6"; //DNI : RUC
                $hora_de_emision = date("h:i:s");

                $comp_arr = array(
                    "serie_documento"        => $comprobante->serie->serie,
                    "numero_documento"       => $comprobante->correlativo,
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
                        "total_operaciones_gravadas"   => number_format($comprobante->subtotal, 2, '.', ''),
                        "total_operaciones_inafectas"  => 0.00,
                        "total_operaciones_exoneradas" => 0.00,
                        "total_operaciones_gratuita"   => 0.00,
                        "total_igv"                    => number_format($comprobante->igv,2, '.', ''),
                        "total_impuestos"              => number_format($comprobante->igv,2, '.', ''), //Total de Impuestos?
                        "total_valor"                  => number_format($comprobante->subtotal, 2, '.', ''),
                        "total_venta"                  => $comprobante->total,
                    ),
                    "items" => $items,
                    "informacion_adicional" => 'Comentario: '.$comprobante->comentario.' / Forma de Pago: '.$comprobante->medio_pago["medio_pago"],
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
                    return response()->json(['comp_arr'=>$comp_arr,'success'=> true,'pdf'=>$pdf_document, 'message' => 'Comprobante creado y validado.', 'id_comprobante' => $comprobantes->id_comprobante, 'print'=>$usuario_logeado->sucursal['impresion_automatica']]);
                }else{
                    $comprobante->id_estado_comprobante  = 3;
                    $comprobante->save();
                    return response()->json(['success'=> false, 'message' => 'Error en la comprobancion con la SUNAT', 'id_comprobante'=>$comprobantes->id_comprobante, 'error' => $respuesta]);
                }
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
            $comprobante = Comprobante::findOrFail($id);
            return response()->json($comprobante, 200);
        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function showDetail($id)
    {
        try{
            $comprobante = ComprobanteDetalle::where('id_comprobante',$id)->get();
            return response()->json($comprobante, 200);
        }catch(\Exception $e){
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

                DB::beginTransaction();
                $usuario_logeado = auth('sanctum')->user();

                //$request = Comprobante::normalizeRequest($request,2);
                /*$comprobante = Comprobante::findOrFail($id);
                $comprobante->update($request->all());*/

                $last_inv = Comprobante::where("id_serie",$request->id_serie)->latest()->first();
                //dd($last_inv);
                if($last_inv != null){
                    $correaltivo_fin = $last_inv->correlativo + 1;

                }else{
                    if($request->id_serie == '1'){

                        $correaltivo_fin = 1996;

                    }elseif($request->id_serie == '2'){

                        $correaltivo_fin = 245;

                    }else{
                        $correaltivo_fin = 1;
                    }
                }

                $data = Caja::where('id_sucursal',$usuario_logeado->id_sucursal)->latest()->first();
                $nro_caja = $data->id_caja;

                $upd_data = array();
                if(isset($request->id_cliente)){
                    $clie = Clientes::find($request->id_cliente);
                    $upd_data = array(
                        "id_cliente" => $request->id_cliente,
                        "id_serie"   => $request->id_serie,
                        "id_tipo_comprobante" => $request->id_tipo_comprobante,
                        "nombre_cliente"      => $clie->nombre,
                        "nro_documento"       => $clie->nro_doc,
                        "direccion_cliente"   => $clie->direccion,
                        "correlativo"         => $correaltivo_fin,
                        "id_caja"             => $nro_caja
                    );
                }
                else{
                    if(strlen($request->nro_documento)<3 || strlen($request->nombre_cliente)<3 || strlen($request->direccion_cliente)<3){
                        $request['id_cliente'] = 0;
                        $request['nombre_cliente'] = "CLIENTES VARIOS";
                        $request['nro_documento'] = "88888888";
                        $request['direccion_cliente'] = "-";
                    }

                    $upd_data = array(
                        "id_cliente" => $request->id_cliente,
                        "id_serie"   => $request->id_serie,
                        "id_tipo_comprobante" => $request->id_tipo_comprobante,
                        "nombre_cliente"      => $request->nombre_cliente,
                        "nro_documento"       => $request->nro_documento,
                        "direccion_cliente"   => $request->direccion_cliente,
                        "correlativo"         => $correaltivo_fin,
                        "id_caja"             => $nro_caja
                    );
                }

                $comprobante = Comprobante::findOrFail($id);
                $comprobante->update($upd_data);

                $comprobante_detalle = ComprobanteDetalle::where("id_comprobante", $id)->get();
                $items = array();

                $nueva_serie = Serie::find($request->id_serie);

                $comprobante_n = Comprobante::findOrFail($id);

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
                $codigo_tipo_documento           = $comprobante_n->id_tipo_comprobante != 1? "01" : "03"; //Factura : Boleta
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
                $codigo_tipo_documento_identidad = $codigo_tdi;// strlen($comprobante->nro_documento) == 8 ? "1" : "6";
                $hora_de_emision = date("h:i:s");
                
                /** TEMPORAL FIX */

                $total = $comprobante->total;
                $subtotal = number_format($total/1.10, 2, '.', '');
                $igv = number_format($total-$subtotal, 2, '.', '');

                $comp_arr = array(
                    "serie_documento"        => $nueva_serie->serie,
                    "numero_documento"       => $comprobante_n->correlativo,
                    "fecha_de_emision"       => $comprobante_n->fecha_emision,
                    "hora_de_emision"        => $hora_de_emision, //extraer hora de emision de request
                    "codigo_tipo_operacion"  => "0101", //Operacion Sunat
                    "codigo_tipo_documento"  => $codigo_tipo_documento,
                    "codigo_tipo_moneda"     => "PEN",
                    "fecha_de_vencimiento"   => $comprobante_n->fecha_emision, //la misma fecha de emision
                    "numero_orden_de_compra" => "", //Opcional
                    "formato_pdf"            => $comprobante_n->formato_impresion,
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
                        "numero_documento"                   => $comprobante_n->nro_documento,
                        "apellidos_y_nombres_o_razon_social" => $comprobante_n->nombre_cliente,
                        "codigo_pais"                        => "PE",
                        "ubigeo"                             => "",
                        "direccion"                          => $comprobante_n->direccion_cliente,
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
                    "acciones" => array("enviar_xml_firmado"=> false, "enviar_email" => false, "formato_pdf" => "ticket"),
                );

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

                    $pdf_cpe_embed = $ruta."/print/document/".$external_id."/ticket";

                    DB::commit();
                    return response()->json(['success'=> true,'pdf'=>$pdf_cpe_embed, 'message' => 'Comprobante creado y validado.', 'id_comprobante' => $comprobante->id_comprobante]);
                }else{
                    $comprobante->id_estado_comprobante  = 3;
                    $comprobante->save();
                    DB::rollBack();
                    return response()->json(['success'=> false, 'message' => 'Error en la comprobancion con la SUNAT', 'id_comprobante'=>$comprobante->id_comprobante]);
                }
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

    }

    public function preLoadData($id){
        try{
            $orden = Orden::findOrFail($id);
            $data = [];
            $data['id_cliente'] = $orden->id_cliente;
            $data['nombre_cliente'] = $orden->cliente["nombre"];
            $data['nro_documento'] = $orden->cliente["nro_doc"];
            $data['direccion_cliente'] = $orden->cliente["direccion"];
            $data['fecha_emision'] = date('Y-m-d H:i:s');
            return response()->json($data, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    /*public function storeByOrder(Request $request, $id)
    {

        try{

            DB::beginTransaction();
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

            //foreach ($request->detail as $value) {
            //    $value = ComprobanteDetalle::normalizeRequest($value,$comprobantes->id_comprobante);
            //    ComprobanteDetalle::create(collect($value)->all());
            //   $product = Productos::find($value["id_producto"]);
            //    if($product->tipo_producto_combo == 3){
            //        $product->decrement('stock', $value["cantidad"]);
            //    }
            //}

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
                // if($product->recetas){
                //     foreach ($product->recetas as $receta) {
                //         $receta_detalle = RecetasDetalle::where('id_receta',$receta->id_receta)->get();
                //         foreach ($receta_detalle as $insumo_receta) {
                //             //$insumo = Insumos::findOrFail($insumo_receta["id_insumo"]);
                //             $insumo = InsumosSucursal::where('id_insumo',$insumo_receta["id_insumo"])
                //                     ->where('id_sucursal',$usuario_logeado->id_sucursal)
                //                     ->first();
                //             $cantidad = $insumo_receta["cantidad"] * $request->cantidad;
                //             $cant =  ($insumo['stock'] - $cantidad);
                //             $insumo->stock = $cant;
                //             $insumo->save();
                //             var_dump($insumo->stock);
                //         }
                //     }
                // }

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
                return response()->json(['success'=> true,'pdf'=>$pdf_document, 'message' => 'Comprobante creado y validado.', 'id_comprobante' => $comprobantes->id_comprobante, 'print'=>$usuario_logeado->sucursal['impresion_automatica']]);
            }
            else{
                $comprobante         = Comprobante::find($comprobantes->id_comprobante);
                $comprobante_detalle = ComprobanteDetalle::where("id_comprobante", $comprobantes->id_comprobante)->get();
                $items = array();

                //Array Api - Boleta Gravada Items[]
                foreach($comprobante_detalle as $cmp_d){
                    $product_i = Productos::find($cmp_d->id_producto );
                    $valor_unitario = $cmp_d->precio_unitario / 1.18;
                    $total_base_igv = $cmp_d->precio_total / 1.18;//subtotal
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
                        "porcentaje_igv"             => 18,
                        "total_igv"                  => number_format($total_igv, 2, '.', ''),
                        "total_impuestos"            => number_format($total_igv, 2, '.', ''),
                        "total_valor_item"           => number_format($total_base_igv, 2, '.', ''),
                        "total_item"                 => $cmp_d->precio_total,
                    );
                }

                //Array Api - Boleta Gravada
                $codigo_tipo_documento           = $comprobante->id_tipo_comprobante != 1? "01" : "03"; //Factura : Boleta
                //$codigo_tipo_documento_identidad = $comprobante->id_tipo_comprobante != 1? "1" : "6"; //DNI : RUC
                $codigo_tipo_documento_identidad = strlen($comprobante->nro_documento) == 8 ? "1" : "6"; //DNI : RUC
                //Fecha emisiom
                $hora_de_emision = date("h:i:s");

                $comp_arr = array(
                    "serie_documento"        => $comprobante->serie->serie,
                    "numero_documento"       => $comprobante->correlativo,
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
                        "total_operaciones_gravadas"   => number_format($comprobante->subtotal, 2, '.', ''),
                        "total_operaciones_inafectas"  => 0.00,
                        "total_operaciones_exoneradas" => 0.00,
                        "total_operaciones_gratuita"   => 0.00,
                        "total_igv"                    => number_format($comprobante->igv,2, '.', ''),
                        "total_impuestos"              => number_format($comprobante->igv,2, '.', ''), //Total de Impuestos?
                        "total_valor"                  => number_format($comprobante->subtotal, 2, '.', ''),
                        "total_venta"                  => $comprobante->total,
                    ),
                    "items" => $items,
                    "informacion_adicional" => 'Forma de Pago: '.$comprobante->medio_pago["medio_pago"],
                    "acciones" => array("formato_pdf" => "ticket")
                );

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

                    $comprobante->external_id = $external_id;
                    $comprobante->id_estado_comprobante  = 1;
                    $comprobante->save();


                    DB::commit();
                    return response()->json(['success'=> true,'pdf'=>$pdf_document, 'message' => 'Comprobante creado y validado.', 'id_comprobante' => $comprobantes->id_comprobante, 'print'=>$usuario_logeado->sucursal['impresion_automatica']]);
                }else{
                    $comprobante->id_estado_comprobante  = 3;
                    $comprobante->save();
                    DB::rollBack();

                    return response()->json(['success'=> false, 'message' => 'Error en la comprobancion con la SUNAT', 'id_comprobante'=>$comprobantes->id_comprobante, 'error' => $respuesta]);
                }
            }


        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }*/
    public function storeByOrder(Request $request, $id)
    {

        try{
            $whichIf = [];
            DB::beginTransaction();
            //DB::transaction(function () {
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

            foreach ($request->detail as $value) {
                $value = ComprobanteDetalle::normalizeRequest($value,$comprobantes->id_comprobante);
                $value['id_comprobante']=$comprobantes->id_comprobante;
                

                ComprobanteDetalle::create(collect($value)->all());
                $product = Productos::find($value["id_producto"]);
                $product_sucursal = ProductosSucursal::where('id_producto',$value["id_producto"])->where('id_sucursal',$usuario_logeado->id_sucursal)->first();

                //Si es Combo
                if($value['tipo_producto']==2){
                    $whichIf = [$value,1];
                    //$value['id_combo']=$request['id_producto'];
                    $combo = OrdenDetalle::where([['id_producto',$value["id_producto"]],['id_orden',$id]])->first();
                    $combo_detalle = json_decode($combo->detalle_combo, true);
                   
                    if(isset($combo_detalle)){ 
                        foreach ($combo_detalle as $prod) {
                            $p = Productos::find($prod["id_producto"]);
                            $ps = ProductosSucursal::where('id_producto',$prod["id_producto"])->where('id_sucursal',$usuario_logeado->id_sucursal)->first();
                            if($p["tipo_producto_combo"] == 3){
                                $act_p = $ps->stock;
    
                                $mov = new AlmacenMovimientos();
                                $mov->id_sucursal = $usuario_logeado->id_sucursal;
                                $mov->id_usuario  = $usuario_logeado->id;
                                $mov->id_tipo_movimiento  = 3;
                                $mov->id_producto = $prod["id_producto"];
                                //$mov->id_comprobante = $comprobantes->id_comprobante;
                                $mov->tipo_producto_insumo = 0;
                                $mov->nombre_producto_insumo = $p->nombre_producto;
                                $mov->cantidad = $prod["cantidad"];
                                $mov->precio_unitario = $ps->precio;
                                $mov->precio_total = $ps->precio * $prod["cantidad"];
                                $mov->stock_actual = $act_p; //$act_p - $prod["cantidad"];
                                $mov->fecha_movimiento = date('Y-m-d');
                                $mov->save();
    
                                $ps->decrement('stock', $prod["cantidad"]);
                                $ps->save();
                            }
                            else{
                                if($p->recetas){
                                    foreach ($p->recetas as $p_receta) {
                                        $p_receta_detalle = RecetasDetalle::where('id_receta',$p_receta->id_receta)->get();
    
                                        foreach ($p_receta_detalle as $p_insumo_receta) {
    
                                            if($p_insumo_receta['id_insumo'] != null){
                                                $insumo = InsumosSucursal::where('id_insumo',$p_insumo_receta["id_insumo"])
                                                    ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                                    ->first();
                                                $insumo_general = Insumos::where('id_insumo',$p_insumo_receta["id_insumo"])
                                                    ->first();

                                                $act = $insumo->stock;
                                                $cantidad = $p_insumo_receta["cantidad"] * $value["cantidad"];
                                                $cant =  ($insumo->stock - $cantidad);
                                            }else if($p_insumo_receta['id_producto'] != null){
                                                $insumo = ProductosSucursal::where('id_producto',$p_insumo_receta["id_producto"])
                                                    ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                                    ->first();
                                                $insumo_general = Productos::where('id_insumo',$p_insumo_receta["id_insumo"])
                                                    ->first();
                                                
                                                $act = $insumo->stock;
                                                $cantidad = $p_insumo_receta["cantidad"] * $value["cantidad"];
                                                $cant =  ($insumo->stock - $cantidad);
                                            }
                                            else{
                                                throw new \Exception("Error: details are not correct");
                                            }


                                            $movimiento = new AlmacenMovimientos();
                                            $movimiento->id_sucursal = $usuario_logeado->id_sucursal;
                                            $movimiento->id_usuario  = $usuario_logeado->id;
                                            $movimiento->id_tipo_movimiento  = ($p_insumo_receta["id_insumo"] != null) ? 4 : 3;
                                            $movimiento->id_insumo = $p_insumo_receta["id_insumo"];
                                            $movimiento->id_producto = $p_insumo_receta["id_producto"];
                                            //$movimiento->id_comprobante = $comprobantes->id_comprobante;
                                            $movimiento->tipo_producto_insumo = ($p_insumo_receta["id_insumo"] != null) ? 1 : 0;
                                            $movimiento->nombre_producto_insumo = ($p_insumo_receta["id_insumo"] != null) ? $insumo_general->nombre_insumo : $insumo_general->nombre_producto;
                                            $movimiento->cantidad = $cantidad;
                                            $movimiento->precio_unitario = $product_sucursal->precio;
                                            $movimiento->precio_total = $product_sucursal->precio * $value["cantidad"];
                                            $movimiento->stock_actual = $act;//$act - $value["cantidad"];
                                            $movimiento->fecha_movimiento = date('Y-m-d');
                                            $movimiento->save();

                                            $cantidad = $p_insumo_receta["cantidad"] * $prod["cantidad"];
                                            $cant =  ($insumo->stock - $cantidad);
                                            $insumo->stock = $cant;
                                            $insumo->save();
    
                                            $cant_general = ($insumo_general->stock - $cantidad);
                                            $insumo_general->stock = $cant_general;
                                            $insumo_general->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //Si es Producto
                }else if($value['tipo_producto']==3){
                    $whichIf = [$value,2];
                    //$product->decrement('stock', $value["cantidad"]);
                    
                    $product_sucursal->decrement('stock', $value["cantidad"]);
                    $product_sucursal->save();
                    
                    $act = $product_sucursal->stock;

                    $movimiento = new AlmacenMovimientos();
                    $movimiento->id_sucursal = $usuario_logeado->id_sucursal;
                    $movimiento->id_usuario  = $usuario_logeado->id;
                    $movimiento->id_tipo_movimiento  = 3;
                    $movimiento->id_producto = $value["id_producto"];
                    //$movimiento->id_comprobante = $comprobantes->id_comprobante;
                    $movimiento->tipo_producto_insumo = 0;
                    $movimiento->nombre_producto_insumo = $product->nombre_producto;
                    $movimiento->cantidad = $value["cantidad"];
                    $movimiento->precio_unitario = $product_sucursal->precio;
                    $movimiento->precio_total = $product_sucursal->precio * $value["cantidad"];
                    $movimiento->stock_actual = $act;//$act - $value["cantidad"];
                    $movimiento->fecha_movimiento = date('Y-m-d');
                    $movimiento->save();

                    //Si es Plato
                }else{
                    $whichIf = [$value,3];
                    //RECETA DESCUENTO
                    if($product->recetas){
                        foreach ($product->recetas as $receta) {
                            $receta_detalle = RecetasDetalle::where('id_receta',$receta->id_receta)->get();
                            foreach ($receta_detalle as $insumo_receta) {
                                if($insumo_receta["id_insumo"] != null){
                                    $_insumo = Insumos::findOrFail($insumo_receta["id_insumo"]);
                                    $insumo = InsumosSucursal::where('id_insumo',$insumo_receta["id_insumo"])
                                        ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                        ->first();
                                    $act = $insumo->stock;
                                    $cantidad = $insumo_receta["cantidad"] * $value["cantidad"];
                                    $cant =  ($insumo->stock - $cantidad);
                                }else{
                                    $_insumo = Productos::findOrFail($insumo_receta["id_producto"]);
                                    $insumo = ProductosSucursal::where('id_producto',$insumo_receta["id_producto"])
                                        ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                        ->first();
                                    $act = $insumo->stock;
                                    $cantidad = $insumo_receta["cantidad"] * $value["cantidad"];
                                    $cant =  ($insumo->stock - $cantidad);
                                }
                                /*
                                $_insumo = Insumos::findOrFail($insumo_receta["id_insumo"]);
                                $insumo = InsumosSucursal::where('id_insumo',$insumo_receta["id_insumo"])
                                        ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                        ->first();
                                $act = $insumo->stock;
                                $cantidad = $insumo_receta["cantidad"] * $value["cantidad"];
                                $cant =  ($insumo->stock - $cantidad);*/
                                //var_dump($request->cantidad);

                                $movimiento = new AlmacenMovimientos();
                                $movimiento->id_sucursal = $usuario_logeado->id_sucursal;
                                $movimiento->id_usuario  = $usuario_logeado->id;
                                $movimiento->id_tipo_movimiento  = ($insumo_receta["id_insumo"] != null) ? 4 : 3;
                                $movimiento->id_insumo = $insumo_receta["id_insumo"];
                                $movimiento->id_producto = $insumo_receta["id_producto"];
                                //$movimiento->id_comprobante = $comprobantes->id_comprobante;
                                $movimiento->tipo_producto_insumo = ($insumo_receta["id_insumo"] != null) ? 1 : 0;
                                $movimiento->nombre_producto_insumo = ($insumo_receta["id_insumo"] != null) ? $_insumo->nombre_insumo : $_insumo->nombre_producto;
                                $movimiento->cantidad = $cantidad;
                                $movimiento->precio_unitario = $product_sucursal->precio;
                                $movimiento->precio_total = $product_sucursal->precio * $value["cantidad"];
                                $movimiento->stock_actual = $act;//$act - $value["cantidad"];
                                $movimiento->fecha_movimiento = date('Y-m-d');
                                $movimiento->save();

                                $insumo->stock = $cant;
                                $insumo->save();

                                $_insumo->stock = $_insumo->stock -$cantidad;
                                $_insumo->save();
                                //var_dump($insumo->stock);
                            }
                        }
                    }
                }
            }
            //Clean Comporbante Request





            if($comprobantes->id_tipo_comprobante==3){

                $orden = MesaOrden::where('id_orden',$id)->first();
                if(isset($orden)){
                    $orden->delete();
                }
                $this->removeOrder($id);
                $comprobantes->id_estado_comprobante =  1;//5;
                $comprobantes->save();
                DB::commit();
                $pdf_document = '/generarTicketPDF/'.$comprobantes->id_comprobante.'&embedded=true';
                return response()->json(['success'=> true,'pdf'=>$pdf_document, 'message' => 'Comprobante creado y validado.', 'id_comprobante' => $comprobantes->id_comprobante, 'print'=>$usuario_logeado->sucursal['impresion_automatica'],'ifs' => $whichIf]);
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
                $codigo_tipo_documento           = $comprobante->id_tipo_comprobante != 1 ? "01" : "03"; //Factura : Boleta
                //$codigo_tipo_documento_identidad = $comprobante->id_tipo_comprobante != 1 ? "1" : "6"; //DNI : RUC
                //$codigo_tipo_documento_identidad = $comprobante->cliente->tipo_doc->codigo_sunat;
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
                $codigo_tipo_documento_identidad = $codigo_tdi;// strlen($comprobante->nro_documento) == 8 ? "1" : "6"; //DNI : RUC

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
                    "formato_pdf"            => 'ticket',
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
                    "acciones" => array("enviar_xml_firmado"=> false, "enviar_email" => false, "formato_pdf" => "ticket"),
                    "informacion_adicional" => 'Comentario: '.$comprobante->comentario.' / Forma de Pago: '.$comprobante->medio_pago["medio_pago"],
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

                    $pdf_cpe_embed = $ruta."/print/document/".$external_id.'/ticket';

                    DB::commit();
                    return response()->json(['success'=> true,'pdf'=>$pdf_cpe_embed, 'message' => 'Comprobante creado y validado.', 'id_comprobante' => $comprobantes->id_comprobante, 'print'=>$usuario_logeado->sucursal['impresion_automatica'],'respuesta'=>$respuesta,'ifs' => $whichIf]);
                }else{

                    $comprobante->id_estado_comprobante  = 3;
                    $comprobante->save();
                    DB::rollBack();

                    return response()->json(['success'=> false, 'message' => 'Error en la comprobancion con la SUNAT', 'id_comprobante'=>$comprobantes->id_comprobante, 'error' => $respuesta]);
                }
            }
            //});
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
    public function removeOrder($id){
        $detalle = OrdenDetalle::where('id_orden',$id)->get();
        foreach ($detalle as $key) {
            OrdenDetalle::findOrFail($key->id_detalle_orden)->delete();
        }
        $orden = Orden::findOrFail($id);
        $orden->delete();
    }

    public function getComprobantes(Request $request){
        try{
            $from = date($request->data["fecha_inicio"]);
            $to = date($request->data["fecha_fin"]);
            $id_mozo = $request->data["id_mozo"];
            $tipo_doc = null;
            $estado_doc = null;
            $usuario_logeado = auth('sanctum')->user();
            $datos = [];
            if(empty($request->data["fecha_inicio"]) && empty($request->data["fecha_fin"] && empty($id_mozo))){
                $datos = Comprobante::select("*", DB::raw("CONCAT(external_id,'/ticket') as external_id") )->where("id_sucursal",$usuario_logeado->id_sucursal)
                ->orderBy('id_comprobante', 'desc')
                ->paginate($request->perpage);
            }
            if(!empty($request->data["fecha_inicio"]) && !empty($request->data["fecha_fin"] && !empty($id_mozo))){
                $datos = Comprobante::select("*", DB::raw("CONCAT(external_id,'/ticket') as external_id") )->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])
                ->where("id_sucursal",$usuario_logeado->id_sucursal)
                ->where("id_mozo", $id_mozo)
                ->orderBy('id_comprobante', 'desc')
                ->paginate($request->perpage);
            }
            if(!empty($request->data["fecha_inicio"]) && !empty($request->data["fecha_fin"] && empty($id_mozo))){
                $datos = Comprobante::select("*", DB::raw("CONCAT(external_id,'/ticket') as external_id") )->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"])
                ->where("id_sucursal",$usuario_logeado->id_sucursal)
                ->orderBy('id_comprobante', 'desc')
                ->paginate($request->perpage);
            }
            return $datos;
            //return $datos->paginate($request->perpage)->last();
        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function vista($id){
        $document = Comprobante::findOrFail($id);
        $detail = ComprobanteDetalle::where('id_comprobante',$id)->get();
        return view('ticket', compact('document','detail'));
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
    }


    public function getComprobantesreport(Request $request) {

        try{
            $from = $request->data["fecha_inicio"];
            $to = $request->data["fecha_fin"];
            $tipo_doc = $request->data["id_tipo_comprobante"];
            $estado_doc = $request->data["id_estado"];
            $usuario_logeado = auth('sanctum')->user();

            $datos = Comprobante::select("*", DB::raw("CONCAT(external_id,'/ticket') as external_id") )->whereBetween('created_at', [$from." 00:00:00", $to." 23:59:59"]);
            if(!empty($request->data["id_tipo_comprobante"])){
                $datos->whereIn("id_tipo_comprobante", $request->data["id_tipo_comprobante"]);
            }
            if(!empty($request->data["id_estado"])){
                $datos->whereIn("id_estado_comprobante", $request->data["id_estado"]);
            }
            return $datos->paginate($request->perpage);
        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }


    public function anularComprobante(Request $request, $id){
        try{
            $comprobante = Comprobante::find($id);
            $upd_data = array(
                'id_estado_comprobante' => 2,
                'motivo_anulacion'  =>  $request->motivo_anulacion,
                'fecha_anulacion'  =>  date('Y-m-d'),
            );
            $detalle = ComprobanteDetalle::where("id_comprobante", $id)->get();
            $usuario_logeado = auth('sanctum')->user();
            foreach ($detalle as $det) {

                if(!str_contains($det->codigo_producto, 'ALJ00')) {
                    $product = Productos::find($det->id_producto);
                    $product_sucursal = ProductosSucursal::where('id_producto', $det->id_producto)->where('id_sucursal',$usuario_logeado->id_sucursal)->first();

                    //dd($det);
                    //Si es Combo
                    if($product->tipo_producto_combo==2){
                        //$value['id_combo']=$request['id_producto'];
                        $combo_detalle = json_decode($det->detalle_combo, true);

                        
                        foreach ($combo_detalle as $prod) {

                            $p = Productos::find($prod["id_producto"]);
                            $ps = ProductosSucursal::where('id_producto',$prod["id_producto"])->where('id_sucursal',$usuario_logeado->id_sucursal)->first();
                            if($p["tipo_producto_combo"] == 3){
                                $act_p = $ps->stock;

                                $mov = AlmacenMovimientos::create([
                                    'id_sucursal' => $usuario_logeado->id_sucursal,
                                    'id_usuario'  => $usuario_logeado->id,
                                    'id_tipo_movimiento'  => 5,
                                    'id_producto' =>  $prod["id_producto"],
                                    'id_comprobante' => $comprobante->id_comprobante,
                                    'tipo_producto_insumo' => 0,
                                    'nombre_producto_insumo' => $p->nombre_producto,
                                    'cantidad' => $prod["cantidad"],
                                    'precio_unitario' =>  $ps->precio,
                                    'precio_total' => $ps->precio * $prod["cantidad"],
                                    'stock_actual' => $act_p, //act_p + $prod["cantidad"],
                                    'fecha_movimiento' => date('Y-m-d'),
                                ]);

                                $ps->increment('stock', $prod["cantidad"]);
                                $ps->save();
                            }
                            else{
                                if($p->recetas){
                                    foreach ($p->recetas as $p_receta) {
                                        $p_receta_detalle = RecetasDetalle::where('id_receta',$p_receta->id_receta)->get();
                                        foreach ($p_receta_detalle as $p_insumo_receta) {
                                            /*
                                            $_insumo = Insumos::findOrFail($p_insumo_receta["id_insumo"]);
                                            $insumo = InsumosSucursal::where('id_insumo',$p_insumo_receta["id_insumo"])
                                                    ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                                    ->first();*/
                                            if($p_insumo_receta["id_insumo"] != null){
                                                $_insumo = Insumos::findOrFail($p_insumo_receta["id_insumo"]);
                                                $insumo = InsumosSucursal::where('id_insumo',$p_insumo_receta["id_insumo"])
                                                                        ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                                                        ->first();
                                            }else{
                                                $insumo_producto = 0;
                                                $_insumo = Productos::findOrFail($p_insumo_receta["id_producto"]);
                                                $insumo = ProductosSucursal::where('id_producto',$p_insumo_receta["id_producto"])
                                                                            ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                                                            ->first();
                                            }


                                            $cantidad = $p_insumo_receta["cantidad"] * $prod["cantidad"];
                                            $cant =  ($insumo->stock + $cantidad);

                                            $act_p = $insumo->stock;

                                            $mov = AlmacenMovimientos::create([
                                                'id_sucursal' => $usuario_logeado->id_sucursal,
                                                'id_usuario'  => $usuario_logeado->id,
                                                'id_tipo_movimiento'  => 5,
                                                'id_insumo' =>  $insumo["id_insumo"],
                                                'id_comprobante' => $comprobante->id_comprobante,
                                                'tipo_producto_insumo' => 1,
                                                'nombre_producto_insumo' => $_insumo->nombre_insumo,
                                                'cantidad' => $cantidad,
                                                'precio_unitario' =>  $insumo->precio,
                                                'precio_total' => $insumo->precio * $cantidad,
                                                'stock_actual' => $act_p,
                                                'fecha_movimiento' => date('Y-m-d'),
                                            ]);

                                            $insumo->stock = $cant;
                                            $insumo->save();
                                        }
                                    }
                                }
                            }
                        }


                    //Si es Producto

                    }else if($product->tipo_producto_combo==3){

                        $product_sucursal->increment('stock', $det->cantidad);
                        $product_sucursal->save();
                        $act = $product_sucursal->stock;

                        $mov = AlmacenMovimientos::create([
                            'id_sucursal' => $usuario_logeado->id_sucursal,
                            'id_usuario'  => $usuario_logeado->id,
                            'id_tipo_movimiento'  => 5,
                            'id_producto' =>  $det->id_producto,
                            'id_comprobante' => $id,
                            'tipo_producto_insumo' => 0,
                            'nombre_producto_insumo' => $product->nombre_producto,
                            'cantidad' => $det->cantidad,
                            'precio_unitario' =>  $product_sucursal->precio,
                            'precio_total' => $product_sucursal->precio * $det->cantidad,
                            'stock_actual' => $act,
                            'fecha_movimiento' => date('Y-m-d'),
                        ]);

                    //Si es Plato
                    }else{

                        if($product->recetas){
                            foreach ($product->recetas as $receta) {
                                $receta_detalle = RecetasDetalle::where('id_receta',$receta->id_receta)->get();
                                $insumo_producto = 1;
                                foreach ($receta_detalle as $insumo_receta) {
                                    if($insumo_receta["id_insumo"] != null){
                                        $_insumo = Insumos::findOrFail($insumo_receta["id_insumo"]);
                                        $insumo = InsumosSucursal::where('id_insumo',$insumo_receta["id_insumo"])
                                                ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                                ->first();
                                    }else{
                                        $insumo_producto = 0;
                                        $_insumo = Productos::findOrFail($insumo_receta["id_producto"]);
                                        $insumo = ProductosSucursal::where('id_producto',$insumo_receta["id_producto"])
                                            ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                            ->first();
                                    }
                                    /*
                                    $_insumo = Insumos::findOrFail($insumo_receta["id_insumo"]);
                                    $insumo = InsumosSucursal::where('id_insumo',$insumo_receta["id_insumo"])
                                            ->where('id_sucursal',$usuario_logeado->id_sucursal)
                                            ->first();*/
                                    $cantidad = $insumo_receta["cantidad"] * $det->cantidad;
                                    $cant =  ($insumo->stock + $cantidad);
                                    //var_dump($request->cantidad);

                                    $act_p = $insumo->stock;
                                    $mov = AlmacenMovimientos::create([
                                        'id_sucursal' => $usuario_logeado->id_sucursal,
                                        'id_usuario'  => $usuario_logeado->id,
                                        'id_tipo_movimiento'  => 5,
                                        'id_insumo' => ($insumo["id_insumo"] != null) ? $insumo["id_insumo"] : $insumo['id_producto'],
                                        'id_comprobante' => $comprobante->id_comprobante,
                                        'tipo_producto_insumo' => $insumo_producto,
                                        'nombre_producto_insumo' => ($insumo["id_insumo"] != null) ? $_insumo->nombre_insumo : $_insumo->nombre_producto,
                                        'cantidad' => $cantidad,
                                        'precio_unitario' => $insumo->precio,
                                        'precio_total' => $insumo->precio * $cantidad,
                                        'stock_actual' => $act_p, //$act_p + $cantidad;
                                        'fecha_movimiento' => date('Y-m-d'),
                                    ]);
                                    $insumo->stock = $cant;
                                    $insumo->save();
                                }
                            }
                        }
                    }
                    
                }
            }

            /** FIN Devolución stock e insumos */
            $comprobante->update($upd_data);

            $token = $comprobante->sucursal['token_facturador'];
            $ruta = $comprobante->sucursal['facturador'];
            $flag = 0;

            $fecha_de_emision_de_documentos = date("Y-m-d", strtotime($comprobante->fecha_emision));
            $respuesta2 = '';
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
                $respuesta2  = curl_exec($ch);
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

            return response()->json(['message' =>'Comprobante anulado.','respuesta'=>$respuesta2], 200);


        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function updateComprobante(){
        //
    }
}
