<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsSystemAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Obtenemos el usuario del token JWT
        $user = Auth::guard('api')->user();

        // Verificamos que el usuario esté autenticado y que sea un system admin
        if ($user && $user->is_system_admin) {
            return $next($request);
        }

        // Si no, devolvemos un error de prohibido.
        return response()->json(['error' => 'Forbidden. Administrator access required.'], 403);
    }
}