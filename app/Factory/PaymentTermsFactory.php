<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\PaymentTerms;
use App\Models\User;

class PaymentTermsFactory
{
    /**
     * @param Account $account
     * @param User $user
     * @return PaymentTerms
     */
    public static function create(Account $account, User $user): PaymentTerms
    {
        $gs = new PaymentTerms;
        $gs->name = '';
        $gs->account_id = $account->id;
        $gs->user_id = $user->id;

        return $gs;
    }
}
