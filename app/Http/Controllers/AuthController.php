<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

/**
 * reference: https://laravel-jwt-auth.readthedocs.io/en/latest/quick-start/
 */
class AuthController extends Controller
{
    /**
     * Authentication user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $token = $request->authenticate();

        return $this->respondWithToken($token);
    }

    /**
     * Refresh a token.
     */
    public function refresh(): JsonResponse
    {
        /** @var string */
        $token = \Auth::refresh();

        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     */
    public function logout(): void
    {
        \Auth::logout();
    }

    /**
     * Get the authenticated User.
     */
    public function me(): JsonResponse
    {
        /** @var User */
        $user = \Auth::user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            // TODO ADD ROLE AND PERMISSIONS
        ]);
    }

    /**
     * Get the token array structure.
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => \Auth::factory()->getTTL() * 60, // @phpstan-ignore-line
        ]);
    }
}
