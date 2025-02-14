<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adicionales extends Model
{
    use HasFactory;
    protected $table = 'mst_adicionales';
    protected $primaryKey = 'id_adicional';
    protected $fillable = [
        'nombre',
        'precio',
        'estado'
    ];
    
    public $timestamps = false;
    

    protected $with = array('detalle');

    public function detalle(){
        return $this->hasMany('App\Models\AdicionalesDetalle', 'id_adicional', 'id_adicional');
    }
}
