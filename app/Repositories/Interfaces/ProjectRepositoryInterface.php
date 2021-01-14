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
     * @param $data
     * @param Project $invoice
     * @return Project|null
     */
    public function save($data, Project $invoice): ?Project;

    /**
     * @return bool
     */
    public function deleteProject(): bool;

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return InvoiceSearch|LengthAwarePaginator
     */
    public function getAll(SearchRequest $search_request, Account $account);

    /**
     * @param string[] $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return Collection
     */
    public function listProjects($columns = array('*'), string $orderBy = 'id', string $sortBy = 'asc'): Collection;
}
