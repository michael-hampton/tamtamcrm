<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountUser;
use App\Models\CompanyToken;
use App\Models\User;
use App\Requests\LoginRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use JWTAuth;
use JWTAuthException;
use Laravel\Socialite;

class LoginController extends BaseController
{
    use AuthenticatesUsers;

    public function doLogin(LoginRequest $request)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return response()->json(['message' => 'Too many login attempts, you are being throttled'], 401);
        }

        if ($token = auth()->attempt($request->all())) {
            $user = auth()->user();
            $default_account = $user->accounts->first()->domains->default_company;

            $token = $this->getToken($request, $default_account);

            $user->auth_token = $token;
            $user->save();

            $accounts = AccountUser::whereUserId($user->id)->with('account')->get();

            CompanyToken::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'is_web'     => true,
                    'token'      => $token,
                    'user_id'    => $user->id,
                    'account_id' => $default_account->id,
                    'domain_id'  => $user->accounts->first()->domains->id
                ]
            );

            $response = [
                'success' => true,
                'data'    => $this->getIncludes()
            ];

            Cache::put('reauthenticate_last_authentication', strtotime('now'));

            return response()->json($response, 201);
        }

        return response()->json(['success' => false, 'data' => 'Record doesnt exists']);
    }

    private function getToken(LoginRequest $request, Account $account)
    {
        $expiration_time = !empty($account->settings->default_logout_time) ? $account->settings->default_logout_time : null;
        config()->set('jwt.ttl', $expiration_time);
        $token = null;

        try {
            if (!$token = JWTAuth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return response()->json(
                    [
                        'response' => 'error',
                        'message'  => 'Password or email is invalid',
                        'token'    => $token
                    ]
                );
            }
        } catch (JWTAuthException $e) {
            return response()->json(
                [
                    'response' => 'error',
                    'message'  => 'Token creation failed',
                ]
            );
        }
        return $token;
    }

    public function showLogin()
    {
        // show the form
        return View::make('login');
    }

    public function doLogout()
    {
        Auth::logout(); // log the user out of our application
        return Redirect::to('login'); // redirect the user to the login screen
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToGoogle($social = 'google')
    {
        return Socialite\Facades\Socialite::with($social)
                                          ->scopes([])
                                          ->stateless()
                                          ->redirect();
    }

    /**
     * @param string $social
     * @return Application|JsonResponse|RedirectResponse|Redirector
     */
    public function handleGoogleCallback($social = 'google')
    {
        try {
            //create a user using socialite driver google
            $user = Socialite\Facades\Socialite::with($social)->stateless()->user();

            // if the user exits, use that user and login
            $finduser = User::where('google_id', $user->id)->first();

            if ($finduser) {
                Auth::login($finduser);
                Cache::put('reauthenticate_last_authentication', strtotime('now'));
                $response = $this->executeLogin(Str::random(64));
                return view('google-login')->with($response);
            } else {
                //user is not yet created, so create first
                $newUser = User::create(
                    [
                        'name'      => $user->name,
                        'email'     => $user->email,
                        'google_id' => $user->id,
                        'password'  => encrypt('')
                    ]
                );

                //login as the new user
                Auth::login($newUser);
                // go to the dashboard
                return redirect('/dashboard');
            }
            //catch exceptions
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    private function executeLogin($token)
    {
        $this->forced_includes = ['company_users'];

        $token = JWTAuth::fromUser(auth()->user());
        $user = auth()->user();
        $user->auth_token = $token;
        $user->save();

        $default_account = $user->accounts->first()->domains->default_company;
        //$user->setAccount($default_account);

        $accounts = AccountUser::whereUserId($user->id)->with('account')->get()->toArray();

        CompanyToken::updateOrCreate(
            ['user_id' => $user->id],
            [
                'is_web'     => true,
                'token'      => $token,
                'user_id'    => $user->id,
                'account_id' => $default_account->id,
                'domain_id'  => $user->accounts->first()->domains->id
            ]
        );

        $response = [
            'success' => true,
            'data'    => $this->getIncludes()
        ];

        return response()->json($response, 201);
    }

    public function enable($provider, Request $request)
    {
        $key = "{$provider}_id";
        $key2 = "{$provider}_secret";

        $user = User::where('id', $request->input('user'))->first();

        if (!empty($user->{$key}) || !empty($user->{$key2})) {
            return response()->json('User already has account');
        }

        $user->{$key} = $request->input('user_id');
        $user->{$key2} = $request->input('secret');
        $user->save();

        return response()->json('success');
    }

}
