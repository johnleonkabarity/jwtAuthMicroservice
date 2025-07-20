<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SocialiteController;
use App\Http\Controllers\Api\RolePermissionController;
use App\Http\Controllers\Api\UserAccessController;

// Rutas de autenticación JWT
Route::prefix('auth')->group(function () {
    // Rutas públicas
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Rutas de login social (Google)
    Route::get('google/redirect', [SocialiteController::class, 'redirectToProvider'])->defaults('provider', 'google');
    Route::get('google/callback', [SocialiteController::class, 'handleProviderCallback'])->defaults('provider', 'google');

    // Rutas protegidas por el guard 'api' (requieren JWT)
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:api')->group(function () {

    // --- Rutas de Administración (Solo para is_system_admin) ---
    Route::prefix('admin/access-control')->middleware('is.admin')->group(function () {
        // Crear
        Route::post('roles', [RolePermissionController::class, 'createRole']);
        Route::post('permissions', [RolePermissionController::class, 'createPermission']);

        // Asignar
        Route::post('assign/permission-to-role', [RolePermissionController::class, 'assignPermissionToRole']);
        Route::post('assign/role-to-user', [RolePermissionController::class, 'assignRoleToUser']);
        Route::post('assign/direct-permission-to-user', [RolePermissionController::class, 'assignDirectPermissionToUser']);
        Route::post('assign/object-permission', [RolePermissionController::class, 'assignObjectPermission']);

        // Revocar
        Route::post('revoke/all-user-access', [RolePermissionController::class, 'revokeAllUserAccess']);

        // Ver
        Route::get('roles', [RolePermissionController::class, 'indexRoles']);
        Route::get('permissions', [RolePermissionController::class, 'indexPermissions']);
        Route::get('users/{user}/access', [RolePermissionController::class, 'showUserAccess']);
    });

    // --- Rutas para Usuarios Normales ---
    Route::prefix('me/access')->group(function () {
        Route::get('/', [UserAccessController::class, 'myAccess']);
        Route::post('check-object-permission', [UserAccessController::class, 'checkObjectPermission']);
    });
});