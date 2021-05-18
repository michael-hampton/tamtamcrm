<?php

namespace App\Http\Controllers;

use App\Components\Payment\Gateways\Stripe\StripeConnect;
use App\Factory\CompanyGatewayFactory;
use App\Models\Account;
use App\Models\CompanyGateway;
use App\Models\Customer;
use App\Models\CustomerGateway;
use App\Models\ErrorLog;
use App\Models\User;
use App\Repositories\AccountRepository;
use App\Repositories\CompanyGatewayRepository;
use App\Requests\CompanyGateway\StoreCompanyGatewayRequest;
use App\Requests\CompanyGateway\UpdateCompanyGatewayRequest;
use App\Requests\SearchRequest;
use App\Search\CompanyGatewaySearch;
use App\Transformations\CompanyGatewayTransformable;
use App\Transformations\ErrorLogTransformable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Class CompanyGatewayController
 * @package App\Http\Controllers
 */
class CompanyGatewayController extends Controller
{
    use CompanyGatewayTransformable;

    private $account_repo;
    private $company_gateway_repo;

    /**
     * CompanyGatewayController constructor.
     * @param AccountRepository $account_repo
     * @param CompanyGatewayRepository $company_gateway_repo
     */
    public function __construct(AccountRepository $account_repo, CompanyGatewayRepository $company_gateway_repo)
    {
        $this->account_repo = $account_repo;
        $this->company_gateway_repo = $company_gateway_repo;
    }

    public function index(SearchRequest $request)
    {
        $invoices =
            (new CompanyGatewaySearch($this->company_gateway_repo))->filter(
                $request,
                auth()->user()->account_user()->account
            );

        return response()->json($invoices);
    }

    public function store(StoreCompanyGatewayRequest $request)
    {
        $company_gateway = $this->company_gateway_repo->create(
            $request->all(),
            CompanyGatewayFactory::create(auth()->user()->account_user()->account_id, auth()->user()->id)
        );

        return response()->json($this->transformCompanyGateway($company_gateway));
    }

    /**
     * @param UpdateCompanyGatewayRequest $request
     * @param int $id
     * @return mixed
     */
    public function update(UpdateCompanyGatewayRequest $request, CompanyGateway $company_gateway)
    {
        $company_gateway = $this->company_gateway_repo->update($request->all(), $company_gateway);

        return response()->json($this->transformCompanyGateway($company_gateway));
    }

    /**
     * @param string $gateway_key
     * @return mixed
     */
    public function show(string $gateway_key)
    {
        $company_gateway = $this->company_gateway_repo->getCompanyGatewayByGatewayKey($gateway_key);

        if (!$company_gateway) {
            return response()->json([]);
        }

        return response()->json($this->transformCompanyGateway($company_gateway));
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $company_gateway = CompanyGateway:: withTrashed()->where('id', '=', $id)->first();
        $company_gateway->restore();
        return response()->json([], 200);
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function archive(CompanyGateway $company_gateway)
    {
        $company_gateway->archive();
    }

    public function destroy(CompanyGateway $company_gateway)
    {
        $this->authorize('delete', $company_gateway);

        $company_gateway->deleteEntity();
        return response()->json([], 200);
    }

    public function getErrorLogs(CompanyGateway $company_gateway)
    {
        $error_logs = $company_gateway->error_logs();

        $error_logs = $error_logs->map(
            function (ErrorLog $error_log) {
                return (new ErrorLogTransformable())->transformErrorLog($error_log);
            }
        )->all();

        return response()->json($error_logs);
    }

    public function createStripeConnectAccount(Request $request)
    {
        $company_gateway = CompanyGateway::byGatewayKey($request->input('token'), auth()->user()->account_user()->account)->first();

//        if (!empty($company_gateway)) {
//            return response()->json(['message' => 'Already has account']);
//        }

        $stripe_client_id = config('taskmanager.stripe_client_id');

        //https://stripe.com/docs/connect/oauth-reference Dynamically set the redirect URI
        $redirect_uri = 'http://tamtamcrm.develop:8080/company_gateways/stripe/complete';
        $return_url = "https://connect.stripe.com/oauth/authorize?response_type=code&client_id={$stripe_client_id}&redirect_uri={$redirect_uri}&scope=read_write";

        if (!empty(auth()->user()->email)) {
            $return_url .= '&stripe_user[email]=' . auth()->user()->email;
        }

        if (!empty(auth()->user()->first_name)) {
            $return_url .= '&stripe_user[first_name]=' . auth()->user()->first_name;
        }

        if (!empty(auth()->user()->last_name)) {
            $return_url .= '&stripe_user[first_name]=' . auth()->user()->last_name;
        }

        if (!empty(auth()->user()->phone_number)) {
            $return_url .= '&stripe_user[phone_number]=' . auth()->user()->phone_number;
        }

        if (!empty(auth()->user()->account_user()->account->country()) && !empty(auth()->user()->account_user()->account->country()->name)) {
            $return_url .= '&stripe_user[business_name]=' . auth()->user()->account_user()->account->country()->name;
        }

        if (!empty(auth()->user()->account_user()->account) && !empty(auth()->user()->account_user()->account->settings->name)) {
            $return_url .= '&stripe_user[country]=' . auth()->user()->account_user()->account->settings->name;
        }

        Cache::put('stripe_connect_user', ['user_id' => auth()->user()->id, 'account_id' => auth()->user()->account_user()->account_id], now()->addMinutes(10));

        return response()->json(['url' => $return_url]);
    }

    public function completeStripeConnect(Request $request)
    {
        $objStripe = new StripeConnect();

        $cache = Cache::pull('stripe_connect_user');

        $user = User::byId($cache['user_id'])->first();
        $account = Account::byId($cache['account_id'])->first();

        if (empty($user) || empty($account)) {
            throw new \Exception('Required Data missing');
        }

        $token = $objStripe->requestToken($request->code);

        $objStripe = new StripeConnect();

        $stripe_accounts = $objStripe->listAllConnectedAccounts();

        $account_id = null;

        foreach ($stripe_accounts as $stripe_account) {
            if ($stripe_account->email === $user->email) {
                $account_id = $stripe_account->id;
                break;
            }
        }

        $settings = [
            "token_type"             => 'bearer',
            "stripe_publishable_key" => $token->stripe_publishable_key,
            "scope"                  => $token->scope,
            "livemode"               => $token->livemode,
            "stripe_user_id"         => $token->stripe_user_id,
            "account_id"             => $token->stripe_user_id,
            "refresh_token"          => $token->refresh_token,
            "access_token"           => $token->access_token
        ];

        if (!empty($account_id)) {
            $settings = ['account_id' => $account_id];
        }

        $company_gateway = $this->company_gateway_repo->create(
            ['gateway_key' => 'ocglwiyeow', 'settings' => $settings],
            CompanyGatewayFactory::create($account->id, $user->id)
        );

        return view('stripe.completed', ['gateway' => $this->transformCompanyGateway($company_gateway)]);
    }

    public function refreshStripeConnect(Request $request)
    {
        die('here');
    }
}
