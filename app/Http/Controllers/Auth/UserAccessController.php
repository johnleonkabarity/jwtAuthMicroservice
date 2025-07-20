<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;


class UserAccessController extends Controller
{
    /**
     * Devuelve los roles y permisos del usuario autenticado.
     */
    public function myAccess(Request $request)
    {
        $user = $request->user();
        $user->load('roles:id,name', 'directPermissions:id,name');

        return response()->json([
            'roles' => $user->roles->pluck('name'),
            'permissions' => $user->directPermissions->pluck('name')->merge(
                $user->roles->flatMap->permissions->pluck('name')
            )->unique()->values()
        ]);
    }

    /**
     * Verifica si el usuario autenticado tiene un permiso específico sobre un objeto.
     */
    public function checkObjectPermission(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'permission_name' => 'required|string|exists:permissions,name',
            'object_type' => 'required|string',
            'object_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = $request->user();
        $permission = Permission::where('name', $request->permission_name)->first();

        $hasPermission = DB::table('object_permission_user')
            ->where('user_id', $user->id)
            ->where('permission_id', $permission->id)
            ->where('object_type', $request->object_type)
            ->where('object_id', $request->object_id)
            ->exists();

        return response()->json(['has_permission' => $hasPermission]);
    }
}