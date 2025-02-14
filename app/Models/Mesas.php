<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesas extends Model
{
    use HasFactory;

    protected $table = 'mst_mesas';
    protected $primaryKey = 'id_mesa';
    protected $fillable = [
        'id_zona',
        'nro_mesa',
        'id_sucursal',
        'estado_ocupada',
        'estado'
    ];
    
    public $timestamps = false;
    
    protected $with = array('zona', 'sucursal','orders');

    public function orders(){
        return $this->belongsToMany(Orden::class, 'prc_mesa_orden', 'id_mesa', 'id_orden');
        //->withPivot([ 'id_usuario', 'id_cliente']);
    }
    /*OBJECT FUNCTIONS*/

    public function remove()
    {   
        try{
            $this->update(['estado'=>0]);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage(), 500);
        }  
    }

    public function scopeActive($q)
    {
        //scope users where active 1
        return $q->where('estado', 1);
    } 

    public function zona(){
        return $this->belongsTo('App\Models\Zonas', 'id_zona');
    }

    public function ordenes(){
        return $this->hasMany('App\Models\MesaOrden', 'id_mesa');
    }


    public function ordenes_detalle()
    {
        
        return $this->hasManyThrough(
            'App\Models\OrdenDetalle',
            'App\Models\MesaOrden',
            'id_mesa',
            'id_orden',
            'id_mesa',
            'id_orden');
    
    }

    public function sucursal(){
        return $this->belongsTo('App\Models\Sucursales', 'id_sucursal');
    }
}
