<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecetasDetalle extends Model
{
    use HasFactory;
    protected $table= 'mst_receta_detalle';
    protected $primaryKey= 'id_receta_detalle';
    protected $fillable = [
        'id_receta',
        'id_insumo',
        'id_producto',
        'cantidad',
        'unidad',
        'estado'
    ];
    public $timestamps = false; 

    protected $with = array('insumo','producto');

    public function insumo(){
        return $this->belongsTo('App\Models\Insumos', 'id_insumo');
    }
    public function producto(){
        return $this->belongsTo('App\Models\Productos', 'id_producto');
    } 
}
