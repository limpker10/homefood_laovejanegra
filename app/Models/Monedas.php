<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monedas extends Model
{
    use HasFactory;

    protected $table = 'mst_monedas';
    protected $primaryKey = 'id_moneda';
    protected $fillable = [
        'moneda',
        'nombre',
        'simbolo'
    ];
    
    public $timestamps = false;
}
