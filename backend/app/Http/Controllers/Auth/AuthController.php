<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserTypes;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\User\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
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
     * Fetches the authenticated user's details.
     *
     * @return UserResource
     */
    public function getUser()
    {
        return new UserResource(Auth::user());
    }
}
