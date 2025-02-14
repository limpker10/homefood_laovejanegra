<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprasDetalle extends Model
{
    use HasFactory;
    protected $table= 'prc_compras_detalle';
    protected $primaryKey= 'id_compra_detalle';
    protected $fillable = [
        'id_compra', 
        'id_producto',
        'id_insumo',
        'tipo_compra',
        'nombre_producto_insumo', 
        'cantidad', 
        'precio_compra_unitario', 
        'total',
    ];
    public $timestamps = false;

    //protected $with = array('compra');

    public function compra(){
        return $this->belongsTo('App\Models\Compras', 'id_compra');
    }

}
