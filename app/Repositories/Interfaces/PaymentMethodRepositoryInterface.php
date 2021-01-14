<?php

namespace App\Repositories\Interfaces;

use Illuminate\Support\Collection;

interface PaymentMethodRepositoryInterface
{

    /**
     *
     * @param string[] $columns
     * @param string $orderBy
     * @param string $sortBy
     * @return Collection
     */
    public function listPaymentMethods(
        $columns = array('*'),
        string $orderBy = 'id',
        string $sortBy = 'asc'
    ): Collection;

}
