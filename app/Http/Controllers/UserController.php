<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request): JsonResponse
    {
        $user = User::with('rooms')->find($request->user('api')->id);

        return response()->json(['user' => $user]);
    }

    /**
     * Update the current User
     *
     * @return [json] user object
     */
    public function update(Request $request)
    {
        $user = User::find($request->user('api')->id);

        if ($request->has('first_name')) {
            $user->first_name = $request->get('first_name');
        }
        if ($request->has('last_name')) {
            $user->last_name = $request->get('last_name');
        }
        if ($request->has('username')) {
            $user->username = $request->get('username');
        }
        if ($request->has('email')) {
            if ($user->email != Str::lower($request->get('email'))) { // if email is a new one, update email, mark profile as not verified and send mail confirmation email
                $user->email = Str::lower($request->get('email'));
                $user->email_verified_at = null;
                $user->sendEmailVerificationNotification();
            }
            $user->email = Str::lower($request->get('email'));
        }
        if ($request->has('dob')) {
            $user->dob = $request->get('dob');
        }
        if ($request->has('country_id')) {
            $user->country_id = $request->get('country_id');
        }
        $user->save();

        $user = User::with('country')->find($user->id);

        return response()->json(['user' => $user]);
    }
}
