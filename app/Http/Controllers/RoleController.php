<?php

namespace App\Http\Controllers;

use App\Models\CustomRole;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;


class RoleController extends Controller
{
    
    function __construct(){
        //$this->middleware('auth:sanctum');
        /*$this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);
        $this->middleware('permission:role-create', ['only' => ['create','store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);*/
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $roles = CustomRole::get();
            return  response()->json($roles,200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
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
            CustomRole::allowedToSave($request->name);
            $role = CustomRole::create(['name' => $request->name,'title' => $request->title,'description' => $request->description,'guard_name'=>'web']);
            return  response()->json($role,201);
        }
        catch(\Exception $e){
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
        try{
            $role = CustomRole::findOrFail($id);
            return  response()->json($role,200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
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
            $role = CustomRole::findOrFail($id);
            $role->update($request->all());
            return response()->json($role,200);
        }
        catch(\Exception $e){
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
            $role = Role::findOrFail($id);
            $role->delete();
            return response()->json(['message' => 'Rol Eliminado'],201);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }

    
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assignPermissions(Request $request, $id)
    {
        try{
            $role = CustomRole::find($id);
            $role->syncPermissions($request->input('permissions'));
            return response()->json($role,201);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
}
