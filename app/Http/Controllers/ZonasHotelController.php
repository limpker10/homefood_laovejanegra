<?php

namespace App\Http\Controllers;

use App\Models\ZonasHotel;
use Illuminate\Http\Request;

class ZonasHotelController extends Controller
{
    public function getZonas(Request $request){
        try{
            $rows = $request->perpage;
            return ZonasHotel::where('estado',1)->paginate($rows);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Room not found'], 404);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            return ZonasHotel::where('estado',1)->get();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Room not found'], 404);
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
        try{
            return ZonasHotel::create($request->all());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Room not found'], 404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ZonasHotel  $zonasHotel
     * @return \Illuminate\Http\Response
     */
    public function show(ZonasHotel $zonasHotel)
    {
        try{
            return $zonasHotel;
        } catch (\Exception $e) {
            return response()->json(['message' => 'Room not found'], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ZonasHotel  $zonasHotel
     * @return \Illuminate\Http\Response
     */
    public function edit(ZonasHotel $zonasHotel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ZonasHotel  $zonasHotel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $zonasHotel =  ZonasHotel::findOrFail($id);
            $zonasHotel->update($request->all());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Room not found'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ZonasHotel  $zonasHotel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $zonasHotel = ZonasHotel::findOrFail($id);
            $upd_data = array(
                'estado'  =>  0
            );
            $zonasHotel->update($upd_data);
            //$zonasHotel->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Room not found'], 404);
        }
    }
}
