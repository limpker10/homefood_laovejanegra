<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedioPago extends Model
{
    use HasFactory;
    protected $table = 'mst_medio_pago';
    protected $primaryKey = 'id_medio_pago';
    protected $fillable = [
        'medio_pago'
    ];
    
    public $timestamps = false;

}
