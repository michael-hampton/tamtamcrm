<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Promocode;
use App\Repositories\PromocodeRepository;
use App\Requests\SearchRequest;
use App\Transformations\PromocodeTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

class PromocodeSearch extends BaseSearch
{
    use PromocodeTransformable;

    private PromocodeRepository $promocode_repo;

    private Promocode $model;

    public function __construct(PromocodeRepository $promocode_repo)
    {
        $this->promocode_repo = $promocode_repo;
        $this->model = $promocode_repo->getModel();
    }

    /**
     * @param SearchRequest $request
     * @param Account $account
     * @return LengthAwarePaginator|mixed
     */
    public function filter(SearchRequest $request, Account $account)
    {
        $recordsPerPage = !$request->per_page ? 0 : $request->per_page;
        $orderBy = !$request->column ? 'expires_at' : $request->column;
        $orderDir = !$request->order ? 'asc' : $request->order;

        $this->query = $this->model->select('*');

        if ($request->filled('search_term')) {
            $this->searchFilter($request->search_term);
        }

        if ($request->has('status')) {
            $this->status($request->status);
        }

//        if ($request->filled('customer_id')) {
//            $this->query->whereCustomerId($request->customer_id);
//        }

//        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
//            $this->filterDates($request);
//        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $companies = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->promocode_repo->paginateArrayResults($companies, $recordsPerPage);
            return $paginatedResults;
        }

        return $companies;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where('code', 'like', '%' . $filter . '%');

        return true;
    }

    /**
     * @return mixed
     */
    private function transformList()
    {
        $list = $this->query->get();
        $promocodes = $list->map(
            function (Promocode $promocode) {
                return $this->transformPromocodes($promocode);
            }
        )->all();

        return $promocodes;
    }

}
