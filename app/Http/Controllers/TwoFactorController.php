<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Requests\TwoFactor\TwoFactorVerification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function __construct()
    {
        $this->middleware('two_factor_auth:true', ['only' => ['enableTwoFactorAuthenticationForUser']]);
    }

    public function show2faForm()
    {
        return view('2fa');
    }

    public function enableTwoFactorAuthenticationForUser()
    {
        return redirect()->intended('/#/');
    }

    public function verifyToken(Request $request)
    {
        $this->validate(
            $request,
            [
                'token' => 'required|string',
            ]
        );

        $user = auth()->user();

        if ($request->token === decrypt($user->two_factor_token)) {
            $user->two_factor_expiry = Carbon::now()->addMinutes(config('session.lifetime'));
            $user->save();
            return redirect()->intended('/home');
        }

        return redirect('/2fa')->with('message', 'Incorrect code.');
    }

    public function getQrCode(User $user)
    {
        if (!empty($user->google_secret) || empty($user->phone_number)) {
            return response()->json(['message' => 'Unable to setup'], 400);
        }

        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $qr_code = $google2fa->getQRCodeUrl(
            config('taskmanager.app_name'),
            $user->email,
            $secret
        );


        return response()->json(
            [
                'secret'  => $secret,
                'qr_code' => $qr_code,
            ],
            200
        );
    }

    public function enableTwoFactorAuthentication(TwoFactorVerification $request)
    {
        $token = (new Google2FA())->verifyKey($request->input('secret'), $request->input('one_time_password'));

        if (empty($token)) {
            return response()->json('Unable to generate token');
        }

        $user = User::where('user_id', $request->input('user'))->first();
        $user->two_factor_token = $token;
        $user->two_factor_expiry = Carbon::now()->addMinutes(config('session.lifetime'));
        $user->two_factor_authentication_enabled = true;
        $user->save();

        return response()->json('Token updated');
    }
}
