<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'id_sucursal',
        'id_rol',
        'estado',
        'access_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function remove()
    {   
        try{
            $this->update(['estado'=>0]);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage(), 500);
        }  
    }

    public function scopeActive($q)
    {
        //scope users where active 1
        return $q->where('estado', 1);
    } 

    protected $with = array('sucursal','rol'); 
    
    //relacion con sucursal
    public function sucursal(){
        return $this->belongsTo('App\Models\Sucursales', 'id_sucursal');
    }

    //relacion con rol_usuario
    public function rol(){
        return $this->belongsTo('App\Models\RolUsuario', 'id_rol');
    }

    public function getData()
    {
        $data = [];
        $data['id'] = $this->id;
        $data['name'] = $this->name;
        $data['email'] = $this->email;
        $data['id_sucursal'] = $this->id_sucursal;
        $data['sucursal'] = $this->sucursal;
        $data['roles'] = [];
        $data['permissions'] = [];
        $_roles = $this->roles;
        foreach ($_roles  as $r) {
            array_push($data['roles'], $r->name);
        }
        $_permisions = $this->getAllPermissions();
        foreach ($_permisions  as $p) {
            array_push($data['permissions'], $p->name);
        }
        return $data;
    }

    public static function allowedToSave($code)
    {
        $user =  User::where('access_code', '=', $code)->first();
        if (isset($user)) {
            throw new \Exception("Code already exist", 422);
        }
    }

    public function matchPassword($password)
    {
        return Hash::check($password, $this->password);
    }
    
}
