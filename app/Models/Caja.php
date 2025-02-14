<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;
    protected $table = 'prc_caja';
    protected $primaryKey = 'id_caja';
    protected $fillable = [
        'fecha_apertura',
        'fecha_cierre',
        'monto_apertura',
        'id_usuario',
        'id_sucursal',
        'rubro',
    ];
    
    public $timestamps = true;
    
    protected $with = array('usuario', 'sucursal');

    public function usuario(){
        return $this->belongsTo('App\Models\User', 'id_usuario');
    }
    public function sucursal(){
        return $this->belongsTo('App\Models\Sucursales', 'id_sucursal');
    }

}
