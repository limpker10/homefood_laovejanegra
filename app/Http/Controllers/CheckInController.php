<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Reservas;
use App\Models\ReservaDetalle;
use App\Models\ReservaHabitaciones;
use App\Models\Habitaciones;

class CheckInController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $booking = Reservas::create($request->all());
            $id_reservas = $booking->id_reservas;
            foreach ($request->habitaciones as $habitacion) {
                //$habitacion["id_reservas"] = $id_reservas;
                //ReservaHabitaciones::create(collect($habitacion));
                ReservaHabitaciones::create([
                    "id_habitacion" => $habitacion['id_habitacion'],
                    "id_reservas" => $id_reservas,
                    'id_categoria' => $request->categoria["id_categoria"],
                    'precio' => $request->categoria["precio"]
                ]);
                $room = Habitaciones::find($habitacion['id_habitacion']);
                $room->costo = $request->categoria["precio"];
                $room->id_reservas = $id_reservas;
                $room->estado = 1;                
                $room->save();
            };

            return response()->json(['message' => 'CheckIn created successfully', 'booking' => $id_reservas], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        } 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function booking(Request $request, $id)
    {
        try {
            $booking = Reservas::findOrFail($id);
            $booking->estado = 1;
            $booking->dias_alojamiento = $request->dias_alojamiento;
            $booking->fecha_alojamiento = $request->fecha_alojamiento;
            $booking->fecha_salida_estimado = $request->fecha_salida_estimado;  
            $booking->costo_total = $request->costo_total;
            $booking->adelanto = $request->adelanto;
            $booking->descuento = $request->descuento;
            $booking->save();
            $id_reservas = $booking->id_reservas;
            foreach ($request->habitaciones as $habitacion) {
                $room = Habitaciones::find($habitacion['id_habitacion']);
                $room->id_reservas = $id_reservas;
                $room->costo = $request->categoria["precio"];
                $room->estado = 1;                
                $room->save();
            };
            return response()->json(['message' => 'Check in booking successfully', 'booking' => $booking], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function checkout(Request $request, $id)
    {
        try {
            $booking = Reservas::findOrFail( $id);
            $booking->estado = 2;
            $booking->save();
            $id_reservas = $booking->id_reservas;
            foreach ($request->habitaciones as $habitacion) {
                $room = Habitaciones::find($habitacion['id_habitacion']);
                $room->id_reservas = $id_reservas;
                $room->estado = 2;                
                $room->save();
            };
            ReservaDetalle::where('id_reservas', $id)
            ->update(['pago' => 1]);
            return response()->json(['message' => 'Check out booking successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
