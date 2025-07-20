<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    

    /**
     * @OA\Post(
     * path="/api/auth/login",
     * summary="Iniciar sesión de usuario",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(response=200, description="Login exitoso"),
     * @OA\Response(response=401, description="No autorizado")
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Post(
     * path="/api/auth/register",
     * summary="Registrar un nuevo usuario",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name","email","password","password_confirmation"},
     * @OA\Property(property="name", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="password123"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     * )
     * ),
     * @OA\Response(response=201, description="Usuario registrado y logueado"),
     * @OA\Response(response=422, description="Error de validación")
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        $token = Auth::guard('api')->login($user);
        return $this->respondWithToken($token);

 
    }

    /**
     * @OA\Post(
     * path="/api/auth/logout",
     * summary="Cerrar sesión e invalidar JWT",
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Logout exitoso")
     * )
     */
    public function logout()
    {
        Auth::guard('api')->logout(); // Invalida el token (lo añade a la blacklist)
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @OA\Post(
     * path="/api/auth/refresh",
     * summary="Refrescar un JWT expirado",
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Token refrescado")
     * )
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::guard('api')->refresh());
    }

    /**
     * @OA\Get(
     * path="/api/auth/me",
     * summary="Obtener datos del usuario autenticado",
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Datos del usuario")
     * )
     */
    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60, // Duración en segundos
            'user' => Auth::guard('api')->user()
        ]);
    }
}