<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoHabitacion extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "mst_categorias_habitacion";
    protected $primaryKey = "id_categoria";
    protected $fillable = [
        'nombre_categoria',
        'precio',
        'estado'
    ];

}
