<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountUser;
use App\Models\CompanyToken;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Industry;
use App\Models\Language;
use App\Models\PaymentGateway;
use App\Models\PaymentMethod;
use App\Models\Permission;
use App\Models\TaxRate;
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

class LoginController extends Controller
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

            $permissions = Permission::getRolePermissions($user);

            $allowed_permissions = [];

            foreach ($permissions as $permission) {
                $allowed_permissions[$permission->role_id][$permission->name] = $permission->has_permission;
            }

            $response = [
                'success' => true,
                'data'    => [
                    'account_id'          => $default_account->id,
                    'require_login'       => (bool)$default_account->settings->require_admin_password,
                    'id'                  => $user->id,
                    'auth_token'          => $user->auth_token,
                    'name'                => $user->name,
                    'email'               => $user->email,
                    'accounts'            => $accounts,
                    'allowed_permissions' => $allowed_permissions,
                    'number_of_accounts'  => $user->accounts->count(),
                    'currencies'          => Currency::all()->toArray(),
                    'languages'           => Language::all()->toArray(),
                    'industries'          => Industry::all()->toArray(),
                    'countries'           => Country::all()->toArray(),
                    'payment_types'       => PaymentMethod::all()->toArray(),
                    'gateways'            => PaymentGateway::all()->toArray(),
                    'tax_rates'           => TaxRate::all()->toArray(),
                    'custom_fields'       => $user->account_user()->account->custom_fields,
                    'users'               => User::where('is_active', '=', 1)->get(
                        ['first_name', 'last_name', 'phone_number', 'id', 'email']
                    )->toArray()
                ]
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

        $permissions = Permission::getRolePermissions($user);

        $allowed_permissions = [];

        foreach ($permissions as $permission) {
            $allowed_permissions[$permission->role_id][$permission->name] = $permission->has_permission;
        }

        return [
            'success' => true,
            'data'    => [
                'redirect'            => 'http://taskman2.develop',
                'account_id'          => $default_account->id,
                'require_login'       => (bool)$default_account->settings->require_admin_password,
                'id'                  => $user->id,
                'auth_token'          => $user->auth_token,
                'name'                => $user->first_name . ' ' . $user->last_name,
                'email'               => $user->email,
                'accounts'            => json_encode($accounts),
                'allowed_permissions' => json_encode($allowed_permissions),
                'number_of_accounts'  => $user->accounts->count(),
                'currencies'          => json_encode(Currency::all()->toArray()),
                'languages'           => json_encode(Language::all()->toArray()),
                'countries'           => json_encode(Country::all()->toArray()),
                'payment_types'       => json_encode(PaymentMethod::all()->toArray()),
                'gateways'            => json_encode(PaymentGateway::all()->toArray()),
                'industries'          => json_encode(Industry::all()->toArray()),
                'tax_rates'           => json_encode(TaxRate::all()->toArray()),
                'custom_fields'       => json_encode(auth()->user()->account_user()->account->custom_fields),
                'users'               => json_encode(
                    User::where('is_active', '=', 1)->get(
                        ['first_name', 'last_name', 'phone_number', 'id']
                    )->toArray()
                )
            ]
        ];
    }

}
