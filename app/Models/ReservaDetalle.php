<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservaDetalle extends Model
{
    use HasFactory;
    protected $table= 'prc_reserva_detalle';
    protected $primaryKey= 'id_reserva_detalle';
    protected $fillable = [
        'id_producto_servicio', 
        'type_model',
        'nombre_producto_servicio',
        'cantidad', 
        'precio_unitario', 
        'precio_total', 
        'observaciones', 
        'pago', 
        'fecha_hora',
        'id_reservas',
        'id_unidad_medida'
    ];
    public $timestamps = false;
}
