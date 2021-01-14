<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use App\Requests\SearchRequest;
use App\Transformations\CustomerTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

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
