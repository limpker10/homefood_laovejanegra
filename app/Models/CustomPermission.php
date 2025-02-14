<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class CustomPermission extends Permission
{
    use HasFactory;

    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'description',
        'guard_name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /** STACTIC METHODS */
    public static function allowedToSave($name){
        
        $role =  CustomPermission::where('name', $name)->first();
        if(isset($role)){
            throw new \Exception("Permission already exist",500);
        }
    }


    /** OBJECT METHODS */
    
}
