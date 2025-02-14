<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZonasHotel extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "mst_zonas_hotel";
    protected $primaryKey = "id_zona_hotel";
    protected $fillable = [
        'nombre_zona',
        'estado'
    ];
}
