<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Credit;
use App\Repositories\CreditRepository;
use App\Requests\SearchRequest;
use App\Transformations\CreditTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

class CreditSearch extends BaseSearch
{
    use CreditTransformable;

    private $credit_repo;

    private Credit $model;

    /**
     * CompanySearch constructor.
     * @param CreditRepository $credit_repository
     */
    public function __construct(CreditRepository $credit_repository)
    {
        $this->credit_repo = $credit_repository;
        $this->model = $credit_repository->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'total' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('*');

        if ($request->filled('search_term')) {
            $this->searchFilter($request->search_term);
        }

        if ($request->has('status')) {
            $this->status('credits', $request->status);
        }

        if ($request->filled('customer_id')) {
            $this->query->whereCustomerId($request->customer_id);
        }

        if ($request->filled('project_id')) {
            $this->query->whereProjectId($request->project_id);
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

        $this->checkPermissions('creditcontroller.index');

        $this->orderBy($orderBy, $orderDir);

        $companies = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->credit_repo->paginateArrayResults($companies, $recordsPerPage);
            return $paginatedResults;
        }

        return $companies;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where(
            function ($query) use ($filter) {
                $query->where('credits.number', 'like', '%' . $filter . '%')
                      ->orWhere('credits.number', 'like', '%' . $filter . '%')
                      ->orWhere('credits.date', 'like', '%' . $filter . '%')
                      ->orWhere('credits.total', 'like', '%' . $filter . '%')
                      ->orWhere('credits.balance', 'like', '%' . $filter . '%')
                      ->orWhere('credits.custom_value1', 'like', '%' . $filter . '%')
                      ->orWhere('credits.custom_value2', 'like', '%' . $filter . '%')
                      ->orWhere('credits.custom_value3', 'like', '%' . $filter . '%')
                      ->orWhere('credits.custom_value4', 'like', '%' . $filter . '%');
            }
        );

        return true;
    }

    public function buildCurrencyReport (Request $request)
    {
        $this->query =!DB::table('credits')
             ->select(DB::raw('count(*) as count, currencies.name, SUM(total) as total, SUM(balance) AS balance'))
             ->join('currencies', 'currencies.id', '=', 'credits.currency_id')
             ->where('currency_id', '<>', 0)
             ->groupBy('currency_id');
    }

    public function buildReport (Request $request)
    {
        $this->query = DB::table('credits');
        
         if(!empty($request->input('group_by')) {
            $this->query->select(DB::raw('count(*) as count, customers.name AS customer, SUM(total) as total, SUM(balance) AS balance'))
            $this->query->groupBy($request->input('group_by'));
        } else {
            $this->query->select('customers.name AS customer, total, number, balance, date, due_date');
        }

         $this->query->join('customers', 'customers.id', '=', 'credits.customer_id')
         ->orderBy('credits.created_at');
       
             //$this->query->where('status', '<>', 1)
            
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $credits = $list->map(
            function (Credit $credit) {
                return $this->transformCredit($credit);
            }
        )->all();

        return $credits;
    }
}
