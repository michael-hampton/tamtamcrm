<?php

namespace App\Search;

use App\Models\Account;
use App\Models\TaxRate;
use App\Repositories\TaxRateRepository;
use App\Requests\SearchRequest;
use App\Transformations\TaxRateTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

class TaxRateSearch extends BaseSearch
{
    use TaxRateTransformable;

    private TaxRateRepository $tax_rate_repo;

    private TaxRate $model;

    /**
     * CompanySearch constructor.
     * @param TaxRateRepository $tax_rate_repo
     */
    public function __construct(TaxRateRepository $tax_rate_repo)
    {
        $this->tax_rate_repo = $tax_rate_repo;
        $this->model = $tax_rate_repo->getModel();
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

        if ($request->filled('search_term')) {
            $this->searchFilter($request->search_term);
        }

        if ($request->has('status')) {
            $this->status('tax_rates', $request->status);
        }

        if ($request->input('start_date') <> '' && $request->input('end_date') <> '') {
            $this->filterDates($request);
        }

        $this->addAccount($account);

        $this->orderBy($orderBy, $orderDir);

        $tax_rates = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->tax_rate_repo->paginateArrayResults($tax_rates, $recordsPerPage);
            return $paginatedResults;
        }

        return $tax_rates;
    }

    public function searchFilter(string $filter = ''): bool
    {
        if (strlen($filter) == 0) {
            return false;
        }

        $this->query->where(
            function ($query) use ($filter) {
                $query->where('name', 'like', '%' . $filter . '%');
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
        $companies = $list->map(
            function (TaxRate $tax_rate) {
                return $this->transformTaxRate($tax_rate);
            }
        )->all();

        return $companies;
    }

}
