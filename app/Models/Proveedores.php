<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedores extends Model
{
    use HasFactory;
    protected $table= 'mst_proveedores';
    protected $primaryKey= 'id_proveedor';
    protected $fillable = [
        'id_sucursal',
        'id_tipo_doc',
        'nombre',
        'nro_doc',
        'direccion',
        'email',
        'telefono',
        'estado',
        'activo'
    ];
    public $timestamps = true;

    protected $with = array('sucursal', 'tipo_doc');

    public function sucursal(){
        return $this->belongsTo('App\Models\Sucursales', 'id_sucursal');
    }

    public function tipo_doc(){
        return $this->belongsTo('App\Models\TipoDocumentos', 'id_tipo_doc');
    }

    public static function normalizeRequest($request){
        
        $usuario_logeado = auth('sanctum')->user();
        // normalize password
        $request->merge(['id_sucursal'=>$usuario_logeado->id_sucursal ]);
        return $request;
    }

    public function getData(){
        $data = [];
        $data['id_proveedor'] = $this->id_proveedor;
        $data['id_sucursal'] = $this->id_sucursal;
        $data['id_tipo_doc'] = $this->id_tipo_doc;
        $data['nombre'] = $this->nombre;
        $data['nro_doc'] = $this->nro_doc;
        $data['direccion'] = $this->direccion;
        $data['email'] = $this->email;
        $data['telefono'] = $this->telefono;
        $data['tipo_doc'] = $this->tipo_doc;
        return $data;
    }

    public static function allowedToSave($dni){
        $proveedor =  Proveedores::where('nro_doc', $dni)->first();
        if (isset($proveedor) && $proveedor->estado == 1) {
            return 
            [
                'status' => false,
                "message" => "Proveedor ya creado" ,
                "flag" => 1
            ];
        }else if(isset($proveedor) && $proveedor->estado == 0) {
            
            $proveedor =  Proveedores::where('nro_doc', $dni)->first();
            $proveedor->estado = 1;
            $proveedor->save();
            return 
            [
                'status' => false,
                "message" => "Proveedor activado",
                "customer" => $proveedor->id_proveedor,
                "flag" => 2
            ];
        }
        return ['status' => true, "message" => "Ok"];


    }
}
