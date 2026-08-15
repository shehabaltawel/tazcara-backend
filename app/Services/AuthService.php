<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory;

/**
 * Auth Service
 */
class AuthService
{
    public function __construct(private readonly Factory $auth) {}

    /**
     * Register a new user and issue an access token.
     */
    public function register(array $data): array
    {
        return $this->authenticateResponse(User::create($data));
    }

    /**
     * Authenticate the user with the given credentials and issue an access token.
     *
     * @throws AuthenticationException
     */
    public function login(array $credentials): array
    {
        return $this->authenticateResponse($this->authenticateUser($credentials));
    }

    /**
     * Authenticate the user with the given credentials.
     *
     * @throws AuthenticationException
     */
    private function authenticateUser(array $credentials): User
    {
        throw_if(
            ! $this->auth->guard()->attempt($credentials),
            AuthenticationException::class,
            'Invalid credentials'
        );

        /** @var User $user */
        $user = $this->auth->guard()->user();

        return $user;
    }

    /**
     * Prepare the response data for a successful authentication.
     */
    private function authenticateResponse(User $user): array
    {
        return [
            'access_token' => $this->issueToken($user),
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration') * 60,
            'user' => $user,
        ];
    }

    /**
     * Issue a Sanctum personal access token for the given user.
     */
    private function issueToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }
}
