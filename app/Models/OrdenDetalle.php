<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenDetalle extends Model
{
    use HasFactory;
    protected $table= 'prc_orden_detalle';
    protected $primaryKey= 'id_detalle_orden';
    protected $fillable = [
        'id_producto', 
        'cantidad', 
        'precio', 
        'contador_comanda', 
        'observaciones', 
        'id_orden',
        'detalle_combo'
    ];
    public $timestamps = false;

    protected $with = array( 'producto');

    public function producto(){
        return $this->belongsTo('App\Models\Productos', 'id_producto');
    }

    public static function normalizeRequest($request,$id){
        $request->merge(['id_orden'=>$id]);
        return $request;
    }

    public function getData()
    {
        $data = [];
        $data['id_detalle_orden'] = $this->id_detalle_orden;
        $data['id_producto'] = $this->id_producto;
        $data['cantidad'] = $this->cantidad;
        $data['precio'] = $this->precio;
        $data['contador_comanda'] = $this->contador_comanda;
        $data['observaciones'] = $this->observaciones;
        $data['producto'] = $this->producto;
        $data['detalle_combo'] = $this->detalle_combo;
        if($this->producto["tipo_producto_combo"]==2){
            $data["combo"] = ComboProducto::where('id_combo',$this->id_producto)->get();
        }
        return $data;
    }

    public function updateField($field, $value)
    {
        $this[$field] = $value;
        $this->save();
    }

}
