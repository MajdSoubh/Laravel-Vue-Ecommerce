<?php

namespace App\Http\Controllers\User;

use App\Enums\HttpStatusCode;
use App\Enums\UserTypes;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use  App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);

        if (!Auth::attempt($credentials, $remember))
        {

            return response()->json(['message' => 'The credentials are not correct'], HttpStatusCode::UNPROCESSABLE_CONTENT->value);
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();
        if ($user->type === UserTypes::admin->value)
        {
            Auth::logout();
            return response()->json(['message' => 'The credentials are not correct'], HttpStatusCode::UNPROCESSABLE_CONTENT->value);
        }

        $token = $user->createToken('main')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
    }


    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'type' => UserTypes::client->value,
        ]);

        $token = $user->createToken('main')->plainTextToken;
        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    public function logout()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return response()->json(['success' => true], 200);
    }

    public function getUser()
    {
        return new UserResource(Auth::user());
    }
}
