<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Auth Service
 */
class AuthService
{
    /**
     * Register a new user and issue an access token.
     */
    public function register(array $data): array
    {
        return $this->tokenResponse(User::create($data));
    }

    /**
     * Authenticate the user with the given credentials and issue an access token.
     *
     * @throws ValidationException
     */
    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $this->tokenResponse($user);
    }

    /**
     * Prepare the response data for a successful authentication.
     */
    private function tokenResponse(User $user): array
    {
        $token = $user->createToken('auth_token');

        return [
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => $token->accessToken->created_at
                ->addMinutes((int) config('sanctum.expiration'))
                ->diffInSeconds(now()),
            'user' => $user,
        ];
    }
}
