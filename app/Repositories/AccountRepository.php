<?php

namespace App\Repositories;

use App\Models\Account;
use App\Repositories\Base\BaseRepository;

/**
 * AccountRepository
 */
class AccountRepository extends BaseRepository
{
    /**
     * AccountRepository constructor.
     * @param Account $account
     */
    public function __construct(Account $account)
    {
        parent::__construct($account);
        $this->model = $account;
    }

    /**
     * Gets the class name.
     *
     * @return     string The class name.
     */
    public function getClassName()
    {
        return Account::class;
    }

    /**
     * @param int $id
     * @return Account
     */
    public function findAccountById(int $id): Account
    {
        return $this->findOneOrFail($id);
    }

    /**
     * Saves the client and its contacts
     *
     * @param array $data The data
     * @param Account $account
     * @return     Client|Company|null  Company Object
     */
    public function save(array $data, Account $account): ?Account
    {
        $account->fill($data);
        $account->save();
        return $account;
    }
}
