<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compras extends Model
{
    use HasFactory;
    protected $table= 'prc_compras';
    protected $primaryKey= 'id_compra';
    protected $fillable = [
        'id_sucursal', 
        'id_usuario', 
        'id_proveedor', 
        'nro_guia_remision', 
        'nro_factura',
        'igv',
        'total',
        'subtotal', 
        'nombre_proveedor',
        'nro_doc',
        'direccion_proveedor'
    ];
    public $timestamps = true;

    protected $with = array('usuario', 'sucursal', 'proveedor');

    public function usuario(){
        return $this->belongsTo('App\Models\User', 'id_usuario');
    }

    public function sucursal(){
        return $this->belongsTo('App\Models\Sucursales', 'id_sucursal');
    }

    public function proveedor(){
        return $this->belongsTo('App\Models\Proveedores', 'id_proveedor');
    }
}
