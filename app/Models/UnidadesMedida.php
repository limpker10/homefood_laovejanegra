<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadesMedida extends Model
{
    use HasFactory;
    
    protected $table = 'mst_unidades_medida';
    protected $primaryKey = 'id_unidad_medida';
    protected $fillable = [
        'unidad_medida',
        'codigo_sunat',
        'simbolo',
        'estado'
    ];
    
    public $timestamps = false;

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
