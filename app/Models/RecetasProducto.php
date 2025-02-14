<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecetasProducto extends Model
{
    use HasFactory;
    protected $table= 'mst_recetas_producto';
    protected $primaryKey= 'id_recetas_producto';
    protected $fillable = [
        'id_receta',
        'id_producto',
        'estado'
    ];
    public $timestamps = false;

}
