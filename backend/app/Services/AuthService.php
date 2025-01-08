<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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

    /**
     * Sends a password reset link to the user's email.
     *
     * @param array $credentials User credentials.
     * @param string $resetURL The base URL for the password reset link.
     * @return string Returns the status of the password reset link sending process.
     */
    public function sendResetLink(array $credentials, string $resetURL)
    {
        $this->buildMailMessage($resetURL);

        $status = Password::sendResetLink($credentials);

        return $status;
    }

    /**
     * Resets the user's password.
     *
     * @param array $credentials Password reset credentials.
     * @param bool $logoutAllDevices Whether to log the user out from all devices.
     * @return string Returns the status of the password reset process.
     */
    public function resetPassword(array $credentials)
    {
        $status = Password::reset($credentials, function (User $user, $password)
        {
            $user->password = Hash::make($password);
            $user->save();
        });
        return $status;
    }

    /**
     * Builds the email message for the password reset notification.
     *
     * @param string $resetURL The base URL for the password reset link.
     * @return void
     */
    protected function buildMailMessage(string $resetURL)
    {
        ResetPassword::toMailUsing(function ($notifiable, $token) use ($resetURL)
        {
            $url = Str::finish($resetURL, '/') . $token;

            return (new MailMessage)->subject(Lang::get('Reset Password Notification'))
                ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
                ->action(Lang::get('Reset Password'), $url)
                ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')]))
                ->line(Lang::get('If you did not request a password reset, no further action is required.'));
        });
    }
}
