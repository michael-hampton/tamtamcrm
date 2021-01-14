<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Deal;
use App\Models\User;

class DealFactory
{
    /**
     * @param User $user
     * @param Account $account
     * @return Deal
     */
    public static function create(User $user, Account $account): Deal
    {
        $deal = new Deal;
        $deal->source_type = 1;
        $deal->user_id = $user->id;
        $deal->account_id = $account->id;

        return $deal;
    }
}
