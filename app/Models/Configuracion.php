<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;
    protected $table = 'cnf_empresa';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'direccion',
        'direccion',
        'telefono',
        'email',
        'logo',
    ];
    
    public $timestamps = false;
}
