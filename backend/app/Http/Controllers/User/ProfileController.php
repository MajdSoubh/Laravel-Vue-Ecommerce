<?php

namespace App\Http\Controllers\User;

use App\Events\Notify;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Profile\UpdateDetailsRequest;
use App\Http\Requests\User\Profile\UpdatePasswordRequest;
use App\Models\Country;
use Illuminate\Support\Facades\Hash;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $data = $request->validated();

        /** @var App\Models\User $user */
        $user = auth()->user();

        if (!Hash::check($data['old_password'], $user->password))
        {
            $errors = new stdClass;
            $errors->old_password = ['Invalid current password'];

            return response()->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->password = $data['new_password'];
        $user->update();

        Notify::dispatch("The password has been updated", 'success');
    }

    public function getCountries()
    {
        $countries = Country::get();
        return response()->json(['countries' => $countries], Response::HTTP_OK);
    }
    public function updateDetails(UpdateDetailsRequest $request)
    {
        /** @var App\Models\User $user */
        $user = auth()->user();

        $data = $request->validated();

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];
        $user->details()->updateOrCreate([
            'address_1' => $data['address_1'],
            // 'address_2' => $data['address_2'],
            'city' => $data['city'],
            'state' => $data['state'],
            'zipcode' => $data['zip_code'],
            'country_code' => $data['country'],
        ]);
        $user->update();

        Notify::dispatch("User details has been updated", 'success');
    }
    public function getDetails()
    {
        /** @var App\Models\User $user */
        $user = auth()->user();

        $data = $user->details;
        $data['name'] = $user->name;
        $data['email'] = $user->email;
        $data['phone'] = $user->phone;

        return response()->json($data, Response::HTTP_OK);
    }
}
