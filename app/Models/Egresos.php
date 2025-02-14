<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Egresos extends Model
{
    use HasFactory;
    protected $table= 'prc_egresos';
    protected $primaryKey= 'id_egreso';
    protected $fillable = [
        'fecha_egreso',
        'monto',
        'detalle',
        'id_usuario',
        'id_sucursal',
        'id_motivo_egreso',
        'id_caja',
        'rubro',
        'id_compra',
        'caja_chica',
        'estado'
    ];
    public $timestamps = false;

    protected $with = array('usuario','motivo_egreso');

    public function usuario(){
        return $this->belongsTo('App\Models\User', 'id_usuario');
    }
    public function motivo_egreso(){
        return $this->belongsTo('App\Models\EgresosMotivos', 'id_motivo_egreso');
    }

}
