<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\File;
use App\Repositories\CustomerRepository;
use App\Requests\SearchRequest;
use App\Transformations\CustomerTransformable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CustomerSearch extends BaseSearch
{
    use CustomerTransformable;

    /**
     * @var CustomerRepository
     */
    private CustomerRepository $customerRepository;

    private Customer $model;

    /**
     * CustomerFilter constructor.
     * @param CustomerRepository $customerRepository
     */
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->model = $customerRepository->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'name' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select(
            'customers.*',
            'billing.address_1',
            'billing.address_2',
            'billing.city',
            'billing.state_code AS state',
            'billing.zip',
            'billing.country_id AS country_id',
            'shipping.address_1 AS shipping_address_1',
            'shipping.address_2 AS shipping_address_2',
            'shipping.city AS shipping_city',
            'shipping.state_code AS shipping_town',
            'shipping.zip AS shipping_zip',
            'shipping.country_id AS shipping_country_id'
        )->leftJoin(
            'addresses as billing',
            function ($join) {
                $join->on('billing.customer_id', '=', 'customers.id');
                $join->where('billing.address_type', '=', 1);
            }
        )->leftJoin(
            'addresses as shipping',
            function ($join) {
                $join->on('shipping.customer_id', '=', 'customers.id');
                $join->where('shipping.address_type', '=', 2);
            }
        );

        if ($request->has('status')) {
            $this->status('customers', $request->status);
        } else {
            $this->query->withTrashed();
        }

        if ($request->filled('company_id')) {
            $this->query->byCompany($request->company_id);
        }

        if ($request->filled('group_settings_id')) {
            $this->query->whereGroupId($request->group_settings_id);
        }

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->filled('id')) {
            $this->query->byId($request->id);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->query->byDate($request->input('start_date'), $request->input('end_date'));
        }

        $this->query->byAccount($account);

        $this->checkPermissions('customercontroller.index');

        $this->orderBy($orderBy, $orderDir);

        $this->query->groupBy('customers.id');

        $customers = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->customerRepository->paginateArrayResults($customers, $recordsPerPage);
            return $paginatedResults;
        }

        return $customers;
    }

    /**
     * @param string $filter
     * @return bool
     */
    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where(
            function ($query) use ($filter) {
                $query->where('name', 'like', '%' . $filter . '%')
                      ->orWhere('number', 'like', '%' . $filter . '%')
                      ->orWhereHas(
                          'contacts',
                          function ($query) use ($filter) {
                              $query->where('email', 'like', '%' . $filter . '%')
                                    ->orWhere('first_name', 'like', '%' . $filter . '%')
                                    ->orWhere('last_name', 'like', '%' . $filter . '%');
                          }
                      )
                      ->orWhere('custom_value1', 'like', '%' . $filter . '%')
                      ->orWhere('custom_value2', 'like', '%' . $filter . '%')
                      ->orWhere('custom_value3', 'like', '%' . $filter . '%')
                      ->orWhere('custom_value4', 'like', '%' . $filter . '%');
            }
        );

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->cacheFor(now()->addMonthNoOverflow())->cacheTags(['customers'])->get();
        $contacts = CustomerContact::all()->groupBy('customer_id');

        $files = File::where('fileable_type', '=', 'App\Models\Customer')->get()->groupBy('fileable_id');

        $customers = $list->map(
            function (Customer $customer) use ($contacts, $files) {
                return $this->transformCustomer($customer, $contacts, $files);
            }
        )->all();

        return $customers;
    }

    public function buildCurrencyReport(Request $request, Account $account)
    {
        return DB::table('customers')
                 ->select(
                     DB::raw(
                         'count(*) as count, currencies.name, SUM(amount_paid) as amount_paid, SUM(balance) AS balance'
                     )
                 )
                 ->join('currencies', 'currencies.id', '=', 'customers.currency_id')
                 ->where('currency_id', '<>', 0)
                 ->where('account_id', '=', $account->id)
                 ->groupBy('currency_id')
                 ->get();
    }

    public function buildReport(Request $request, Account $account)
    {
        $this->query = DB::table('customers');

        if (!empty($request->input('group_by'))) {
            $this->query->select(
                DB::raw(
                    'count(*) as count, customers.name, currencies.name AS currency, SUM(amount_paid) AS amount_paid, SUM(balance) AS balance, CONCAT(first_name," ",last_name) as contact'
                )
            )
                        ->groupBy($request->input('group_by'));
        } else {
            $this->query->select(
                DB::raw('IFNULL(customers.name, "No Name") AS name'),
                'customers.website AS website',
                'currencies.name AS currency',
                'languages.name AS language',
                'customers.private_notes',
                'customers.public_notes',
                'industries.name AS industry',
                'customers.custom_value1 AS custom1',
                'customers.custom_value2 AS custom2',
                'customers.custom_value3 AS custom3',
                'customers.custom_value4 AS custom4',
                'billing.address_1',
                'billing.address_2',
                'billing.city',
                'billing.state_code AS state',
                'billing.zip',
                'billing_country.name AS country',
                'shipping.address_1 AS shipping_address_1',
                'shipping.address_2 AS shipping_address_2',
                'shipping.city AS shipping_city',
                'shipping.state_code AS shipping_town',
                'shipping.zip AS shipping_zip',
                'shipping_country.name AS shipping_country',
                'customers.phone',
                'customers.vat_number',
                'number',
                DB::raw('CONCAT(assigned_user.first_name," ", assigned_user.last_name) as assigned_to'),
                DB::raw('CONCAT(users.first_name," ", users.last_name) as user'),
                DB::raw('CONCAT(customer_contacts.first_name," ", customer_contacts.last_name) as contact'),
                'customer_contacts.email AS contact_email',
                'customer_contacts.email AS contact_email',
                'customer_contacts.first_name AS contact_first_name',
                'customer_contacts.last_name AS contact_last_name',
                'customer_contacts.phone AS contact_phone',
                DB::raw('(balance + amount_paid) AS total'),
                DB::raw('ROUND(balance, 2) AS balance'),
                DB::raw('ROUND(amount_paid, 2) AS amount_paid'),
                DB::raw('ROUND(credit_balance, 2) AS credit_balance'),
                DB::raw(
                    '(ROUND(balance * IF(account_currency.exchange_rate = 0.00, 1, account_currency.exchange_rate), 2)) AS converted_balance'
                ),
                DB::raw(
                    '(ROUND(amount_paid * IF(account_currency.exchange_rate = 0.00, 1, account_currency.exchange_rate), 2)) AS converted_amount_paid'
                ),
                DB::raw(
                    '(ROUND(credit_balance * IF(account_currency.exchange_rate = 0.00, 1, account_currency.exchange_rate), 2)) AS converted_credit_balance'
                ),
            );
        }

        $this->query->join('currencies', 'currencies.id', '=', 'customers.currency_id')
                    ->leftJoin('accounts', 'accounts.id', '=', 'customers.account_id')
                    ->leftJoin(
                        'currencies AS account_currency',
                        'account_currency.id',
                        '=',
                        'accounts.settings->currency_id'
                    )
                    ->leftJoin('languages', 'languages.id', '=', 'customers.settings->language_id')
                    ->leftJoin('industries', 'industries.id', '=', 'customers.industry_id')
                    ->leftJoin('addresses AS billing', 'billing.customer_id', '=', 'customers.id')
                    ->leftJoin('addresses AS shipping', 'shipping.customer_id', '=', 'customers.id')
                    ->leftJoin('countries AS billing_country', 'billing_country.id', '=', 'billing.country_id')
                    ->leftJoin('countries AS shipping_country', 'shipping_country.id', '=', 'shipping.country_id')
                    ->leftJoin('users AS assigned_user', 'assigned_user.id', '=', 'customers.assigned_to')
                    ->leftJoin('users', 'users.id', '=', 'customers.user_id')
                    ->leftJoin(
                        'customer_contacts',
                        function ($join) {
                            $join->on('customer_contacts.customer_id', '=', 'customers.id');
                            $join->where('customer_contacts.is_primary', '=', 1);
                        }
                    )
                    ->where('customers.account_id', '=', $account->id)
                    ->where('shipping.address_type', '=', 2)
                    ->where('billing.address_type', '=', 1);

        $order = $request->input('orderByField');
        $order_dir = $request->input('orderByDirection');

        if (!empty($order) && !empty($order_dir)) {
            if ($order === 'contact') {
                $this->query->orderByRaw(
                    'CONCAT(customer_contacts.first_name, " ", customer_contacts.last_name)' . $order_dir
                );
            } elseif (!empty($this->field_mapping[$order])) {
                $order = str_replace('$table', 'customers', $this->field_mapping[$order]);
                $this->query->orderBy($order, $request->input('orderByDirection'));
            } else {
                $this->query->orderBy('customers.' . $order, $order_dir);
            }
        }

        $rows = $this->query->get()->toArray();

        if (!empty($request->input('perPage')) && $request->input('perPage') > 0) {
            return $this->customerRepository->paginateArrayResults($rows, $request->input('perPage'));
        }

        return $rows;
        //$this->query->where('status', '<>', 1)

    }

}
