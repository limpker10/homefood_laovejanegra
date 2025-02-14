<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sucursales;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use DB;

class UserController extends Controller
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
        try{
            User::allowedToSave($request->access_code);
            $password = Hash::make($request->password);
            $user = User::create($request->all());
            $user->password = $password;
            $user->save();
            $user->syncRoles($request->input('role'));
            return response()->json($user, 200);
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
            $user = User::findOrFail($id);
            $user->update($request->all());
            if($request->password){
                $password = Hash::make($request->password);
                $user->password = $password;
            }
            $user->save();
            $user->syncRoles($request->input('role'));
            return response()->json($user, 200);
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
        try {
            $user = User::findOrFail($id);
            $user->remove();
            return response()->json(['message' => 'The user has been deleted'], 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getUsuarios(Request $request){
        $datos = User::with('roles')->active();        
        if(!empty($request->data)){
            $datos = $datos->where(function($query) use ($request){
                $query->where("name", "LIKE", "%".$request->data."%")
                    ->orWhere("email", "LIKE", "%".$request->data."%");
            });
            //de esta forma filtra sin tener encuenta las condiciones anteriores de active() 
            //y si coincide el email lo reconoce en la busqueda igualmente asi estado = 0
            //$datos = $datos->where("name", "LIKE", "%".$request->data."%")->orWhere("email", "LIKE", "%".$request->data."%");
        }

        return $datos->paginate($request->perpage);
    }

    public function getMozos(Request $request){
        $mozos = DB::table('users')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.email')
            ->where("model_has_roles.role_id", "3")
            ->orWhere("model_has_roles.role_id", "1")
            ->get();

        return $mozos;
    }
    
    public function validatePasswordAccess(Request $request){
        try {
            $user = User::findOrFail(1);

            if($user->matchPassword($request['password'])){
                return response()->json([
                    'response' => true,
                    'message' => 'Contraseña válida'
                ], 200);
            }
            else{
                return response()->json([
                    'response' => false,
                    'message' => 'Contraseña inválida'
                ], 200);
            }
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }  
    }

    public function validateCodeAccess(Request $request){
        try {
            //$user = User::where('id', $request['id'])->first();
            /*$user = DB::table('users')
                    ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                    ->select('users.id', 'users.name', 'users.email')
                    ->where("users.id",$request['id'])
                    ->where("access_code",$request['code'])
                    ->where("model_has_roles.role_id", "3")
                    ->orWhere("model_has_roles.role_id", "1")
                    ->first();
            

            if($user &&  $request['code'] != "0000"){
                return response()->json([
                    'response' => true,
                    'data' => $user,
                    'message' => 'Código válido'
                ], 200);
            }
            else{
                return response()->json([
                    'response' => false,
                    'message' => 'Código inválido'
                ], 200);
            }*/
            //$user = User::where('access_code', $request['code'])->first();
            $usuario_logeado = auth('sanctum')->user();
            //dd($usuario_logeado);
            if($usuario_logeado->access_code == $request['code']){
                return response()->json([
                    'response' => true,
                    'data' => $usuario_logeado,
                    'message' => 'Código válido'
                ], 200);
            }
            else{
                return response()->json([
                    'response' => false,
                    'message' => 'Código inválido'
                ], 200);
            }
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }  
    }
}
