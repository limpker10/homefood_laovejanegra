<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    use HasFactory;
    protected $table= 'mst_producto';
    protected $primaryKey= 'id_producto';
    protected $fillable = [
        'id_categoria',
        'stock',
        'precio',
        'precio_compra',
        'id_unidad_medida',
        'nombre_producto',
        'codigo_producto',
        'stock_minimo',
        'estado',
        'activo',
        'tipo_producto_combo',
        'cocina',
        'imprimir_cocina',
        'imprimir_barra'
    ];
    public $timestamps = true;

    protected $with = array('unidad_medida', 'categoria', 'imagen_producto','recetas','sucursales');




    public function getData()
    {
        $data = [];
        $data['id_producto'] = $this->id_producto;
        $data['id_categoria'] = $this->id_categoria;
        $data['nombre_producto'] = $this->nombre_producto;
        return $data;
    }


    public function categoria(){
        return $this->belongsTo('App\Models\Categorias', 'id_categoria');
    }

    public function unidad_medida(){
        return $this->belongsTo('App\Models\UnidadesMedida', 'id_unidad_medida');
    }

    public function imagen_producto(){
        return $this->belongsTo('App\Models\FileEntry', 'id_producto', 'id_producto');
    }

    public function recetas(){
        return $this->hasMany('App\Models\RecetasProducto', 'id_producto');
    }
    public function sucursales(){
        return $this->hasMany('App\Models\ProductosSucursal', 'id_producto');
    }

    public function scopeGroupId($q, $ids)
    {
        //scope users where IN group ids
        return $q->whereIn('id_producto', $ids);
    }

    public function disable($request)
    {
        try{
            $this->update(['activo'=> $request->activo]);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage(), 500);
        }
    }

    public function scopeDisable($q, $request){
        try{
            //scope update users status
            $q->update(['activo'=> $request->activo]);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage(), 500);
        }
    }

}
