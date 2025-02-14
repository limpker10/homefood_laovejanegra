<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MesaOrden extends Model
{
    use HasFactory;

    protected $table= 'prc_mesa_orden';
    protected $primaryKey= 'id_mesa_atencion';
    protected $fillable = [
        'id_orden', 
        'id_mesa'
    ];
    public $timestamps = false;

    protected $with = array( 'orden', 'mesa');

    public function orden(){
        return $this->belongsTo('App\Models\Orden', 'id_orden');
    }

    public function mesa(){
        return $this->belongsTo('App\Models\Mesas', 'id_mesa');
    }

    public function getData()
    {
        $data = [];
        $data['id_mesa_atencion'] = $this->id_mesa_atencion;
        $data['id_mesa'] = $this->id_mesa;
        $data['id_orden'] = $this->id_orden;
        $data['orden'] = $this->orden;
        $data['mesa'] = $this->mesa;
        return $data;
    }
}
