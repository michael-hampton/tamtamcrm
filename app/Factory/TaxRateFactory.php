<?php

namespace App\Factory;

use App\Models\TaxRate;

class TaxRateFactory
{
    public static function create(int $account_id, int $user_id): TaxRate
    {
        $tax_rate = new TaxRate;
        $tax_rate->name = '';
        $tax_rate->rate = '';
        $tax_rate->account_id = $account_id;
        $tax_rate->user_id = $user_id;
        return $tax_rate;
    }

}
