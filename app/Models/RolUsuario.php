<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolUsuario extends Model
{
    use HasFactory;

    protected $table = 'roles_usuario';
    protected $primaryKey = 'id_rol';
    protected $fillable = [
        'nombre_rol',
    ];
    
    public $timestamps = false;

}
