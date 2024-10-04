<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;

final readonly class AuthService
{

    public function login(array $credentials, bool $remember = false, string $type): bool | array
    {

        if (!Auth::attempt($credentials, $remember))
        {
            return false;
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->type !== $type)
        {
            Auth::logout();
            return false;
        }

        $token = $user->createToken('main')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function register(array $data, string $type)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'type' => $type,
        ]);

        $token = $user->createToken('main')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

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

    public function sendResetLink(array $credentials, string $route = null)
    {
        $this->buildMailMessage($route);

        $status = Password::sendResetLink($credentials);

        return $status;
    }

    public function resetPassword(array $credentials, bool $logoutAllDevices = true)
    {

        $status = Password::reset($credentials, function (User $user, $password)
        {
            $user->password = Hash::make($password);
            $user->save();
            // if ($request->has("logoutAllDevices") && $request->logoutAllDevices)
            // {
            // }
        });
        return $status;
    }
    protected function buildMailMessage(string $route = null)
    {
        ResetPassword::toMailUsing(function ($notifiable, $token) use ($route)
        {
            $url = $route ? $route . "?$token" : route('password.reset', [$token]);

            return (new MailMessage)->subject(Lang::get('Reset Password Notification'))
                ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
                ->action(Lang::get('Reset Password'), $url)
                ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')]))
                ->line(Lang::get('If you did not request a password reset, no further action is required.'));
        });
    }
}
