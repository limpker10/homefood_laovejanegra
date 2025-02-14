<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    use HasFactory;

    protected $table = 'mst_clientes';
    protected $primaryKey = 'id_cliente';
    protected $fillable = [
        'nombre',
        'nro_doc',
        'id_tipo_doc',
        'direccion',
        'email',
        'telefono',
        'estado'
    ];
    
    public $timestamps = false;

    protected $with = array('tipo_doc');

    public function tipo_doc(){
        return $this->belongsTo('App\Models\TipoDocumentos', 'id_tipo_doc');
    }

    /*OBJECT FUNCTIONS*/
    public function getData(){
        $data = [];
        $data['id_cliente'] = $this->id_cliente;
        $data['nombre'] = $this->nombre;
        $data['nro_doc'] = $this->nro_doc;
        $data['id_tipo_doc'] = $this->id_tipo_doc;
        $data['direccion'] = $this->direccion;
        $data['email'] = $this->email;
        $data['telefono'] = $this->telefono;
        $data['estado'] = $this->estado;
        $data['tipo_doc'] = $this->tipo_doc;
        return $data;
    }
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

    public static function allowedToSave($dni) {
        $cliente = Clientes::where('nro_doc', $dni)->first();
        if (!$cliente) {
            return [
                'status' => true,
                "message" => "Ok"
            ];
        }
        if ($cliente->estado == 1) {
            return [
                'status' => false,
                "message" => "Cliente ya creado",
                "flag" => 1
            ];
        }
        $cliente->estado = 1;
        $cliente->save();
        return [
            'status' => false,
            "message" => "Cliente activado",
            "flag" => 2
        ];
    }
    
}
