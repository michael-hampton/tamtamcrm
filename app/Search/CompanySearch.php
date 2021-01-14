<?php

namespace App\Search;

use App\Models\Account;
use App\Models\Company;
use App\Repositories\CompanyRepository;
use App\Requests\SearchRequest;
use App\Transformations\CompanyTransformable;
use Illuminate\Pagination\LengthAwarePaginator;

class CompanySearch extends BaseSearch
{
    use CompanyTransformable;

    private $companyRepository;

    private Company $model;

    /**
     * CompanySearch constructor.
     * @param CompanyRepository $companyRepository
     */
    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
        $this->model = $companyRepository->getModel();
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

        $this->query = $this->model->select('companies.*')
                                   ->leftJoin('company_contacts', 'company_contacts.company_id', '=', 'companies.id');

        if ($request->has('status')) {
            $this->status('companies', $request->status);
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

        $this->addAccount($account, 'companies');

        $this->orderBy($orderBy, $orderDir);

        $this->query->groupBy('companies.id');

        $companies = $this->transformList();

        if ($recordsPerPage > 0) {
            $paginatedResults = $this->companyRepository->paginateArrayResults($companies, $recordsPerPage);
            return $paginatedResults;
        }

        return $companies;
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
                $query->where('companies.name', 'like', '%' . $filter . '%')
                    //->orWhere('companies.number', 'like', '%'.$filter.'%')
                      ->orWhere('company_contacts.first_name', 'like', '%' . $filter . '%')
                      ->orWhere('company_contacts.last_name', 'like', '%' . $filter . '%')
                      ->orWhere('company_contacts.email', 'like', '%' . $filter . '%')
                      ->orWhere('companies.custom_value1', 'like', '%' . $filter . '%')
                      ->orWhere('companies.custom_value2', 'like', '%' . $filter . '%')
                      ->orWhere('companies.custom_value3', 'like', '%' . $filter . '%')
                      ->orWhere('companies.custom_value4', 'like', '%' . $filter . '%');
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
            function (Company $company) {
                return $this->transformCompany($company);
            }
        )->all();

        return $companies;
    }

}
