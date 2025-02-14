<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaSucursal extends Model
{    
    use HasFactory;

    protected $table = 'mst_categoria_sucursal';
    protected $primaryKey = 'id_categoria_sucursal';
    protected $fillable = [
        'id_categoria',
        'id_sucursal',
        'color'
    ];
    
    public $timestamps = false;
    
    protected $with = array('sucursal','categoria');

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

    public function categoria(){
        return $this->belongsTo('App\Models\Categorias', 'id_categoria');
    }
}
