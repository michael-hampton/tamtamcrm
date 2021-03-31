<?php

namespace App\Factory;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Plan;
use App\Models\Project;
use App\Models\User;

class PlanFactory
{
    /**
     * @param User $user
     * @param Account $account
     * @return Plan
     */
    public static function create(User $user, Account $account): Plan
    {
        $plan = new Plan();

        $plan->account_id = $account->id;
        $plan->user_id = $user->id;

        return $plan;
    }
}
