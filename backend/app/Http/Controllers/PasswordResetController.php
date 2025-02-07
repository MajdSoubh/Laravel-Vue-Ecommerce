<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\PasswordResetService;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    /**
     * Constructor to inject PasswordResetService dependency.
     *
     * @param PasswordResetService $passwordResetService The service for password-related operations.
     */
    public function __construct(public PasswordResetService $passwordResetService)
    {
    }

    /**
     * Sends a password reset link to the user's email.
     *
     * @param ForgetPasswordRequest $request The request containing the user's email.
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $status = $this->passwordResetService->sendResetLink($request->only('email'), $request->resetURL);

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['success' => true, 'message' => __($status)], Response::HTTP_OK)
            : response()->json(['success' => false, 'message' => __($status)], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Resets the user's password.
     *
     * @param ResetPasswordRequest $request The request containing the new password.
     * @param string|null $token The password reset token.
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request, $token = null)
    {
        $_token = $token ? $token : $request->token;
        $credentials = $request->only([
            "email",
            "password",
            "password_confirmation"
        ]) + ["token" => $_token];

        $status = $this->passwordResetService->resetPassword($credentials);

        return $status === Password::PASSWORD_RESET
            ? response()->json(['success' => true, 'message' => __($status)], Response::HTTP_OK)
            : response()->json(['success' => false, 'message' => __($status)], Response::HTTP_BAD_REQUEST);
    }
}
