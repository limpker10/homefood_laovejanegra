<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprobanteDetalle extends Model
{
    use HasFactory;
    protected $table= 'prc_comprobante_detalle';
    protected $primaryKey= 'id_comprobante_detalle';
    protected $fillable = [
        'id_comprobante',
        'id_producto', 
        'id_combo',
        'id_unidad_medida',
        'nombre_producto',
        'precio_unitario', 
        'cantidad', 
        'precio_total', 
        'codigo_producto',
        'detalle_combo',
    ];
    public $timestamps = false;


    public static function normalizeRequest($request, $id_comprobante){
        $request['id_comprobante']=$id_comprobante;
        if (array_key_exists('tipo_producto', $request)) {
            if($request['tipo_producto']==2){
                $request['id_combo']=$request['id_producto'];
            }
        }
        return $request;
    }
}
