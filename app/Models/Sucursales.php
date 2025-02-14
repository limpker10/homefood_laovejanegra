<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursales extends Model
{
    use HasFactory;
    protected $table= 'mst_sucursales';
    protected $primaryKey= 'id_sucursal';
    protected $fillable = [
        'nombre_sucursal',
        'ruc_sucursal',
        'razon_social_sucursal',
        'cod_domicilio_fiscal',
        'direccion_fiscal',
        'departamento',
        'provincia',
        'distrito',
        'telefono',
        'direccion_comercial',
        'email',
        'direccion_web',
        'nro_cuenta_bancario',
        'cci_bancario',
        'estado',
        'facturador',
        'token_facturador',
        'url_print_cocina',
        'url_print_barra',
        'impresion_automatica'
    ];
    public $timestamps = false;

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

    //relacion con zonas
    public function zonas(){
        return $this->hasMany('App\Models\Zonas', 'id_sucursal')->where('estado',1);;
    }
}
