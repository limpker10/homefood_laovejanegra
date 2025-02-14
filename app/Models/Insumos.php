<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insumos extends Model
{
    use HasFactory;
    protected $table = 'mst_insumos';
    protected $primaryKey = 'id_insumo';
    protected $fillable = [
        'id_categoria',
        'stock',
        'precio',
        'nombre_insumo',
        'codigo_insumo',
        'id_unidad_medida',
        'stock_minimo',
        'estado',
        'medida',
        'cantidad_medida',
    ];
    
    public $timestamps = true;

    protected $with = array('unidad_medida', 'categoria');

    public function categoria(){
        return $this->belongsTo('App\Models\Categorias', 'id_categoria');
    }
    
    public function unidad_medida(){
        return $this->belongsTo('App\Models\UnidadesMedida', 'id_unidad_medida');
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
}
