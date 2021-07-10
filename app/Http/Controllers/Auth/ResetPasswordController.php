<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{

    use ResetsPasswords;

    public function getPassword($token)
    {
        return view('auth.passwords.reset', ['token' => $token]);
    }

    /**
     * Reset the given user's password.
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function updatePassword(Request $request)
    {
        //$request->validate($this->rules(), $this->validationErrorMessages());

        $validator = Validator::make(
            $request->all(),
            [
                'token'                 => ['required', 'max:255'],
                'email'                 => 'required|email|exists:users',
                'password'              => [
                    'required',
                    'confirmed',
                    'string',
                    'min:10',             // must be at least 10 characters in length
                    //                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    //                    'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    //                    'regex:/[0-9]/',      // must contain at least one digit
                    //                    'regex:/[@$!%*#?&]/', // must contain a special character
                ],
                'password_confirmation' => 'required'
            ]
        );

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.

        $response = $this->broker()->reset(
            $this->credentials($request),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Reset the given user's password.
     *
     * @param CanResetPassword $user
     * @param string $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $this->setUserPassword($user, $password);
        //Here Larvel tries to set the "Remember me" cookie 
        //$user->setRememberToken(Str::random(60)); 

        $user->save();
        event(new PasswordReset($user));
        //By default, Laravel will attempt to automatically log in the user 
        //$this->guard()->login($user); 
    }

    /**
     * @param Request $request
     * @param $response
     * @return Application|RedirectResponse|Redirector
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return redirect('/login')->with('message', 'Your password has been changed!');
    }

    /**
     * @param Request $request
     * @param $response
     * @return RedirectResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return redirect()->back()
                         ->withInput($request->only('email'))
                         ->withErrors(['email' => trans($response)]);
    }
}
