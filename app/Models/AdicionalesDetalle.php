<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdicionalesDetalle extends Model
{
    use HasFactory;
    protected $table = 'mst_adicionales_detalle';
    protected $primaryKey = 'id_adicional_detalle';
    protected $fillable = [
        'id_adicional',
        'id_producto',
        'cantidad',
        'requerido',
        'precio_adicional',
        'precio'
    ];
    
    public $timestamps = false;
    
    protected $with = array('producto');

    public function producto(){
        return $this->belongsTo('App\Models\Productos', 'id_producto', 'id_producto');
    }
}
