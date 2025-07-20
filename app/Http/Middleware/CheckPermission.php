<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        // El System Admin tiene acceso a todo, sin necesidad de verificar.
        if ($user->is_system_admin) {
            return $next($request);
        }

        // Obtenemos los permisos directamente del payload del JWT (¡muy eficiente!)
        $permissions = auth()->payload()->get('permissions');

        if (is_array($permissions) && in_array($permission, $permissions)) {
            return $next($request);
        }

        return response()->json(['error' => 'Forbidden. You do not have the required permission.'], 403);
    }
}