<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Request;

class VerificationController extends Controller
{
    /**
     * Verify user email.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request, int $id)
    {
        // Fetch the user by ID
        $user = User::find($id);

        // Check if the user exists
        if (!$user)
        {
            return response()->json(['message' => __('verification.user_not_found')], 404);
        }


        if ($user->markEmailAsVerified())
        {
            event(new Verified($user));
        }


        return response()->json([
            'success' => true,
            'message' => __('verification.email_verified')
        ]);
    }

    /**
     * Resend the email verification notification.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerificationEmail()
    {
        $user = request()->user();

        if ($user->hasVerifiedEmail())
        {
            return response()->json([
                'message' => __('verification.already_verified')
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => __('verification.verification_link_resent')
        ], 200);
    }
}
