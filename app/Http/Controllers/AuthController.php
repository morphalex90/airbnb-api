<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param [string] first_name
     * @param [string] last_name
     * @param [string] username
     * @param [string] email
     * @param [string] password
     * @param [string] password_confirmation
     * @param [string] recaptcha_token
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'first_name'            => 'nullable|string|max:100',
            'last_name'             => 'nullable|string|max:100',
            'username'              => 'nullable|string|max:100|unique:users',
            'email'                 => 'required|string|email|max:100|unique:users',
            'password'              => 'required|string|between:8,255|confirmed',
            'password_confirmation' => 'required|string',
            // 'recaptcha_token'       => 'required|string',
        ]);

        /////// reCAPTCHA
        // $recaptcha = $this->validateRecaptcha($request);
        // if ($recaptcha != true) {
        //     return response()->json($recaptcha, 403);
        // }

        $user = User::create([
            'first_name' => ($request->has('first_name') ? $request->get('first_name') : strstr(Str::lower($request->get('email')), '@', true)),
            'last_name' => ($request->has('last_name') ? $request->get('last_name') : null),
            'username' => ($request->has('username') && $request->get('username') != '' ? $request->get('username') : mt_rand(100000, 999999)),
            'role_id' => 1,
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'registration_ip_address' => $request->ip(),
            'country_id' => null,
        ]);

        // event(new Registered($user));

        return response()->json(['message' => 'Successfully created user!'], 201);
    }

    public function verifyAccount($user_id, Request $request): JsonResponse
    {
        $user = User::with('role')->findOrFail($user_id);

        if (!$request->hasValidSignature()) {
            $user->sendEmailVerificationNotification();
            return response()->json(['message' => 'Email link expired, we sent you another email'], 401);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Your account has already been verified'], 401);
        }

        $user->markEmailAsVerified();
        // event(new Verified($user));

        // send welcome email if record label or metaverse
        // if ($user->role->role == 'record_label' || $user->role->role == 'metaverse') {
        //     Mail::to($user->email)->send(new ConfirmedAccount($user));
        // }

        return response()->json(['message' => 'Account correctly validated'], 200);
    }

    public function sendPasswordReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        return Password::sendResetLink($request->only('email'));
    }

    public function saveNewPassword(Request $request)
    {
        $request->validate([
            'token'     => 'required',
            'email'     => 'required|email',
            'password'  => 'required|between:8,255|confirmed',
            'password_confirmation' => 'required',
        ]);

        return Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->setRememberToken(Str::random(60));
                $user->save();
                // event(new PasswordReset($user));
            }
        );
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] recaptcha_token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'             => 'required|string|email',
            'password'          => 'required|string|between:8,255',
            // 'recaptcha_token'   => 'string|required',
        ]);

        // $recaptcha = $this->validateRecaptcha($request);
        // if ($recaptcha != true) {
        //     return response()->json($recaptcha, 403);
        // }

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Incorrect credentials'], 401);
        }

        $user = User::where('id', Auth::user()->id)->first(); // load user

        // if ($user->email_verified_at == null) { // email is not verified, we send a new link to verify email
        //     $user->sendEmailVerificationNotification();
        //     return response()->json(['message' => 'Account not yet validated, check your email'], 401);
        // }

        if ($user->status == 0) { // user is disabled, cannot access anywhere
            return response()->json(['message' => 'Account disabled, please contact us to have access'], 401);
        }

        // Token based on user role (scope)
        $token = $user->createToken($user->email . '-' . now()); //, [$userRole->role] to re add scope

        $user->login = $user->access = date('Y-m-d H:i:s');
        $user->save();

        // $user->generateCode($request->ip()); // send 2fa code

        return response()->json(['access_token' => $token->accessToken, 'expires_at' => $token->token->expires_at]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [json] message
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
