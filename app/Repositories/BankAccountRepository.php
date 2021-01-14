<?php

namespace App\Repositories;

use App\Models\BankAccount;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\BankAccountRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class BankAccountRepository extends BaseRepository implements BankAccountRepositoryInterface
{
    /**
     * BankAccountRepository constructor.
     * @param BankAccount $bank_account
     */
    public function __construct(BankAccount $bank_account)
    {
        parent::__construct($bank_account);
        $this->model = $bank_account;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param array $data
     * @param BankAccount $bank_account
     * @return BankAccount
     */
    public function save(array $data, BankAccount $bank_account): BankAccount
    {
        $bank_account->fill($data);

        if (isset($data['password']) && !empty($data['password'])) {
            $bank_account->password = Hash::make($data['password']);
        }

//        if (isset($data['username']) && !empty($data['username'])) {
//            $bank_account->username = Hash::make($data['username']);
//        }

        $bank_account->save();
        return $bank_account;
    }

    /**
     * @param int $id
     * @return BankAccount
     */
    public function findBankAccountById(int $id): BankAccount
    {
        return $this->findOneOrFail($id);
    }
}
