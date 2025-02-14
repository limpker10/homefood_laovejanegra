<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use HasFactory;
    protected $table= 'prc_ordenes';
    protected $primaryKey= 'id_orden';
    protected $fillable = [
        'id_usuario', 
        'id_cliente', 
        'hora_entrada', 
        'hora_salida', 
        'numero_comensales',
        'id_sucursal',
        'status'
    ];
    public $timestamps = false;

    protected $with = array( 'detalle', 'cliente', 'sucursal','usuario');

    public function detalle(){
        return $this->hasMany('App\Models\OrdenDetalle', 'id_orden');
    }

    public function cliente(){
        return $this->belongsTo('App\Models\Clientes', 'id_cliente');
    }

    public function sucursal(){
        return $this->belongsTo('App\Models\Sucursales', 'id_sucursal');
    }

    public function usuario(){
        return $this->belongsTo('App\Models\User', 'id_usuario');
    }
    
    public static function normalizeRequest($request){
        $usuario_logeado = auth('sanctum')->user();
        $request->id_cliente = 0;
        $request->merge(['hora_entrada'=>date('Y-m-d H:i:s')]);
        
        if(isset($request->id_mozo) && $request->id_mozo != null){
            $request->merge(['id_usuario'=>$request->id_mozo]);
        }else{
            $request->merge(['id_usuario'=>$usuario_logeado->id]);
        }
        $request->merge(['id_sucursal'=>$usuario_logeado->id_sucursal]);
        return $request;
    }

    public function getData()
    {
        $cliente = Clientes::findOrFail(0);
        $data = [];
        $data['id_orden'] = $this->id_orden;
        $data['id_usuario'] = $this->id_usuario;
        $data['id_cliente'] = $cliente->id_cliente;
        $data['hora_entrada'] = $this->hora_entrada;
        $data['hora_salida'] = $this->hora_salida;
        $data['cliente'] = $cliente;
        $data['detalle'] = $this->detalle;
        return $data;
    }

    public function updateField($field, $value)
    {
        $this[$field] = $value;
        $this->save();
    }
}
