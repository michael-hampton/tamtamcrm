<?php

namespace App\Transformations;

use App\Models\Bank;

trait BankTransformable
{

    /**
     * @param Bank $bank
     * @return array
     */
    protected function transformBank(Bank $bank)
    {
        return [
            'id'   => (int)$bank->id,
            'name' => $bank->name
        ];
    }

}
