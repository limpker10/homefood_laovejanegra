<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMovimiento extends Model
{
    use HasFactory;

    protected $table= 'mst_tipo_movimiento_almacen';
    protected $primaryKey= 'id_tipo_movimiento';
    protected $fillable = [
        'tipo_movimiento', 
        'entrada_salida'
    ];
    public $timestamps = false;
}
