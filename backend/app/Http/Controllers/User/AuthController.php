<?php

namespace App\Http\Controllers\User;

use App\Enums\UserTypes;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\User\UserResource;
use App\Services\AuthService;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService;
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);

        $result =  $this->authService->login($credentials, $remember, UserTypes::client->value);

        if (!$result)
        {
            return response()->json(['message' => 'The credentials are not correct'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        return response()->json(['user' => $result['user'], 'token' => $result['token']]);
    }


    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $result = $this->authService->register($data, UserTypes::client->value);

        return response()->json(['user' => $result['user'], 'token' => $result['token']], Response::HTTP_CREATED);
    }

    public function logout()
    {
        $this->authService->logout();

        return response()->json(['success' => true], Response::HTTP_OK);
    }

    public function forgetPassword(ForgetPasswordRequest $request)
    {

        $status = $this->authService->sendResetLink($request->only('email'), route('user.password.reset'));

        return $status === Password::RESET_LINK_SENT ? response()->json(['success' => true, 'message' => __($status)], Response::HTTP_OK) : response()->json(['success' => false, 'message' => __($status)], Response::HTTP_BAD_REQUEST);
    }

    public function resetPassword(ResetPasswordRequest $request, $token = null)
    {

        $_token = $token ? $token : $request->token;
        $credentials = $request->only([
            "email",
            "password",
            "password_confirmation"
        ]) + ["token" => $_token];

        $status = $this->authService->resetPassword($credentials);

        return $status === Password::PASSWORD_RESET ?  response()->json(['success' => true, 'message' => __($status)], Response::HTTP_OK) : response()->json(['success' => false, 'message' => __($status)], Response::HTTP_BAD_REQUEST);
    }
    public function getUser()
    {
        return new UserResource(Auth::user());
    }
}
