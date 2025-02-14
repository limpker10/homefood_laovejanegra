<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComboProducto extends Model
{
    use HasFactory;
    protected $table= 'mst_combo_detalle';
    protected $primaryKey= 'id_combo_detalle';
    protected $fillable = [
        'id_combo',
        'id_adicional',
        'id_producto',
        'cantidad',
    ];
    public $timestamps = false;
    protected $with = array('producto', 'adicional');

    public function producto(){
        return $this->belongsTo('App\Models\Productos', 'id_producto');
    }

    public function adicional(){
        return $this->belongsTo('App\Models\Adicionales', 'id_adicional', 'id_adicional');
    }
}
