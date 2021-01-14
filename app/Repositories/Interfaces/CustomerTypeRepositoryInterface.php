<?php

namespace App\Repositories\Interfaces;

use App\Models\CustomerType;

interface CustomerTypeRepositoryInterface
{
    public function getAll();

    /**
     *
     * @param int $id
     * @return CustomerType
     * @return CustomerType
     */
    public function findCustomerTypeById(int $id): CustomerType;
}
