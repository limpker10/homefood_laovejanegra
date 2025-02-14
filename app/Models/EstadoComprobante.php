<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoComprobante extends Model
{
    use HasFactory;
    
    protected $table= 'mst_estado_comprobante';
    protected $primaryKey= 'id_estado_comprobante';
    protected $fillable = [
        'nombre_estado',
    ];
    public $timestamps = false;
}
