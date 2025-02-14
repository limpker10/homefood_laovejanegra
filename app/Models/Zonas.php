<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zonas extends Model
{
    use HasFactory;

    protected $table = 'mst_zonas_pos';
    protected $primaryKey = 'id_zona';
    protected $fillable = [
        'id_sucursal',
        'nombre',
        'estado'
    ];
    
    public $timestamps = false;
    
    protected $with = array('sucursal');

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

    public function mesas(){
        return $this->hasMany('App\Models\Mesas');
    }
}

