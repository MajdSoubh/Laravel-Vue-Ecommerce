<?php

namespace App\Http\Controllers\Admin;

use App\Enums\HttpStatusCode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use  App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UpdateRequest;
use App\Http\Requests\Admin\User\StoreRequest;
use App\Http\Resources\User\UserResource;

class UserController extends Controller
{

    public function index()
    {
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'asc');
        $perPage = request('per_page', '10');
        $users = User::where('name', 'like', "%{$search}%")->orderBy($sortField, $sortDirection)->paginate($perPage);


        return UserResource::collection($users);
    }
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);

        return (new UserResource($user))->additional(['message' => 'User has been created successfully'])->response()->setStatusCode(HttpStatusCode::CREATED->value);
    }
    public function show(string $id)
    {
        $user = User::find($id);

        if (is_null($user))
        {
            return response()->json(['message' => 'No user exists with the provided id'], HttpStatusCode::NOT_FOUND->value);
        }

        return new UserResource($user);
    }

    public function update(UpdateRequest $request, string $id)
    {
        $user = User::find($id);
        if (is_null($user))
        {
            return response()->json(['message' => 'No user exists with the provided id'], HttpStatusCode::NOT_FOUND->value);
        }

        $user->update($request->validated());


        return (new UserResource($user))->additional(['message' => 'User has been updated successfully'])->response()->setStatusCode(HttpStatusCode::OK->value);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        if (is_null($user))
        {
            return response()->json(['message' => 'No user exists with the provided id'], HttpStatusCode::NOT_FOUND->value);
        }

        $user->delete();

        return (new UserResource($user))->additional(['message' => 'User has been deleted successfully'])->response()->setStatusCode(HttpStatusCode::OK->value);
    }


    public function getUser()
    {
        return new UserResource(Auth::user());
    }
}
