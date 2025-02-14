<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsumosSucursal extends Model
{
    use HasFactory;

    protected $table = 'mst_insumo_sucursal';
    protected $primaryKey = 'id_insumo_sucursal';
    protected $fillable = [
        'id_insumo',
        'id_sucursal',
        'stock',
        'estado'
    ];
    
    public $timestamps = false;
    
    protected $with = array('sucursal', 'insumo');

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

    public function sucursal(){
        return $this->belongsTo('App\Models\Sucursales', 'id_sucursal');
    }

    public function insumo(){
        return $this->belongsTo('App\Models\Insumos', 'id_insumo');
    }
}
