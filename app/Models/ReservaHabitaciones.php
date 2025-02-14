<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservaHabitaciones extends Model
{
    use HasFactory;
    protected $table= 'prc_reserva_habitaciones';
    protected $primaryKey= 'id_reserva_habitaciones';
    protected $fillable = [
        'id_habitacion',
        'id_reservas',
        'detalle',
        'id_categoria',
        'precio'
    ];
    public $timestamps = false;

    protected $with = array( 'habitaciones', 'categoria'); 

    public function habitaciones()
    {
        return $this->belongsTo(Habitaciones::class, 'id_habitacion', 'id_habitacion');
    }

    public function categoria()
    {
        return $this->belongsTo(TipoHabitacion::class, 'id_categoria', 'id_categoria');
    }
}
