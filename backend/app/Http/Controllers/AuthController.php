<?php

namespace App\Http\Controllers;

use App\Enums\UserTypes;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\AuthService;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Constructor to inject AuthService dependency.
     *
     * @param AuthService $authService The service for authentication-related operations.
     */
    public function __construct(public AuthService $authService)
    {
    }

    /**
     * Logs in a user.
     *
     * @param LoginRequest $request The request containing login credentials.
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);

        $userType = UserTypes::client->value;
        if (request()->routeIs('admin.login'))
        {
            $userType = UserTypes::admin->value;
        }

        $result = $this->authService->login($credentials, $remember, $userType);

        if (!$result)
        {
            return response()->json(['message' => __('auth.invalid_credentials')], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json(['user' => $result['user'], 'token' => $result['token']]);
    }

    /**
     * Registers a new user.
     *
     * @param RegisterRequest $request The request containing user registration data.
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $result = $this->authService->register($data, UserTypes::client->value);

        return response()->json(['user' => $result['user'], 'token' => $result['token']], Response::HTTP_CREATED);
    }

    /**
     * Logs out the current user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->authService->logout();

        return response()->json(['success' => true], Response::HTTP_OK);
    }

    /**
     * Sends a password reset link to the user's email.
     *
     * @param ForgetPasswordRequest $request The request containing the user's email.
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $status = $this->authService->sendResetLink($request->only('email'), $request->resetURL);

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

        $status = $this->authService->resetPassword($credentials);

        return $status === Password::PASSWORD_RESET
            ? response()->json(['success' => true, 'message' => __($status)], Response::HTTP_OK)
            : response()->json(['success' => false, 'message' => __($status)], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Fetches the authenticated user's details.
     *
     * @return UserResource
     */
    public function getUser()
    {
        return new UserResource(Auth::user());
    }
}
