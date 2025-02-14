<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    use HasFactory;
    protected $table = 'mst_series';
    protected $primaryKey = 'id_serie';
    protected $fillable = [
        'id_sucursal',
        'id_tipo_comprobante',
        'serie'
    ];
    
    public $timestamps = false;
}
