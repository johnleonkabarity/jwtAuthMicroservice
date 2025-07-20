<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToProvider($provider)
    {
        // El stateless() es CRUCIAL para APIs
        $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
        return response()->json([
            'redirect_url' => $url,
        ]);
    }

    public function handleProviderCallback($provider)
    {
        try {
            // stateless() es CRUCIAL aquí también
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid credentials provided.'], 401);
        }

        // Buscamos o creamos el usuario en nuestra base de datos
        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name' => $socialUser->getName(),
                'password' => null, // Los usuarios sociales no necesitan contraseña local
            ]
        );

        // Emitimos un JWT para este usuario
        $token = Auth::guard('api')->login($user);

        // Devolvemos el JWT, igual que en el login normal
        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}