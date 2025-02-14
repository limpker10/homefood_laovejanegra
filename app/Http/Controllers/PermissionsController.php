<?php

namespace App\Http\Controllers;

use App\Models\CustomPermission;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\Models\CustomRole;

class PermissionsController extends Controller
{

    function __construct(){
        //$this->middleware('auth:sanctum');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $permissions = CustomPermission::get();
            return  response()->json($permissions,200);
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
            CustomPermission::allowedToSave($request->name);
            $permission = CustomPermission::create(
                ['name' => $request->name,
                'description' => $request->description,
                'guard_name'=>'web']);
            return  response()->json($permission,201);
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
            $rolePermissions = $role->permissions;
            return  response()->json($rolePermissions,200);
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
            $rolePermissions = CustomPermission::findOrFail($id);
            $rolePermissions->update($request->all());
            return response()->json($rolePermissions,200);
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
            $rolePermissions = CustomPermission::findOrFail($id);
            $rolePermissions->delete($id);
            return response()->json("Permiso eliminmado correctamente",200);
        }
        catch(\Exception $e){
            return response()->json($e->getMessage(),500);
        }
    }
    


}
