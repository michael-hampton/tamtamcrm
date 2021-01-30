<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Deal;
use App\Repositories\DealRepository;
use App\Requests\SearchRequest;
use App\Transformations\DealTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

class DealSearch extends BaseSearch
{
    use DealTransformable;

    private $dealRepository;

    private Deal $model;

    /**
     * DealSearch constructor.
     * @param DealRepository $dealRepository
     */
    public function __construct(DealRepository $dealRepository)
    {
        $this->dealRepository = $dealRepository;
        $this->model = $dealRepository->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'task_sort_order' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query =
            $this->model->select('*');

        if ($request->has('search_term') && !empty($request->search_term)) {
            $this->searchFilter($request->search_term);
        }

        if ($request->filled('customer_id')) {
            $this->query->whereCustomerId($request->customer_id);
        }

        if ($request->filled('project_id')) {
            $this->query->whereProjectId($request->project_id);
        }

        if ($request->filled('task_status')) {
            $this->status('deals', $request->task_status_id, 'task_status_id');
        }

        if ($request->filled('task_type')) {
            $this->query->whereTaskType($request->task_type);
        }

        if ($request->filled('user_id')) {
            $this->query->where('assigned_to', '=', $request->user_id);
        }

        if ($request->filled('id')) {
            $this->query->whereId($request->id);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->checkPermissions('dealcontroller.index');

        $this->orderBy($orderBy, $orderDir);

        $deals = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->dealRepository->paginateArrayResults($deals, $recordsPerPage);
            return $paginatedResults;
        }

        return $deals;
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
                $query->where('name', 'like', '%' . $filter . '%')->orWhere('description', 'like', '%' . $filter . '%')
                      ->orWhere('rating', 'like', '%' . $filter . '%')
                      ->orWhere('custom_value1', 'like', '%' . $filter . '%')
                      ->orWhere('custom_value2', 'like', '%' . $filter . '%')
                      ->orWhere('custom_value3', 'like', '%' . $filter . '%')
                      ->orWhere('custom_value4', 'like', '%' . $filter . '%');
            }
        );

        return true;
    }

    public function buildCurrencyReport (Request $request)
    {
        $this->query =!DB::table('invoices')
             ->select(DB::raw('count(*) as count, currencies.name, SUM(total) as total, SUM(balance) AS balance'))
             ->join('currencies', 'currencies.id', '=', 'invoices.currency_id')
             ->where('currency_id', '<>', 0)
             ->groupBy('currency_id');
    }

    public function buildReport (Request $request)
    {
        $this->query = DB::table('invoices');
        
         if(!empty($request->input('group_by')) {
             // assigned to, status, source_type, customer
            $this->query->select(DB::raw('count(*) as count, customers.name AS customer, task_statuses.name AS status, source_type.name AS source_type, CONCAT(users.first_name," ",users.last_name) as assigned_to, SUM(valued_at) AS valued_at'))
            $this->query->groupBy($request->input('group_by'));
        } else {
            $this->query->select('customers.name AS customer, task_statuses.name AS status, source_type.name AS source_type, valued_at, due_date', DB::raw('CONCAT(first_name," ",last_name) as assigned_to'));
        }

         $this->query->join('customers', 'customers.id', '=', 'deals.customer_id')
         ->join('source_type', 'source_type.id', '=', 'deals.source_type')
         ->join('task_statuses', 'task_statuses.id', '=', 'deals.task_status')
         ->leftJoin('users', 'users.id', '=', 'deals.assigned_to')
         ->orderBy('deals.created_at');
       
             //$this->query->where('status', '<>', 1)
            
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $deals = $list->map(
            function (Deal $deal) {
                return $this->transformDeal($deal);
            }
        )->all();

        return $deals;
    }
}
