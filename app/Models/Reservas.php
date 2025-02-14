<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservas extends Model
{
    use HasFactory;
    protected $table = "prc_reservas";
    protected $primaryKey = "id_reservas";
    protected $fillable = [
        'id_cliente',
        'estado',
        'fecha_alojamiento',
        'fecha_salida_estimado',
        'fecha_salida',
        'dias_alojamiento',
        'costo_total',
        'mora',
        'adelanto',
        'descuento'
    ];
    
    protected $with = array( 'cliente','habitaciones','detail'); 

    public function habitaciones()
    {
        return $this->hasMany(ReservaHabitaciones::class, 'id_reservas', 'id_reservas');
    }
    public function cliente()
    {
        return $this->hasOne(Clientes::class, 'id_cliente', 'id_cliente');
    }

    public function detail(){
        return $this->hasMany(ReservaDetalle::class,'id_reservas');
    }
}
