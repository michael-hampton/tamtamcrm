<?php
/**
 * Created by PhpStorm.
 * User: michael.hampton
 * Date: 15/11/2019
 * Time: 21:17
 */

namespace App\Repositories\Interfaces;


use App\Models\Account;
use App\Models\Credit;
use App\Requests\SearchRequest;

interface CreditRepositoryInterface
{
    /**
     * @param int $id
     * @return Credit
     */
    public function findCreditById(int $id): Credit;

    /**
     * @param SearchRequest $search_request
     * @param Account $account
     * @return mixed
     */
    public function getAll(SearchRequest $search_request, Account $account);

    /**
     * @param array $data
     * @param Credit $credit
     * @return Credit|null
     */
    public function createCreditNote(array $data, Credit $credit): ?Credit;

    /**
     * @param array $data
     * @param Credit $credit
     * @return Credit|null
     */
    public function updateCreditNote(array $data, Credit $credit): ?Credit;

    /**
     * @param array $data
     * @param Credit $credit
     * @return Credit|null
     */
    public function save(array $data, Credit $credit): ?Credit;

}