<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recetas extends Model
{
    use HasFactory;
    protected $table= 'mst_receta';
    protected $primaryKey= 'id_receta';
    protected $fillable = [
        'nombre_receta',
        'estado'
    ];
    public $timestamps = true;

    public function scopeActive($q)
    {
        //scope users where active 1
        return $q->where('estado', 1);
    } 
    
    public function remove()
    {   
        try{
            $this->update(['estado'=>0]);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage(), 500);
        }  
    }
}
