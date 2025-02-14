<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class CustomRole extends Role
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'title',
        'guard_name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /** STACTIC METHODS */
    public static function allowedToSave($name){
        
        $role =  CustomRole::where('name', $name)->first();
        if(isset($role)){
            throw new \Exception("Role already exist",500);
        }
    }

    /** OBJECT METHODS */
}
