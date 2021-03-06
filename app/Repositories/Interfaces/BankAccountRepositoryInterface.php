<?php

namespace App\Repositories\Interfaces;

use App\Models\BankAccount;
use App\Repositories\Base\BaseRepositoryInterface;

interface BankAccountRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param array $data
     * @param BankAccount $bank_account
     * @return BankAccount
     */
    public function create(array $data, BankAccount $bank_account): BankAccount;

    /**
     * @param array $data
     * @param BankAccount $bank_account
     * @return BankAccount
     */
    public function update(array $data, BankAccount $bank_account): BankAccount;

    /**
     * @param int $id
     * @return BankAccount
     */
    public function findBankAccountById(int $id): BankAccount;
}
