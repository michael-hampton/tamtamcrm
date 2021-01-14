<?php

namespace App\Repositories;

use App\Events\Company\CompanyWasCreated;
use App\Events\Company\CompanyWasUpdated;
use App\Models\Account;
use App\Models\Company;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use App\Requests\SearchRequest;
use App\Search\CompanySearch;
use Exception;
use Illuminate\Support\Collection as Support;

class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{

    private $contact_repo;

    /**
     * CompanyRepository constructor.
     *
     * @param Company $company
     * @param CompanyContactRepository $contact_repo
     */
    public function __construct(Company $company, CompanyContactRepository $contact_repo)
    {
        parent::__construct($company);
        $this->model = $company;
        $this->contact_repo = $contact_repo;
    }

    /**
     * @param int $id
     * @return Company
     */
    public function findCompanyById(int $id): Company
    {
        return $this->findOneOrFail($id);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function deleteCompany(): bool
    {
        return $this->delete();
    }

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return Support
     */
    public function getAll(SearchRequest $search_request, Account $account)
    {
        return (new CompanySearch($this))->filter($search_request, $account);
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param array $data
     * @param Company $company
     * @return Company|null
     */
    public function save(array $data, Company $company): ?Company
    {
        $is_add = empty($company->id);

        $company->fill($data);
        $company->setNumber();
        $company->save();

        if ($is_add) {
            event(new CompanyWasCreated($company));
        } else {
            event(new CompanyWasUpdated($company));
        }

        return $company->fresh();
    }
}
