<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Customer;
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

        $this->query = $this->model->select('*');

        if ($request->has('status')) {
            $this->status('customers', $request->status);
        }

        if ($request->filled('company_id')) {
            $this->query->whereCompanyId($request->company_id);
        }

        if ($request->filled('group_settings_id')) {
            $this->query->whereGroupId($request->group_settings_id);
        }

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->filled('id')) {
            $this->query->whereId($request->id);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->checkPermissions('customercontroller.index');

        $this->orderBy($orderBy, $orderDir);

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

    public function buildCurrencyReport(Request $request, Account $account)
    {
        $this->query = DB::table('invoices')
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
                    'count(*) as count, name, currencies.name AS currency, SUM(amount_paid) AS amount_paid, SUM(balance) AS balance, CONCAT(first_name," ",last_name) as contact'
                )
            )
                        ->groupBy($request->input('group_by'));
        } else {
            $this->query->select(
                DB::raw('CONCAT(first_name," ",last_name) as contact'),
                'currencies.name AS currency', 'number', 'balance', 'amount_paid'
            );
        }

        $this->query->join('currencies', 'currencies.id', '=', 'customers.currency_id')
                    ->join(
                        'customer_contacts',
                        function ($join) {
                            $join->on('customer_contacts.customer_id', '=', 'customers.id');
                            $join->where('customer_contacts.is_primary', '=', 1);
                        }
                    )
                    ->where('customers.account_id', '=', $account->id)
                    ->orderBy('customers.'.$request->input('orderByField'), $request->input('orderByDirection'));
        //$this->query->where('status', '<>', 1)

        $rows = $this->query->get()->toArray();

        if (!empty($request->input('perPage')) && $request->input('perPage') > 0) {
            return $this->customerRepository->paginateArrayResults($rows, $request->input('perPage'));
        }

        return $rows;
        //$this->query->where('status', '<>', 1)

    }


    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();

        $customers = $list->map(
            function (Customer $customer) {
                return $this->transformCustomer($customer);
            }
        )->all();

        return $customers;
    }

}
