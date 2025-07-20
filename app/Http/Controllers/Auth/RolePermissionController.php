<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RolePermissionController extends Controller
{
    // --- Gestión de Roles y Permisos (Solo Admin) ---

    public function createRole(Request $request)
    {
        $validator = Validator::make($request->all(), ['name' => 'required|string|unique:roles,name']);
        if ($validator->fails()) return response()->json($validator->errors(), 422);

        $role = Role::create(['name' => $request->name, 'description' => $request->description]);
        return response()->json($role, 201);
    }

    public function createPermission(Request $request)
    {
        $validator = Validator::make($request->all(), ['name' => 'required|string|unique:permissions,name']);
        if ($validator->fails()) return response()->json($validator->errors(), 422);

        $permission = Permission::create(['name' => $request->name, 'description' => $request->description]);
        return response()->json($permission, 201);
    }

    // --- Asignaciones (Solo Admin) ---

    public function assignPermissionToRole(Request $request)
    {
        $validator = Validator::make($request->all(), ['role_name' => 'required|string', 'permission_name' => 'required|string']);
        if ($validator->fails()) return response()->json($validator->errors(), 422);

        $role = Role::where('name', $request->role_name)->firstOrFail();
        $permission = Permission::where('name', $request->permission_name)->firstOrFail();

        $role->permissions()->syncWithoutDetaching($permission->id);
        return response()->json(['message' => "Permission '{$permission->name}' assigned to role '{$role->name}'."]);
    }

    public function assignRoleToUser(Request $request)
    {
        $validator = Validator::make($request->all(), ['user_id' => 'required|exists:users,id', 'role_name' => 'required|string']);
        if ($validator->fails()) return response()->json($validator->errors(), 422);

        $user = User::findOrFail($request->user_id);
        $role = Role::where('name', $request->role_name)->firstOrFail();

        $user->roles()->syncWithoutDetaching($role->id);
        return response()->json(['message' => "Role '{$role->name}' assigned to user ID {$user->id}."]);
    }

    public function assignDirectPermissionToUser(Request $request)
    {
        $validator = Validator::make($request->all(), ['user_id' => 'required|exists:users,id', 'permission_name' => 'required|string']);
        if ($validator->fails()) return response()->json($validator->errors(), 422);

        $user = User::findOrFail($request->user_id);
        $permission = Permission::where('name', $request->permission_name)->firstOrFail();

        $user->directPermissions()->syncWithoutDetaching($permission->id);
        return response()->json(['message' => "Direct permission '{$permission->name}' assigned to user ID {$user->id}."]);
    }

    public function assignObjectPermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'permission_name' => 'required|string',
            'object_type' => 'required|string',
            'object_id' => 'required',
        ]);
        if ($validator->fails()) return response()->json($validator->errors(), 422);

        $user = User::findOrFail($request->user_id);
        $permission = Permission::where('name', $request->permission_name)->firstOrFail();

        // Usamos la tabla pivot directamente
        DB::table('object_permission_user')->insert([
            'user_id' => $user->id,
            'permission_id' => $permission->id,
            'object_type' => $request->object_type,
            'object_id' => $request->object_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => "Object permission '{$permission->name}' assigned to user ID {$user->id}."]);
    }

    // --- Revocaciones y Vistas (Solo Admin) ---

    public function revokeAllUserAccess(Request $request)
    {
        $validator = Validator::make($request->all(), ['user_id' => 'required|exists:users,id']);
        if ($validator->fails()) return response()->json($validator->errors(), 422);

        $user = User::findOrFail($request->user_id);
        $user->roles()->detach();
        $user->directPermissions()->detach();

        return response()->json(['message' => "All roles and direct permissions have been revoked from user ID {$user->id}."]);
    }

    public function indexRoles()
    {
        return Role::with('permissions:id,name')->get(['id', 'name']);
    }

    public function indexPermissions()
    {
        return Permission::all(['id', 'name']);
    }

    public function showUserAccess(User $user)
    {
        $user->load(
            'roles:id,name', 
            'directPermissions:id,name', 
            'directObjectPermissions'
        );

        return response()->json($user);
    }
}