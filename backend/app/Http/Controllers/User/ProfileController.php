<?php

namespace App\Http\Controllers\User;

use App\Events\Notification;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Profile\UpdateDetailsRequest;
use App\Http\Requests\User\Profile\UpdatePasswordRequest;
use App\Models\Country;
use Illuminate\Support\Facades\Hash;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    /**
     * Update the authenticated user's password.
     *
     * @param UpdatePasswordRequest $request The request containing old and new passwords.
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $data = $request->validated();

        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Check if the old password is correct
        if (!Hash::check($data['old_password'], $user->password))
        {
            $errors = new stdClass;
            $errors->old_password = [__('profile.invalid_password')];

            return response()->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Update the user's password
        $user->password = $data['new_password'];
        $user->update();

        // Dispatch a notification
        Notification::dispatch(__('profile.password_updated'), 'success');

        return response()->json(['message' => __('profile.password_updated')], Response::HTTP_OK);
    }

    /**
     * Get a list of all countries.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountries()
    {
        $countries = Country::get();
        return response()->json(['countries' => $countries], Response::HTTP_OK);
    }

    /**
     * Update the authenticated user's details.
     *
     * @param UpdateDetailsRequest $request The request containing updated user details.
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDetails(UpdateDetailsRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $data = $request->validated();

        // Update user details
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];
        $user->details()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'address_1' => $data['address_1'],
                'city' => $data['city'],
                'state' => $data['state'],
                'zipcode' => $data['zip_code'],
                'country_code' => $data['country'],
            ]
        );
        $user->update();

        // Dispatch a notification
        Notification::dispatch(__('profile.details_updated'), 'success');

        return response()->json(['message' => __('profile.details_updated')], Response::HTTP_OK);
    }

    /**
     * Get the authenticated user's details.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetails()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $data = $user->details;
        $data['name'] = $user->name;
        $data['email'] = $user->email;
        $data['phone'] = $user->phone;

        return response()->json($data, Response::HTTP_OK);
    }
}
