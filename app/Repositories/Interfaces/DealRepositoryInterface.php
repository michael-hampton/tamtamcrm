<?php

namespace App\Repositories\Interfaces;

use App\Models\Account;
use App\Models\Deal;
use App\Models\User;
use App\Repositories\Base\BaseRepositoryInterface;
use App\Requests\SearchRequest;
use Illuminate\Support\Collection as Support;

interface DealRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param int $id
     * @return Deal
     */
    public function findDealById(int $id): Deal;

    /**
     * @param array $data
     * @param Deal $deal
     * @return mixed
     */
    public function createDeal(array $data, Deal $deal): ?Deal;

    /**
     * @param array $data
     * @param Deal $deal
     * @return mixed
     */
    public function updateDeal(array $data, Deal $deal): ?Deal;

    /**
     * @param $data
     * @param Deal $task
     * @return Deal|null
     */
    public function save($data, Deal $task): ?Deal;

    /**
     *
     */
    public function deleteDeal(): bool;

    public function getAll(SearchRequest $search_request, Account $account);

    /**
     *
     * @param type $limit
     * @param User|null $objUser
     * @return Support
     */
    public function getDeals($limit = null, User $objUser = null): Support;
}
