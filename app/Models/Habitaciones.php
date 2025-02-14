<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitaciones extends Model
{
    use HasFactory;
    protected $table = "mst_habitaciones";
    protected $primaryKey = "id_habitacion";
    protected $fillable = [
        'nombre_habitacion',
        'id_zona_hotel',
        'id_categoria',
        'detalle',
        'costo',
        'id_reservas',
        'estado'
    ];
    public $timestamps = false;
    protected $with = array('zone','category');
    public function zone()
    {
        return $this->belongsTo(ZonasHotel::class, 'id_zona_hotel', 'id_zona_hotel');
    }
    public function category()
    {
        return $this->belongsTo(TipoHabitacion::class, 'id_categoria', 'id_categoria');
    }
    public function booking()
    {
        return $this->belongsTo(Reservas::class, 'id_reservas', 'id_reservas');
    }

    public function updateField($field, $value)
    {
        $this[$field] = $value;
        $this->save();
    }
}
