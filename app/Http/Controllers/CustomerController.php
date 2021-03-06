<?php

namespace App\Http\Controllers;

use App\Components\Customer\ContactRegister;
use App\Factory\CustomerFactory;
use App\Jobs\Customer\StoreCustomerAddress;
use App\Models\Account;
use App\Models\CompanyToken;
use App\Models\Customer;
use App\Models\CustomerGateway;
use App\Models\CustomerType;
use App\Models\ErrorLog;
use App\Models\Transaction;
use App\Repositories\CustomerContactRepository;
use App\Repositories\CustomerTypeRepository;
use App\Repositories\Interfaces\CustomerRepositoryInterface;
use App\Requests\Customer\CreateCustomerRequest;
use App\Requests\Customer\CustomerRegistrationRequest;
use App\Requests\Customer\UpdateCustomerRequest;
use App\Requests\SearchRequest;
use App\Search\CustomerSearch;
use App\Settings\CustomerSettings;
use App\Transformations\CustomerGatewayTransformable;
use App\Transformations\CustomerTransformable;
use App\Transformations\ErrorLogTransformable;
use App\Transformations\TransactionTransformable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use function request;

class CustomerController extends Controller
{

    use CustomerTransformable;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customer_repo;

    private $contact_repo;

    /**
     * CustomerController constructor.
     * @param CustomerRepositoryInterface $customer_repo
     * @param CustomerContactRepository $contact_repo
     */
    public function __construct(CustomerRepositoryInterface $customer_repo, CustomerContactRepository $contact_repo)
    {
        $this->customer_repo = $customer_repo;
        $this->contact_repo = $contact_repo;
    }

    /**
     * @param SearchRequest $request
     * @return mixed
     */
    public function index(SearchRequest $request)
    {
        $customers =
            (new CustomerSearch($this->customer_repo))->filter($request, auth()->user()->account_user()->account);
        return response()->json($customers);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCustomerRequest $request
     * @param int $id
     * @return Response
     * @throws Exception
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer = $this->customer_repo->update($request->except(['addresses', 'settings']), $customer);

        $obj_merged = (object)array_merge((array)$customer->settings, (array)$request->settings);
        $customer = (new CustomerSettings)->save($customer, $obj_merged);

        $customer = StoreCustomerAddress::dispatchNow($customer, $request->all());

        if (!empty($request->contacts)) {
            $this->contact_repo->save($request->contacts, $customer);
        }

        return response()->json($this->transformCustomer($customer));
    }

    public function show(Customer $customer)
    {
        return response()->json($this->transformCustomer($customer));
    }

    /**
     * @param CreateCustomerRequest $request
     * @return array
     * @throws Exception
     */
    public function store(CreateCustomerRequest $request)
    {
        $customer = CustomerFactory::create(auth()->user()->account_user()->account, auth()->user());
        $customer = $this->customer_repo->create($request->except('addresses', 'settings'), $customer);

        $obj_merged = (object)array_merge((array)$customer->settings, (array)$request->settings);
        $customer = (new CustomerSettings)->save($customer, $obj_merged);
        $customer = StoreCustomerAddress::dispatchNow($customer, $request->only('addresses'));

        if (!empty($request->contacts)) {
            $this->contact_repo->save($request->contacts, $customer);
        }

        return $this->transformCustomer($customer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     * @throws Exception
     */
    public function archive(Customer $customer)
    {
        $response = $customer->archive();

        if ($response) {
            return response()->json('Customer deleted!');
        }

        return response()->json('Unable to delete customer!');
    }

    public function destroy(Customer $customer)
    {
        $this->authorize('delete', $customer);

        $customer->deleteEntity();
        return response()->json([], 200);
    }

    public function getCustomerTypes()
    {
        $customerTypes = (new CustomerTypeRepository(new CustomerType))->getAll();
        return response()->json($customerTypes);
    }

    public function bulk()
    {
        $action = request()->input('action');

        $ids = request()->input('ids');
        $customers = Customer::withTrashed()->find($ids);

        $customers->each(
            function ($customer, $key) use ($action) {
                $this->customer_repo->{$action}($customer);
            }
        );
        return response()->json(Customer::withTrashed()->whereIn('id', $ids));
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function restore(int $id)
    {
        $customer = Customer::withTrashed()->where('id', '=', $id)->first();
        $customer->restoreEntity();
        return response()->json([], 200);
    }

    /**
     * @param CustomerRegistrationRequest $request
     * @return JsonResponse
     * @return JsonResponse
     */
    public function register(CustomerRegistrationRequest $request)
    {
        $account = Account::where('portal_domain', '=', $request->input('portal_domain'))->firstOrFail();
        $token_sent = request()->bearerToken();
        $token = CompanyToken::whereToken($token_sent)->first();
        $user = $token->user;

        $contact = (new ContactRegister($request->all(), $account, $user))->create();

        return response()->json(['contact' => $contact]);
    }

    public function getTransactions(Customer $customer)
    {
        $transactions = $customer->transactions;

        $transactions = $transactions->map(
            function (Transaction $transaction) {
                return (new TransactionTransformable())->transformTransaction($transaction);
            }
        )->all();

        return response()->json($transactions);
    }

    public function getErrorLogs(Customer $customer)
    {
        $error_logs = $customer->error_logs;

        $error_logs = $error_logs->map(
            function (ErrorLog $error_log) {
                return (new ErrorLogTransformable())->transformErrorLog($error_log);
            }
        )->all();

        return response()->json($error_logs);
    }

    public function gateways(Customer $customer)
    {
        $gateway_tokens = $customer->gateways;

        return $gateway_tokens->map(
            function (CustomerGateway $gateway) {
                return (new CustomerGatewayTransformable())->transformGateway($gateway);
            }
        )->all();
    }
}
