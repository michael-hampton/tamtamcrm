<?php

namespace App\Repositories\Interfaces;

use App\Models\Account;
use App\Models\Project;
use App\Repositories\Base\BaseRepositoryInterface;
use App\Requests\SearchRequest;
use App\Search\InvoiceSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProjectRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param int $id
     * @return Project
     */
    public function findProjectById(int $id): Project;

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return InvoiceSearch|LengthAwarePaginator
     */
    public function getAll(SearchRequest $search_request, Account $account);

    /**
     * @param array $data
     * @param Project $project
     * @return Project
     */
    public function update(array $data, Project $project): Project;

    /**
     * @param array $data
     * @param Project $project
     * @return Project
     */
    public function create(array $data, Project $project): Project;
}
