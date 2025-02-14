<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlmacenMovimientos extends Model
{
    use HasFactory;
    protected $table= 'prc_almacen_movimientos';
    protected $primaryKey= 'id_almacen_movimientos';
    protected $fillable = [
        'id_sucursal', 
        'id_usuario', 
        'id_tipo_movimiento', 
        'nro_guia_remision', 
        'id_producto',
        'id_insumo',
        'tipo_producto_insumo',
        'nombre_producto_insumo', 
        'cantidad',
        'precio_unitario',
        'precio_total',
        'stock_actual',
        'fecha_movimiento',
    ];
    public $timestamps = true;

    
    
    protected $with = array('usuario', 'sucursal', 'tipo_movimiento');

    public function usuario(){
        return $this->belongsTo('App\Models\User', 'id_usuario');
    }

    public function sucursal(){
        return $this->belongsTo('App\Models\Sucursales', 'id_sucursal');
    }

    public function tipo_movimiento(){
        return $this->belongsTo('App\Models\TipoMovimiento', 'id_tipo_movimiento');
    }
    
}
