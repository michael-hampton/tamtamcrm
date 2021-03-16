<?php

namespace App\Http\Controllers;

use App\Actions\Plan\UpgradePlan;
use App\Factory\AccountFactory;
use App\Models\Account;
use App\Models\CompanyToken;
use App\Models\Domain;
use App\Models\Licence;
use App\Models\Plan;
use App\Notifications\NewAccountCreated;
use App\Repositories\AccountRepository;
use App\Requests\Account\StoreAccountRequest;
use App\Requests\Account\UpdateAccountRequest;
use App\Settings\AccountSettings;
use App\Traits\UploadableTrait;
use App\Transformations\AccountTransformable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class AccountController
 * @package App\Http\Controllers
 */
class AccountController extends BaseController
{
    use DispatchesJobs, AccountTransformable, UploadableTrait;

    public $forced_includes = [];
    protected $account_repo;

    /**
     * AccountController constructor.
     * @param AccountRepository $account_repo
     */
    public function __construct(AccountRepository $account_repo)
    {
        $this->account_repo = $account_repo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $accounts = Account::all();
        return response()->json($accounts);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreAccountRequest $request
     * @return mixed
     */
    public function store(StoreAccountRequest $request)
    {
        $account = AccountFactory::create(auth()->user()->account_user()->account->domain_id);
        $this->account_repo->save($request->except('settings'), $account);

        $logo_path = $this->uploadLogo($request->file('company_logo'));
        $request->settings->company_logo = $logo_path;
        $account = (new AccountSettings)->save($account, $request->settings, true);

        if (!$account) {
            return response()->json('Unable to update settings', 500);
        }

        auth()->user()->attachUserToAccount($account, true);

        event(new NewAccountCreated(auth()->user(), $account));

        return response()->json($this->transformAccount($account));
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        $account = $this->account_repo->findAccountById($id);
        return response()->json($this->transformAccount($account));
    }

    /**
     *
     *
     * the specified resource in storage.
     * @param UpdateAccountRequest $request
     * @param int $id
     * @return mixed
     */
    public function update(UpdateAccountRequest $request, int $id)
    {
        $account = $this->account_repo->findAccountById($id);

        if (!empty($request->file('company_logo')) && $request->file('company_logo') !== 'null') {
            $logo_path = $this->uploadLogo($request->file('company_logo'));
            $request->settings->company_logo = $logo_path;
        }

        $account = (new AccountSettings)->save($account, $request->settings);

        return response()->json($this->transformAccount($account));
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @param int $id
     * @return mixed
     */

    public function destroy(Request $request, int $id)
    {
        $account = Account::find($id);

        $company_count = $account->domains->companies->count();

        if ($company_count == 1) {
            $account->account_users->each(
                function ($account_user) {
                    $account_user->user->forceDelete();
                }
            );

            $account->domain->delete();
        } else {
            $domain = $account->domains;
            $account_id = $account->id;

            $account->account_users->each(
                function ($account_user) {
                    $account_user->delete();
                }
            );

            $account->delete();

            //If we are deleting the default companies, we'll need to make a new company the default.
            if ($domain->default_company_id == $account_id) {
                $new_default_company = Account::whereDomainId($domain->id)->first();
                $domain->default_company_id = $new_default_company->id;
                $domain->save();
            }
        }

        //@todo delete documents also!!

        //@todo in the hosted version deleting the last
        //account will trigger an account refund.

        return response()->json(['message' => 'success'], 200);
    }

    public function getCustomFields($entity)
    {
        $account = $this->account_repo->findAccountById(auth()->user()->account_user()->account_id);

        if (empty($account->custom_fields) || empty($account->custom_fields->{$entity})) {
            return response()->json([]);
        }

        $fields = json_decode(json_encode($account->custom_fields), true);

        $custom_fields['fields'][0] = $fields[$entity];

        return response()->json($custom_fields);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function saveCustomFields(Request $request)
    {
        $objAccount = $this->account_repo->findAccountById(auth()->user()->account_user()->account_id);
        $response = $objAccount->update(['custom_fields' => json_decode($request->fields, true)]);
        return response()->json($response);
    }

    public function getAllCustomFields()
    {
        $objAccount = $this->account_repo->findAccountById(auth()->user()->account_user()->account_id);
        return response()->json($objAccount->custom_fields);
    }

    public function changeAccount(Request $request)
    {
        $user = auth()->user();
        CompanyToken::where('token', $user->auth_token)->update(['account_id' => $request->account_id]);
    }

    public function refresh()
    {
        $response = [
            'success' => true,
            'data'    => $this->getIncludes()
        ];

        return response()->json($response, 201);
    }

    public function upgrade(Request $request)
    {
        $domain = auth()->user()->account_user()->account->domains;
        $plan = $request->input(
            'package'
        ) === 'standard' ? Plan::PLAN_STANDARD : Plan::PLAN_ADVANCED;
        $period = $request->input(
            'period'
        ) === 'monthly' ? Plan::PLAN_PERIOD_MONTH : Plan::PLAN_PERIOD_YEAR;

        (new UpgradePlan())->execute($domain, ['plan' => $plan, 'plan_period' => $period]);
    }

    public function apply(Request $request)
    {
        $licence = Licence::where('licence_number', $request->input('licence_number'))->first();

        if (empty($licence)) {
            return response()->json('Licence could not be found');
        }

        $licence_details = json_decode($licence->details, true);

        $package = $licence_details['package'];
        $period = $licence_details['period'];
        $number_of_licences = $licence_details['number_of_licences'];

        if (empty($number_of_licences)) {
            $package === 'standard' ? env('STANDARD_NUMBER_OF_LICENCES') : env('ADVANCED_NUMBER_OF_LICENCES');
        }

        $domain = auth()->user()->account_user()->account->domains;
        $domain->subscription_plan = $package === 'standard' ? Domain::SUBSCRIPTION_STANDARD : Domain::SUBSCRIPTION_ADVANCED;
        $domain->subscription_period = $period === 'monthly' ? Domain::SUBSCRIPTION_PERIOD_MONTH : Domain::SUBSCRIPTION_PERIOD_YEAR;
        $domain->subscription_expiry_date = $period === 'monthly' ? now()->addMonthNoOverflow()
            : now()->addYearNoOverflow();
        $domain->number_of_licences = $number_of_licences;
        $domain->licence_number = $request->input('licence_number');
        $domain->save();
    }
}
