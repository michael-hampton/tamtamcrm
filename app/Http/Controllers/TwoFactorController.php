<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function show2faForm()
    {
        return view('2fa');
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

        if ($request->token === $user->two_factor_token) {
            $user->two_factor_expiry = Carbon::now()->addMinutes(config('session.lifetime'));
            $user->save();
            return redirect()->intended('/home');
        }

        return redirect('/2fa')->with('message', 'Incorrect code.');
    }
}
