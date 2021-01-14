<?php

namespace App\Repositories\Interfaces;

use App\Models\Bank;
use App\Repositories\Base\BaseRepositoryInterface;

interface BankRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param array $data
     * @param Bank $bank
     * @return Bank
     */
    public function save(array $data, Bank $bank): Bank;

    /**
     * @param int $id
     * @return Bank
     */
    public function findBankById(int $id): Bank;
}
