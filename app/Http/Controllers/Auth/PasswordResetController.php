<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{

    /**
     * Create token password reset
     *
     * @param Request $request
     * @return JsonResponse [string] message
     */
    public function create(Request $request)
    {
        $request->validate(
            [
                'email' => 'required|string|email',
            ]
        );

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            die('no');
            return response()->json(
                [
                    'message' => __('passwords.user')
                ],
                404
            );
        }
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60)
            ]
        );

        if ($user && $passwordReset) {
            //$user->notify(new PasswordResetRequest($passwordReset->token));
        }

        return response()->json(
            [
                'message' => __('passwords.sent')
            ]
        );
    }

    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return JsonResponse [string] message
     */
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();
        if (!$passwordReset) {
            return response()->json(
                [
                    'message' => __('passwords.token')
                ],
                404
            );
        }
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json(
                [
                    'message' => __('passwords.token')
                ],
                404
            );
        }
        return response()->json($passwordReset);
    }

    /**
     * Reset password
     *
     * @param Request $request
     * @return JsonResponse [string] message
     */
    public function reset(Request $request)
    {
        $request->validate(
            [
                'email'    => 'required|string|email',
                'password' => 'required|string|confirmed',
                'token'    => 'required|string'
            ]
        );
        $passwordReset = PasswordReset::where(
            [
                ['token', $request->token],
                ['email', $request->email]
            ]
        )->first();
        if (!$passwordReset) {
            return response()->json(
                [
                    'message' => __('passwords.token')
                ],
                404
            );
        }
        $user = User::where('email', $passwordReset->email)->first();
        if (!$user) {
            return response()->json(
                [
                    'message' => __('passwords.user')
                ],
                404
            );
        }
        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();
        //$user->notify(new PasswordResetSuccess($passwordReset));
        return response()->json($user);
    }

}
