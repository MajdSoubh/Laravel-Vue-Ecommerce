<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserTypes;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Requests\Admin\Auth\RegisterRequest;
use App\Http\Resources\Admin\AdminResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use  App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $remember = $credentials['remember'] ?? false;
        unset($credentials['remember']);

        if (!Auth::attempt($credentials, $remember))
        {
            return response()->json(['message' => 'The credentials are not correct'], 422);
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();
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
            'type' => UserTypes::admin->value,
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


    public function getAdmin()
    {
        return new AdminResource(Auth::user());
    }
}
