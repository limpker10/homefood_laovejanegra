<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedores;

class ProviderController extends Controller
{
    public function __constructor(){
        $this->middleware('auth:sanctum');
    }
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
        /*try{
            $request= Proveedores::normalizeRequest($request);
            $proveedor = Proveedores::create($request->all());
            return response()->json($proveedor->getData(), 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(),500);
        }*/
        try{
            $allowed = Proveedores::allowedToSave($request->nro_doc);
            if($allowed['status'] == true){
                $request= Proveedores::normalizeRequest($request);
                $proveedor = Proveedores::create($request->all());
                return response()->json(
                    [
                        'allowed' => $allowed,
                        'cliente' => $proveedor->getData()
                    ], 200);
            }
            return response()->json(['allowed' => $allowed], 200);

        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
    public function activateProvider($id)
    {
        try{
            $cliente = Proveedores::findOrFail($id);
            $upd_data = array(
                'estado'  =>  1
            );
            $cliente->update($upd_data);
            return response()->json($cliente, 200);

        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
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
        try{
            $proveedor = Proveedores::findOrFail($id);
            $proveedor->update($request->all());
            return response()->json($proveedor, 200);

        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $proveedor = Proveedores::findOrFail($id);
            $upd_data = array(
                'estado'  =>  0
            );
            $proveedor->update($upd_data);
            return response()->json($proveedor, 200);

        }catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    public function getProveedores(Request $request){
        $usuario_logeado = auth('sanctum')->user();
        $datos = Proveedores::where([
            ["estado", 1],
            ["id_sucursal", $usuario_logeado->id_sucursal],
        ])->orderBy("id_proveedor","desc");
        
        if(!empty($request->data)){
            $datos = $datos->where("nombre", "LIKE", "%".$request->data."%")->orWhere("email", "LIKE", "%".$request->data."%");
        }

        return $datos->paginate($request->perpage);
    }
}
