<?php

namespace App\Repositories;

use App\Models\Bank;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Interfaces\BankRepositoryInterface;

class BankRepository extends BaseRepository implements BankRepositoryInterface
{
    /**
     * BankRepository constructor.
     * @param Bank $bank
     */
    public function __construct(Bank $bank)
    {
        parent::__construct($bank);
        $this->model = $bank;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param array $data
     * @param Bank $bank
     * @return Bank
     */
    public function save(array $data, Bank $bank): Bank
    {
        $bank->fill($data);
        $bank->save();
        return $bank;
    }

    /**
     * @param int $id
     * @return Bank
     */
    public function findBankById(int $id): Bank
    {
        return $this->findOneOrFail($id);
    }
}
