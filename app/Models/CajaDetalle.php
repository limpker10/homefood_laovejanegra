<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CajaDetalle extends Model
{
    use HasFactory;

    protected $table = 'prc_caja_detalle';
    protected $primaryKey = 'id_caja_detalle';
    protected $fillable = [
        'id_caja',
        'id_medio_pago',
        'monto'
    ];
    public $timestamps = false;
    
    protected $with = array('medio_pago');

    public function medio_pago(){
        return $this->belongsTo('App\Models\MedioPago', 'id_medio_pago');
    }
}
