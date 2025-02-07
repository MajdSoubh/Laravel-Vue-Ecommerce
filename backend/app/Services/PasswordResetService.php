<?php

namespace App\Services;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

final readonly class PasswordResetService
{

    /**
     * Sends a password reset link to the user's email.
     *
     * @param array $credentials User credentials.
     * @param string $resetURL The base URL for the password reset link.
     * @return string Returns the status of the password reset link sending process.
     */
    public function sendResetLink(array $credentials, string $resetURL)
    {

        $status =  Password::sendResetLink($credentials, function ($user, $token) use ($resetURL)
        {
            Mail::to($user->email)->send(new ResetPasswordMail($token, $resetURL));
        });

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
}
