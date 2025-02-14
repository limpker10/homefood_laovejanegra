<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoComprobantes extends Model
{
    use HasFactory;

    protected $table= 'mst_tipos_comprobante';
    protected $primaryKey= 'id_tipo_comprobante';
    protected $fillable = [
        'tipo_comprobante', 
        'codigo_sunat'
    ];
    public $timestamps = false;

}
