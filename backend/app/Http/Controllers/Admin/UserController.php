<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Account\UpdateRequest;
use App\Http\Requests\Admin\Account\StoreRequest;
use App\Http\Resources\User\UserResource;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a paginated list of users with optional search, sorting, and pagination.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $search = request('search', '');
        $sortField = request('sort_field', 'created_at');
        $sortDirection = request('sort_direction', 'asc');
        $perPage = request('per_page', '10');

        $users = User::where('name', 'like', "%{$search}%")
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        return UserResource::collection($users);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param \App\Http\Requests\Admin\Account\StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $user = User::create($data);

        return (new UserResource($user))
            ->additional(['message' => __('user.created')])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the details of a specific user by its ID.
     *
     * @param string $id The unique identifier of the user.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if (is_null($user))
        {
            return response()->json(
                ['message' => __('user.not_found')],
                Response::HTTP_NOT_FOUND
            );
        }

        return new UserResource($user);
    }

    /**
     * Update the specified user in storage.
     *
     * @param \App\Http\Requests\Admin\Account\UpdateRequest $request
     * @param string $id The unique identifier of the user.
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, string $id)
    {
        $user = User::find($id);

        if (is_null($user))
        {
            return response()->json(
                ['message' => __('user.not_found')],
                Response::HTTP_NOT_FOUND
            );
        }

        $user->update($request->validated());

        return (new UserResource($user))
            ->additional(['message' => __('user.updated')])
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param string $id The unique identifier of the user.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (is_null($user))
        {
            return response()->json(
                ['message' => __('user.not_found')],
                Response::HTTP_NOT_FOUND
            );
        }

        $user->delete();

        return (new UserResource($user))
            ->additional(['message' => __('user.deleted')])
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Retrieve the currently authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser()
    {
        return new UserResource(Auth::user());
    }
}
