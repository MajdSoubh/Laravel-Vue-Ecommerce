<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

/**
 * AuthService handles authentication-related operations such as login, registration, logout, and password reset.
 */
final readonly class AuthService
{
    /**
     * Logs in a user.
     *
     * @param array $credentials User credentials.
     * @param bool $remember Whether to remember the user's session. Defaults to false.
     * @param string $type The user type to validate (e.g., 'admin', 'user').
     * @return bool|array Returns false if login fails. Returns ['user' => User, 'token' => string] on success.
     */
    public function login(array $credentials, bool $remember = false, string $type): bool | array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || $user->type !== $type || !Hash::check($credentials['password'], $user->password))
        {
            return false;
        }

        $expiration = $remember ? now()->addDays(30) : null;

        $token = $user->createToken('main', ['*'], $expiration)->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Registers a new user.
     *
     * @param array $data User data.
     * @param string $type The user type to assign (e.g., 'admin', 'user').
     * @return array Returns ['user' => User, 'token' => string] on success.
     */
    public function register(array $data, string $type)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'type' => $type,
        ]);

        $token = $user->createToken('main')->plainTextToken;


        event(new Registered($user));

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Logs out the current user.
     *
     * @param string $guard The authentication guard to use..
     * @return bool Returns true if logout is successful, false otherwise.
     */
    public function logout(string $guard = null): bool
    {
        /** @var \App\Models\User $user */
        $user = auth($guard)->user();
        if ($user)
        {
            $user->currentAccessToken()->delete();
            return true;
        }

        return false;
    }
}
