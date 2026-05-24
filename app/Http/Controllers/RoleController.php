<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = Role::create(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'message' => 'Rol creado exitosamente',
            'role' => $role
        ]);
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'administrador') {
            return response()->json(['message' => 'No se puede modificar el rol administrador'], 403);
        }

        $validator = Validator::make($request->all(), [
            'permissions' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role->syncPermissions($request->permissions);

        return response()->json([
            'message' => 'Permisos actualizados exitosamente'
        ]);
    }

    public function getPermissions(Role $role)
    {
        return response()->json([
            'permissions' => $role->permissions->pluck('name')
        ]);
    }
}
