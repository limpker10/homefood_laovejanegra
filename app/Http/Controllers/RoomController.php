<?php

namespace App\Http\Controllers;

use App\Models\Reservas;
use App\Models\Habitaciones;
use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RoomController extends Controller
{
    public function index()
    {
        $rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;
        $order = isset($_GET['order']) ? $_GET['order'] : 'nombre_habitacion';
        $oby = isset($_GET['oby']) ? $_GET['oby'] : 'asc';

        $where = isset($_GET['where']) ? $_GET['where'] : -1;
        $like = isset($_GET['like']) ? $_GET['like'] : '';

        if ($rows === -1) $rows = count(Habitaciones::all());

        try {
            //$justOrder = Habitaciones::with("booking", "booking.cliente")->orderBy($order, $oby);
            $justOrder = Habitaciones::orderBy($order, $oby);
           
            if ($where != -1) {
                if ($like == -1) return $justOrder->whereNull($where)->paginate($rows);
                else if ($like === 'filled') return $justOrder->whereNotNull($where)->paginate($rows);
                return $justOrder->where($where, 'like', "%" . $like . '%')->paginate($rows);
            }
            return $justOrder->paginate($rows);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function store(Request $request)
    {
        try {
            $room = Habitaciones::create($request->all());
            return response()->json(['message' => 'Room created successfully', 'room' => $room], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function show($id)
    {
        try {
            $room = Habitaciones::findOrFail($id);
            return response()->json($room, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Room not found'], 404);
        }
    }
    public function update(Request $request, Habitaciones $room)
    {
        try {
            $room->update($request->all());
            return response()->json(['message' => 'Room updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function destroy(Habitaciones $room)
    {
        try {
            $room->delete();
            return response()->json(['message' => 'Room deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->errorInfo[2]], 500);
        }
    }
    public function destroyMany(Request $request)
    {
        try {
            Habitaciones::destroy($request->ids);
            return response()->json(['message' => 'Rooms deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function availableRooms(Request $request)
    {
        $rooms = Habitaciones::all();
        $rows = isset($_GET['rows']) ? intval($_GET['rows']) : 10;

        $from = isset($_GET['from']) ? date('Y-m-d', strtotime($_GET['from'])) : date('Y-m-d');
        $to = isset($_GET['to']) ? date('Y-m-d', strtotime($_GET['to'])) : -1;

        if ($rows === -1) $rows = count($rooms);
        $data = [];
        $errors = [];
        try {
            foreach ($rooms as $room) {
                $booking = $room->booking;
                if (is_null($booking)) array_push($data, $room);
                else {
                    try {
                        $booking_date = $booking->fecha_alojamiento;
                        $booking_days = $booking->dias_alojamiento;

                        if (!is_null($booking_date) && !is_null($booking_days)) {
                            $book_start = date('Y-m-d', strtotime($booking_date));
                            $book_end = date('Y-m-d', strtotime("+" . $booking_days . "days", strtotime($booking_date)));

                            $now = date('Y-m-d', strtotime(date('Y-m-d')));
                            if ($to != -1) {
                                if ($to > $from) {
                                    if (
                                        $now <= $from &
                                        $now < $to &
                                        ($from < $book_start & $to < $book_start) |
                                        ($from > $book_end & $to > $book_end)
                                    ) {
                                        array_push($data, $room);
                                    }
                                }
                            } else {
                                if (
                                    $now <= $from &&
                                    ($from < $book_start ||
                                        $from > $book_end)
                                ) {
                                    // dd(-1);
                                    array_push($data, $room);
                                }
                            }
                        } else  array_push($errors, "Id " . $booking->id_reservas . ": Error with dates and days");
                    } catch (\Exception $e) {
                        array_push($errors, "Id " . $booking->id_reservas . ": " . $e->getMessage());
                    }
                }
            };

            $page = LengthAwarePaginator::resolveCurrentPage();

            $data = Collection::make($data);
            $data = new LengthAwarePaginator($data->slice(($page - 1) * $rows, $rows)->values(), $data->count(), $rows);

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateField(Request $request, $id){
        try{
            $room = Habitaciones::findOrFail($id);
            $room->updateField($request->field,$request->value);
            return response()->json(['message' => "success"], 200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }   
    }

    public function active(){
        try {
            $usuario_logeado = auth('sanctum')->user();
            $habitaciones = Habitaciones::with('booking')->where('estado',1)->get();
            return response()->json( $habitaciones, 200);
        } 
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
        
    }
}
