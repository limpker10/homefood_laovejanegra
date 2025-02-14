<?php

namespace App\Http\Controllers;

use App\Models\Reservas;
use App\Models\ReservaHabitaciones;
use App\Models\Habitaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class BookingController extends Controller
{
    private $data = [];
    private function insertArray($data, $rooms, $client)
    {
        array_push($this->data, [
            "id_reservas" => $data->id_reservas,
            "id_cliente" => $data->id_cliente,
            "cliente" => $client,
            "fecha_alojamiento" => $data->fecha_alojamiento,
            "dias_alojamiento" => $data->dias_alojamiento,
            "habitaciones" => $rooms,
            "costo_total" => $data->costo_total,
            "created_at" => $data->created_at,
            "updated_at" => $data->updated_at,
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
        $order = isset($_GET['order']) ? $_GET['order'] : 'created_at';
        $oby = isset($_GET['oby']) ? $_GET['oby'] : 'asc';
        $where = isset($_GET['where']) ? $_GET['where'] : -1;
        $like = isset($_GET['like']) ? $_GET['like'] : '';
        if ($rows === -1) $rows = count(Reservas::all());
        try {
            $bookings = Reservas::orderBy($order, $oby);
            if ($where != -1) $bookings = $bookings->where($where, 'like', "%" . $like . '%')->paginate($rows);
            else $bookings = $bookings->paginate($rows);

            // return $bookings;

            /*foreach ($bookings->items() as $booking) {
                $rooms = $booking->rooms;
                $client = $booking->client;
                $this->insertArray($booking, $rooms, $client);
            }
            return response()->json(['data' => $bookings, 'total' => count(Reservas::all())], 200);*/
            return response()->json( $bookings, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
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
                $room = Habitaciones::find($habitacion['id_habitacion']);
                $room->id_reservas = $id_reservas;
                $room->estado = 3;
                $room->save();
            };
            foreach ($request->habitaciones as $habitacion) {
                $room = ReservaHabitaciones::create([
                    'id_habitacion' => $habitacion['id_habitacion'],
                    'id_reservas' => $id_reservas,
                    'id_categoria' => $request->categoria["id_categoria"],
                    'precio' => $request->categoria["precio"]
                ]);
            };
            return response()->json(['message' => 'Booking created successfully', 'booking' => $booking], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function generatePDF($id){
        $reserva = Reservas::findOrFail($id);
        $user = auth('sanctum')->user();
        $file = $reserva->id_reservas.'.pdf';
        $mpdf = new Mpdf();
        $mpdf->showImageErrors = true;
        $html = view('pdf.reservacion', compact('reserva','user'));
        $html = $html->render();

        $mpdf->WriteHTML($html);
        $mpdf->Output($file,'D');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Reservas  $reservas
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $reservas = Reservas::findOrFail($id);
            $rooms = $reservas->rooms;
            $client = $reservas->client;
            // $this->insertArray($reservas, $rooms, $client);
            // return $this->data;
            return response()->json($reservas, 200);
        } catch (\Exception $e) {
            // return response()->json(['message' => $e->getMessage()], 404);
            return response()->json(['message' => 'Booking not found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Reservas  $reservas
     * @return \Illuminate\Http\Response
     */
    public function edit(Reservas $reservas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reservas  $reservas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reservas $reservas)
    {
        try {
            foreach ($reservas->rooms as $room) {
                $room->id_reservas = null;
                $room->estado = 0;
                $room->save();
            };
            foreach ($request->habitaciones as $habitacion) {
                $room = Habitaciones::find($habitacion['id_habitacion']);
                $room->id_reservas = $reservas->id_reservas;
                $room->estado = 1;
                $room->save();
            };
            $reservas->update($request->all());
            return response()->json(['message' => 'Booking updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Reservas  $reservas
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reservas $reservas)
    {
        try {
            foreach ($reservas->rooms as $room) {
                $room->id_reservas = null;
                $room->estado = 0;
                $room->save();
            };
            $reservas->delete();
            return response()->json(['message' => 'Booking deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->errorInfo[2]], 500);
        }
    }
    public function destroyMany(Request $request)
    {
        try {
            foreach ($request->ids as $id) {
                $booking = Reservas::findOrFail($id);
                foreach ($booking->rooms as $room) {
                    $room->id_reservas = null;
                    $room->estado = 0;
                    $room->save();
                }
            };
            Reservas::destroy($request->ids);
            return response()->json(['message' => 'Bookings deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function bookingsToday(){
        try {
            $today = date('Y-m-d');
            $bookings = Reservas::where('estado',0)
                    ->whereDate('fecha_alojamiento','<=', $today)
                    ->whereDate('fecha_salida_estimado','>=', $today)
                    ->get();
            return response()->json($bookings, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function preLoadData($id){
        try{
            $reserva = Reservas::findOrFail($id);
            $data = [];
            $data['id_cliente'] = $reserva->id_cliente;
            $data['nombre_cliente'] = $reserva->cliente["nombre"];
            $data['nro_documento'] = $reserva->cliente["nro_doc"];
            $data['direccion_cliente'] = $reserva->cliente["direccion"];
            $data['fecha_emision'] = date('Y-m-d H:i:s');
            $data['id_reserva'] = $id;
            return response()->json($data, 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }


}
